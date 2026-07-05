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

/* Homepage Instagram reels section: one larger self-hosted <video> at a
   time (see instagram_section_v4.html), auto-advancing to the next reel
   when the current one ends, only while the player is scrolled into
   view. No audio control - everything stays muted. */
( function () {
	var player = document.querySelector( '.tt-ig-player' );
	if ( ! player ) {
		return;
	}
	var videos = Array.prototype.slice.call( player.querySelectorAll( '.tt-ig-video' ) );
	var dots = Array.prototype.slice.call( player.querySelectorAll( '.tt-ig-dot' ) );
	var current = 0;
	var inView = false;

	function showIndex( i ) {
		videos[ current ].classList.remove( 'tt-ig-active' );
		videos[ current ].pause();
		if ( dots[ current ] ) {
			dots[ current ].classList.remove( 'tt-ig-dot--active' );
		}
		current = i;
		var next = videos[ current ];
		next.classList.add( 'tt-ig-active' );
		if ( dots[ current ] ) {
			dots[ current ].classList.add( 'tt-ig-dot--active' );
		}
		if ( next.preload === 'none' ) {
			next.preload = 'auto';
		}
		if ( inView ) {
			var p = next.play();
			if ( p && p.catch ) {
				p.catch( function () {} );
			}
		}
	}

	videos.forEach( function ( video, i ) {
		video.addEventListener( 'ended', function () {
			showIndex( ( i + 1 ) % videos.length );
		} );
	} );
	dots.forEach( function ( dot, i ) {
		dot.addEventListener( 'click', function () {
			showIndex( i );
		} );
	} );

	if ( 'IntersectionObserver' in window ) {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				inView = entry.isIntersecting;
				var active = videos[ current ];
				if ( inView ) {
					var p = active.play();
					if ( p && p.catch ) {
						p.catch( function () {} );
					}
				} else {
					active.pause();
				}
			} );
		}, { threshold: 0.6 } );
		io.observe( player );
	} else {
		inView = true;
		videos[ 0 ].play();
	}
} )();

/* Homepage "why subscribe" benefits (Great variety, Snacks change
   monthly, Convenient snack solutions, REAL food, Smaller ethical
   brands, Predominantly Vegan friendly): six items across three
   separate Elementor sections, stacking into one long vertical scroll
   on mobile. Moved into a single horizontal-scroll strip instead,
   matching the reel player above it - done by relocating the existing
   columns in the DOM rather than rebuilding the Elementor content, so
   the six items stay exactly where an editor would expect to find and
   edit them in Elementor itself. */
( function () {
	var sectionIds = [ '17e28054', '74d434b8', '380fdfd4' ];
	var sections = sectionIds
		.map( function ( id ) {
			return document.querySelector( '.elementor-element-' + id );
		} )
		.filter( Boolean );
	if ( sections.length !== sectionIds.length ) {
		return;
	}
	var wrap = document.createElement( 'div' );
	wrap.className = 'tt-benefits-scroll';
	sections[ 0 ].parentNode.insertBefore( wrap, sections[ 0 ] );
	sections.forEach( function ( section ) {
		var columns = section.querySelectorAll( ':scope > .elementor-container > .elementor-column' );
		columns.forEach( function ( col ) {
			col.classList.add( 'tt-benefit-card' );
			wrap.appendChild( col );
		} );
		section.remove();
	} );
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
