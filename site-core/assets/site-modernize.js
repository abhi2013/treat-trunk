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

/* Homepage Instagram reels section: load Instagram's embed script only
   once the section scrolls near view. A <script> tag placed inside the
   section's own HTML (as rendered by the page builder) never executes -
   scripts inserted via innerHTML are inert by design in every browser -
   so this has to live here instead, in a normally-enqueued file. */
( function () {
	var section = document.querySelector( '.tt-ig-section' );
	if ( ! section ) {
		return;
	}
	var loaded = false;
	function loadEmbed() {
		if ( loaded ) {
			return;
		}
		loaded = true;
		if ( window.instgrm ) {
			window.instgrm.Embeds.process();
			return;
		}
		var s = document.createElement( 'script' );
		s.async = true;
		s.src = 'https://www.instagram.com/embed.js';
		document.body.appendChild( s );
	}
	if ( 'IntersectionObserver' in window ) {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					loadEmbed();
					io.disconnect();
				}
			} );
		}, { rootMargin: '400px' } );
		io.observe( section );
	} else {
		loadEmbed();
	}
} )();
