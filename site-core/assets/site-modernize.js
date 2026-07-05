(function () {
	var sections = document.querySelectorAll( '.elementor-top-section' );
	if ( ! sections.length ) {
		return;
	}
	sections.forEach( function ( el ) {
		el.classList.add( 'tt-reveal' );
	} );
	var io = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( entry.isIntersecting ) {
				entry.target.classList.add( 'tt-in' );
				io.unobserve( entry.target );
			}
		} );
	}, { threshold: 0.12 } );
	sections.forEach( function ( el ) {
		io.observe( el );
	} );
	/* Safety net: on some pages a section's initial layout height is 0 (or
	   otherwise doesn't register as intersecting) before async content -
	   images, archive grids - finishes loading, and the observer never
	   fires again since it only reacts to threshold crossings. Force
	   everything visible after a short delay so content can never be
	   silently stuck invisible. */
	setTimeout( function () {
		sections.forEach( function ( el ) {
			el.classList.add( 'tt-in' );
		} );
		io.disconnect();
	}, 2500 );
} )();

/* Product-options pill toggles: highlight the checked radio's label via a
   plain class rather than relying only on :has(), since that selector
   isn't supported in older browsers still in real-world use. */
( function () {
	var tables = document.querySelectorAll( 'table.extra-options' );
	if ( ! tables.length ) {
		return;
	}

	/* "Is This a Gift?" pills read for themselves now instead of bare
	   Yes/No, so the question label above them (hidden via CSS) isn't
	   needed to understand what picking either one means. */
	function relabel( input, text ) {
		if ( ! input ) {
			return;
		}
		var label = input.closest( 'label' );
		if ( ! label ) {
			return;
		}
		for ( var i = label.childNodes.length - 1; i >= 0; i-- ) {
			if ( label.childNodes[ i ].nodeType === 3 ) {
				label.childNodes[ i ].textContent = ' ' + text;
				break;
			}
		}
	}

	function syncPill( input ) {
		var name = input.name;
		document.querySelectorAll( 'input[name="' + name + '"]' ).forEach( function ( sibling ) {
			var label = sibling.closest( 'label' );
			if ( label ) {
				label.classList.toggle( 'tt-pill-checked', sibling.checked );
			}
		} );
	}

	/* querySelectorAll, not querySelector: this page has two separate
	   tables (gift options, and a second "personalisation" one holding
	   who-are-the-snacks-for/name/allergy fields) - only ever handling
	   the first meant the second table's pills and conditional fields
	   were never wired up at all. */
	tables.forEach( function ( table ) {
		relabel( table.querySelector( '#option_is_gift_1' ), 'Yes, it’s a gift' );
		relabel( table.querySelector( '#option_is_gift_No' ), 'No, just for me' );

		table.querySelectorAll( 'input[type="radio"]' ).forEach( function ( input ) {
			syncPill( input );
			input.addEventListener( 'change', function () {
				syncPill( input );
			} );
		} );

		/* Most boxes go to adults, and leaving this unset blocked
		   add-to-basket until a customer picked one - default it, still
		   changeable with one tap. */
		var whoForChecked = table.querySelector( 'input[name="who_is_the_box_for"]:checked' );
		var whoForAdults = table.querySelector( 'input[name="who_is_the_box_for"][value="Adults"]' );
		if ( whoForAdults && ! whoForChecked ) {
			whoForAdults.checked = true;
			syncPill( whoForAdults );
		}

		/* The plugin's own conditional show/hide (e.g. gift_to/gift_from
		   only when "Is this a gift?" is Yes) never ran on initial page
		   load - only on a real user interaction - so every conditional
		   field showed regardless of the actual (pre-checked) default
		   selection. Dispatching a synthetic "change" event didn't
		   trigger the plugin's own handler (it likely binds some other
		   way), so this reads each row's own data-rules attribute and
		   evaluates it directly instead. */
		function fieldValue( name ) {
			var checkedRadio = table.querySelector( 'input[type="radio"][name="' + name + '"]:checked' );
			if ( checkedRadio ) {
				return checkedRadio.value;
			}
			var checkbox = table.querySelector( 'input[type="checkbox"][name="' + name + '"]' );
			if ( checkbox ) {
				return checkbox.checked ? '1' : '';
			}
			var other = table.querySelector( '[name="' + name + '"]' );
			return other ? other.value : null;
		}
		function flattenConditions( node, out ) {
			if ( Array.isArray( node ) ) {
				node.forEach( function ( n ) {
					flattenConditions( n, out );
				} );
			} else if ( node && typeof node === 'object' && node.operator ) {
				out.push( node );
			}
			return out;
		}
		function evaluateRow( row ) {
			var rulesAttr = row.getAttribute( 'data-rules' );
			var action = row.getAttribute( 'data-rules-action' );
			if ( ! rulesAttr ) {
				return;
			}
			var rules;
			try {
				rules = JSON.parse( rulesAttr );
			} catch ( e ) {
				return;
			}
			var conditions = flattenConditions( rules, [] );
			var allMatch = conditions.every( function ( cond ) {
				var name = cond.operand && cond.operand[ 0 ];
				if ( ! name ) {
					return true;
				}
				var actual = fieldValue( name );
				if ( cond.operator === 'value_eq' ) {
					return actual === cond.value;
				}
				return true;
			} );
			var shouldShow = action === 'hide' ? ! allMatch : allMatch;
			row.style.display = shouldShow ? '' : 'none';
		}
		function evaluateAllRows() {
			table.querySelectorAll( 'tr[data-rules]' ).forEach( evaluateRow );
		}
		table.addEventListener( 'change', evaluateAllRows );
		table.addEventListener( 'input', evaluateAllRows );
		evaluateAllRows();
	} );
} )();

/* WooCommerce's own variation dropdown (e.g. "Size"): replace with pill
   buttons for a small option set, same treatment as the gift/who-for
   pills above. The <select> itself stays in the DOM, visually hidden -
   WooCommerce's price/stock update logic listens for its "change" event,
   so pills just drive that same select rather than reimplementing it. */
( function () {
	document.querySelectorAll( 'table.variations select[name^="attribute_"]' ).forEach( function ( select ) {
		var options = Array.prototype.filter.call( select.options, function ( o ) {
			return o.value !== '';
		} );
		if ( options.length < 2 || options.length > 6 ) {
			return;
		}
		var wrap = document.createElement( 'div' );
		wrap.className = 'tt-variation-pills';

		function syncFromSelect() {
			Array.prototype.forEach.call( wrap.children, function ( pill, i ) {
				pill.classList.toggle( 'tt-pill-checked', options[ i ].value === select.value );
			} );
		}

		options.forEach( function ( opt ) {
			var pill = document.createElement( 'button' );
			pill.type = 'button';
			pill.className = 'tt-variation-pill';
			pill.textContent = opt.textContent;
			pill.addEventListener( 'click', function () {
				select.value = opt.value;
				select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
				syncFromSelect();
			} );
			wrap.appendChild( pill );
		} );

		select.classList.add( 'tt-pillified' );
		select.insertAdjacentElement( 'afterend', wrap );
		select.addEventListener( 'change', syncFromSelect );

		/* Default to the "Standard" size where one exists, rather than
		   leaving the box unselected - one tap still changes it. */
		if ( ! select.value ) {
			var standardOpt = options.find( function ( o ) {
				return /standard/i.test( o.textContent );
			} );
			if ( standardOpt ) {
				select.value = standardOpt.value;
				select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}
		}
		syncFromSelect();
	} );
} )();

/* Homepage "See what's inside" carousel: the 4 reels plus 6 real past-
   box photos (hardcoded in instagram_section_v7.html - the same 6
   images that used to live in a separate gallery widget further down
   the page, now folded in here instead of duplicated) are the slides,
   navigated with shared prev/next arrows. The "why subscribe" benefit
   text (Great variety, Convenient snack solutions...) lives separately
   as an always-visible vertical list next to the carousel - relocated
   here from three separate Elementor sections that otherwise stack
   into one long vertical scroll on mobile. Relocating the existing
   columns (rather than rebuilding that content) keeps them exactly
   where an editor would expect to find and edit them in Elementor. */
( function () {
	var player = document.querySelector( '.tt-ig-player' );
	var slidesWrap = player && player.querySelector( '.tt-ig-slides' );
	var listWrap = document.querySelector( '.tt-benefit-list' );
	if ( ! player || ! slidesWrap || ! listWrap ) {
		return;
	}

	var sectionIds = [ '17e28054', '74d434b8', '380fdfd4' ];
	var benefitSections = sectionIds
		.map( function ( id ) {
			return document.querySelector( '.elementor-element-' + id );
		} )
		.filter( Boolean );
	benefitSections.forEach( function ( section ) {
		var columns = section.querySelectorAll( ':scope > .elementor-container > .elementor-column' );
		columns.forEach( function ( col ) {
			col.classList.add( 'tt-benefit-card' );
			listWrap.appendChild( col );
		} );
		section.remove();
	} );

	/* the old standalone gallery of the same 6 past-box photos - now
	   redundant since they are slides in the carousel above. */
	var oldGallery = document.querySelector( '.elementor-element-b65a0e6' );
	if ( oldGallery ) {
		oldGallery.remove();
	}

	var slides = Array.prototype.slice.call( slidesWrap.querySelectorAll( '.tt-ig-slide' ) );
	var counterCurrent = player.querySelector( '.tt-ig-counter-current' );
	var counterTotal = player.querySelector( '.tt-ig-counter-total' );
	var prevBtn = player.querySelector( '.tt-ig-arrow--prev' );
	var nextBtn = player.querySelector( '.tt-ig-arrow--next' );
	var current = 0;
	var inView = false;

	if ( counterTotal ) {
		counterTotal.textContent = slides.length;
	}

	function pauseVideoIn( slide ) {
		var v = slide.querySelector( 'video' );
		if ( v ) {
			v.pause();
		}
	}
	function playVideoIn( slide ) {
		var v = slide.querySelector( 'video' );
		if ( ! v ) {
			return;
		}
		if ( v.preload === 'none' ) {
			v.preload = 'auto';
		}
		var p = v.play();
		if ( p && p.catch ) {
			p.catch( function () {} );
		}
	}

	function showIndex( i ) {
		pauseVideoIn( slides[ current ] );
		slides[ current ].classList.remove( 'tt-ig-slide--active' );
		current = ( i + slides.length ) % slides.length;
		slides[ current ].classList.add( 'tt-ig-slide--active' );
		if ( counterCurrent ) {
			counterCurrent.textContent = current + 1;
		}
		if ( inView ) {
			playVideoIn( slides[ current ] );
		}
	}

	slides.forEach( function ( slide, i ) {
		var v = slide.querySelector( 'video' );
		if ( v ) {
			v.addEventListener( 'ended', function () {
				showIndex( i + 1 );
			} );
		}
	} );
	if ( prevBtn ) {
		prevBtn.addEventListener( 'click', function () {
			showIndex( current - 1 );
		} );
	}
	if ( nextBtn ) {
		nextBtn.addEventListener( 'click', function () {
			showIndex( current + 1 );
		} );
	}

	if ( 'IntersectionObserver' in window ) {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				inView = entry.isIntersecting;
				if ( inView ) {
					playVideoIn( slides[ current ] );
				} else {
					pauseVideoIn( slides[ current ] );
				}
			} );
		}, { threshold: 0.6 } );
		io.observe( player );
	} else {
		inView = true;
		playVideoIn( slides[ 0 ] );
	}
} )();

/* Testimonial cards: add a small initial-letter avatar before each
   reviewer name, since the widget's own settings have per-review
   photos that never actually render (a pre-existing Elementor quirk,
   not something this project's CSS/JS controls). */
( function () {
	document.querySelectorAll( '.elementor-testimonial__cite' ).forEach( function ( cite ) {
		var nameEl = cite.querySelector( '.elementor-testimonial__name' );
		if ( ! nameEl || ! nameEl.textContent.trim() ) {
			return;
		}
		var avatar = document.createElement( 'div' );
		avatar.className = 'tt-review-avatar';
		avatar.textContent = nameEl.textContent.trim().charAt( 0 ).toUpperCase();
		cite.parentNode.insertBefore( avatar, cite );
	} );
} )();

/* Testimonial cards: all the same height with the quote clamped to 6
   lines (see CSS), plus a "Read more" for reviews long enough to be
   cut off. Opens the full review in a shared modal rather than
   expanding the card in place, which pushed every card below it down
   the page each time one was opened. */
( function () {
	var texts = Array.prototype.slice.call( document.querySelectorAll( '.elementor-testimonial__text' ) );
	var overflowing = texts.filter( function ( text ) {
		/* swiper keeps non-active slides at zero size until they are
		   scrolled to, so scrollHeight/clientHeight report as equal
		   (both effectively 0) for any review that was not the
		   currently-active slide when this ran - which is why some
		   long reviews got no Read more at all. Fall back to a plain
		   character-count guess for anything reporting no real size. */
		if ( text.clientHeight > 0 ) {
			return text.scrollHeight > text.clientHeight + 2;
		}
		return text.textContent.trim().length > 260;
	} );
	if ( ! overflowing.length ) {
		return;
	}

	var overlay = document.createElement( 'div' );
	overlay.className = 'tt-review-modal-overlay';
	overlay.hidden = true;
	overlay.innerHTML =
		'<div class="tt-review-modal" role="dialog" aria-modal="true">' +
			'<button type="button" class="tt-review-modal-close" aria-label="Close">✕</button>' +
			'<span class="tt-review-modal-quote">“</span>' +
			'<div class="tt-review-modal-text"></div>' +
			'<div class="tt-review-modal-footer"></div>' +
		'</div>';
	document.body.appendChild( overlay );
	var modalText = overlay.querySelector( '.tt-review-modal-text' );
	var modalFooter = overlay.querySelector( '.tt-review-modal-footer' );
	var closeBtn = overlay.querySelector( '.tt-review-modal-close' );

	function closeModal() {
		overlay.hidden = true;
	}
	function openModal( text ) {
		modalText.textContent = text.textContent.trim();
		var footer = text.closest( '.elementor-testimonial' ).querySelector( '.elementor-testimonial__footer' );
		modalFooter.innerHTML = '';
		if ( footer ) {
			modalFooter.appendChild( footer.cloneNode( true ) );
		}
		overlay.hidden = false;
	}
	closeBtn.addEventListener( 'click', closeModal );
	overlay.addEventListener( 'click', function ( e ) {
		if ( e.target === overlay ) {
			closeModal();
		}
	} );
	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' && ! overlay.hidden ) {
			closeModal();
		}
	} );

	overflowing.forEach( function ( text ) {
		var btn = document.createElement( 'button' );
		btn.type = 'button';
		btn.className = 'tt-review-more';
		btn.textContent = 'Read more';
		btn.addEventListener( 'click', function () {
			openModal( text );
		} );
		text.parentNode.insertBefore( btn, text.nextSibling );
	} );
} )();
