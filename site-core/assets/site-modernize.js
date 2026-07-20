(function () {
	/* Popups (elementor-location-popup) don't scroll into view - they're
	   triggered directly by a click - so scroll-reveal doesn't apply and
	   actively breaks them: this script only ever queries the DOM once,
	   at page load, but popup content is injected later, the first time
	   a popup opens. Any section whose HTML already had "tt-reveal"
	   baked in (Elementor's own caching had captured an earlier render
	   of this file) got the opacity:0 starting state with nothing left
	   to ever add "tt-in" - permanently invisible. Excluded here; the
	   matching CSS rule in site-modernize.css is the real backstop
	   (covers the class already being present in cached popup HTML
	   regardless of what this query does). */
	var sections = Array.prototype.filter.call(
		document.querySelectorAll( '.elementor-top-section' ),
		function ( el ) {
			return ! el.closest( '.elementor-location-popup' );
		}
	);
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
		var groupId = 'tt-pg-' + Math.random().toString( 36 ).slice( 2 );
		var wrap = document.createElement( 'div' );
		wrap.className = 'tt-variation-pills';
		wrap.setAttribute( 'data-pill-group', groupId );

		function syncFromSelect() {
			/* Query live rather than close over wrap.children - a later
			   script (the welcome-box Option 1/2 grouping) moves these
			   pills into new containers elsewhere in the DOM, and a stale
			   reference to the now-empty original wrap would silently stop
			   updating the checked/highlighted state. data-pill-group ties
			   a pill back to this select regardless of where it now lives. */
			var pills = document.querySelectorAll( '[data-pill-group="' + groupId + '"] .tt-variation-pill' );
			Array.prototype.forEach.call( pills, function ( pill, i ) {
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
		/* Inserted after the whole <table class="variations">, not just
		   after the select - custom.css hides that table entirely
		   (position:absolute + clip:rect(0,0,0,0), a standard visually-
		   hidden pattern) once its pills exist, on the assumption pills
		   always get moved out of it. That's only true for welcome-box
		   subscription products (see the relocate() IIFE below); on every
		   other variable product, the pills were staying inside the same
		   table cell as the select and getting clipped into invisibility
		   right along with it - a real "select works, but nothing visible
		   to click" bug on every regular variation dropdown site-wide. */
		var table = select.closest( 'table.variations' );
		( table || select ).insertAdjacentElement( 'afterend', wrap );
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

/* Welcome-box subscription products only: the Mini/Standard/No Welcome
   Box pills built above sit in one row below both Option 1/Option 2
   boxes, disconnected from either - move each pill into the option box
   it actually belongs to (Mini/Standard -> Option 1, No Welcome Box ->
   Option 2) so the control that picks a plan sits next to the plan it
   picks, instead of requiring a scroll back up to match one to the
   other. A real DOM move (not a clone), so the pill's existing click
   listener from the block above comes with it unchanged. */
( function () {
	function relocate() {
		/* The pills wrap now lands right after table.variations (see the
		   IIFE above), not inside it - matches where it's actually
		   inserted now. */
		var pillsWrap = document.querySelector( 'table.variations + .tt-variation-pills' );
		var options = document.querySelectorAll( '.woovr-variations .option' );
		if ( ! pillsWrap || ! pillsWrap.children.length || options.length < 2 ) {
			return false;
		}
		var groupId = pillsWrap.getAttribute( 'data-pill-group' );
		var option1 = options[ 0 ];
		var option2 = options[ 1 ];
		var wrap1 = document.createElement( 'div' );
		wrap1.className = 'tt-variation-pills';
		wrap1.setAttribute( 'data-pill-group', groupId );
		option1.appendChild( wrap1 );
		var wrap2 = document.createElement( 'div' );
		wrap2.className = 'tt-variation-pills';
		wrap2.setAttribute( 'data-pill-group', groupId );
		option2.appendChild( wrap2 );

		Array.prototype.slice.call( pillsWrap.children ).forEach( function ( pill ) {
			var target = /no welcome box/i.test( pill.textContent ) ? wrap2 : wrap1;
			target.appendChild( pill );
		} );
		return true;
	}

	/* Both elements are ordinary server-rendered HTML present from initial
	   load in every manual check, but relocate() reliably found nothing
	   when run synchronously here on first page load, and reliably
	   succeeded a moment later when re-run by hand - some other script
	   on this heavily-plugin-loaded page appears to touch this markup
	   shortly after DOM ready in a way not fully isolated. Retrying for
	   up to 2s is cheap and avoids depending on winning that race. */
	if ( relocate() ) {
		return;
	}
	var attempts = 0;
	var retry = setInterval( function () {
		attempts++;
		if ( relocate() || attempts >= 20 ) {
			clearInterval( retry );
		}
	}, 100 );
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

	/* Touch swipe (mobile): swipe left -> next reel, swipe right -> prev.
	   Passive listeners so vertical page scrolling is never blocked; a move
	   only counts as a swipe when it's clearly horizontal (past a 40px
	   threshold and more horizontal than vertical), so an ordinary up/down
	   scroll that happens to start on the video doesn't flip slides. */
	var touchStartX = 0;
	var touchStartY = 0;
	var touching = false;
	slidesWrap.addEventListener( 'touchstart', function ( e ) {
		if ( ! e.touches || ! e.touches.length ) {
			return;
		}
		touchStartX = e.touches[ 0 ].clientX;
		touchStartY = e.touches[ 0 ].clientY;
		touching = true;
	}, { passive: true } );
	slidesWrap.addEventListener( 'touchend', function ( e ) {
		if ( ! touching || ! e.changedTouches || ! e.changedTouches.length ) {
			return;
		}
		touching = false;
		var dx = e.changedTouches[ 0 ].clientX - touchStartX;
		var dy = e.changedTouches[ 0 ].clientY - touchStartY;
		if ( Math.abs( dx ) > 40 && Math.abs( dx ) > Math.abs( dy ) * 1.5 ) {
			showIndex( dx < 0 ? current + 1 : current - 1 );
		}
	}, { passive: true } );

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
   reviewer name, but only when the review has no real photo set -
   some reviews in the widget have no image configured at all, and
   those rendered with no avatar whatsoever. Reviews that DO have a
   real photo (.elementor-testimonial__image) render it fine; this
   was previously added unconditionally for every card, which put a
   duplicate letter-avatar next to the real photo. */
( function () {
	document.querySelectorAll( '.elementor-testimonial__cite' ).forEach( function ( cite ) {
		var card = cite.closest( '.elementor-testimonial' );
		if ( card && card.querySelector( '.elementor-testimonial__image' ) ) {
			return;
		}
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
	/* A scrollHeight/clientHeight comparison depends on the element
	   already being laid out at real size - swiper keeps non-active
	   slides at zero size until scrolled to, so that check silently
	   passed (no Read more shown) for any review that was not the
	   active slide, or was measured before swiper finished sizing it.
	   A plain character-count guess has no such timing dependency. */
	var overflowing = texts.filter( function ( text ) {
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
			'<button type="button" class="tt-review-modal-close" aria-label="Close">' +
				'<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>' +
			'</button>' +
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
		btn.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			openModal( text );
		} );
		text.parentNode.insertBefore( btn, text.nextSibling );

		/* the whole card opens the same modal, not just the Read more
		   button - the truncated text under a card that does nothing
		   when clicked reads as broken. */
		var card = text.closest( '.elementor-testimonial' );
		if ( card ) {
			card.classList.add( 'tt-review-clickable' );
			card.addEventListener( 'click', function () {
				openModal( text );
			} );
		}
	} );
} )();

/* Welcome-box subscription products: make Option 1 / Option 2 a real
   single-open accordion. The plugin's own .active (tracks the current
   selection) and .expanded (tracks manually opening a panel to peek at
   it) classes are independent - peeking at the non-selected option
   while a different one is still selected leaves both classes present
   on different panels at once, and the CSS above (keyed off .active
   OR .expanded) shows both again, recreating the exact redundant
   double-panel view this was meant to fix. Tracking panel-open state
   ourselves in a dedicated class, exclusive across the group, so
   opening one always closes the other regardless of which is
   currently selected. */
( function () {
	var groups = document.querySelectorAll( '.woovr-variations' );
	if ( ! groups.length ) {
		return;
	}
	groups.forEach( function ( group ) {
		var options = group.querySelectorAll( ':scope > .option' );
		if ( options.length < 2 ) {
			return;
		}
		function openOnly( target ) {
			options.forEach( function ( opt ) {
				opt.classList.toggle( 'tt-panel-open', opt === target );
			} );
		}
		// Start with whichever option matches the default selection.
		var initiallyActive = group.querySelector( ':scope > .option.active' ) || options[ 0 ];
		openOnly( initiallyActive );

		options.forEach( function ( opt ) {
			var header = opt.querySelector( ':scope > .option_header' );
			if ( header ) {
				header.addEventListener( 'click', function () {
					openOnly( opt );
				} );
			}
			// Selecting a pill/radio inside a panel keeps that panel open
			// and closes any other, even if it was opened via a plain click
			// rather than the header.
			opt.addEventListener( 'click', function ( e ) {
				if ( e.target.closest( '.tt-variation-pill, .woovr-variation-radio' ) ) {
					openOnly( opt );
				}
			} );
		} );
	} );
} )();
