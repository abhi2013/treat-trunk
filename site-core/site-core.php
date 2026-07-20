<?php
/**
 * Plugin Name: Treat Trunk Site Core
 * Description: Small custom site-wide behaviors for treattrunk.co.uk that don't belong in the theme. Currently: automatic bulk-quantity pricing for corporate letterbox orders.
 * Version: 1.0.0
 * Author: Treat Trunk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manual homepage CSS optimization (inline critical CSS + preload-swap deferral)
 * was a workaround built while WP Rocket's license was nulled and its Remove
 * Unused CSS (RUCSS) SaaS could not authenticate. As of 2026-07-20 the genuine
 * WP Rocket 3.23 license is active (consumer_email sally@treattrunk.co.uk) and
 * RUCSS handles CSS delivery natively - which strips *unused* rules rather than
 * only deferring whole stylesheets. The two mechanisms conflict: rewriting a
 * <link rel=stylesheet> to rel=preload stops RUCSS from processing that handle.
 * So the manual path is now gated off. Flip this to true to instantly restore
 * the old behavior if RUCSS ever has to be turned back off.
 */
if ( ! defined( 'TT_MANUAL_CSS_OPT' ) ) {
	define( 'TT_MANUAL_CSS_OPT', false );
}

/**
 * CLS: the header logo (attachment 18837, Treat-Trunk-Logo-Sig-Logo-150.png) is
 * above the fold but was being lazy-loaded. Its lazy placeholder renders ~120px
 * tall while the loaded logo is a fixed 60x60 (set in CSS), so on load the header
 * collapses ~99px and the whole <main> jumps up - a ~0.22 layout shift flagged by
 * PageSpeed Insights. Skip lazyload for it (WP Rocket honours data-no-lazy) so it
 * loads eagerly at its final size: no shift, and the logo paints sooner. */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment ) {
	if ( isset( $attachment->ID ) && 18837 === (int) $attachment->ID ) {
		$attr['data-no-lazy'] = '1';
	}
	return $attr;
}, 10, 2 );

/**
 * Bulk pricing for the Letterbox product (ID 40245, slug "letterbox").
 *
 * Corporate buyers ordering many boxes to one address (a single WooCommerce
 * order always ships to one address, so no separate verification is needed)
 * get an automatic per-unit price based on cart quantity. No coupon code
 * required - this removes the "email us for pricing" friction for the common
 * bulk-to-one-address case.
 *
 * Tiers set as flat per-box prices (not percentages) so the two headline
 * quantities land on round totals - confirmed by user 2026-07-05:
 *   1-19 units: full price, £15.99/box
 *   20-49 units: £13.75/box  (20 boxes = £275.00)
 *   50+ units:   £13.00/box  (50 boxes = £650.00)
 */
define( 'TT_BULK_LETTERBOX_PRODUCT_ID', 40245 );

function tt_bulk_letterbox_unit_price( int $quantity, float $regular_price ): float {
	if ( $quantity >= 50 ) {
		return 13.00;
	}
	if ( $quantity >= 20 ) {
		return 13.75;
	}
	return $regular_price;
}

add_action( 'woocommerce_before_calculate_totals', function ( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {
		if ( (int) $cart_item['product_id'] !== TT_BULK_LETTERBOX_PRODUCT_ID ) {
			continue;
		}

		$product     = $cart_item['data'];
		$quantity    = (int) $cart_item['quantity'];
		$base_price  = (float) $product->get_regular_price();
		$unit_price  = tt_bulk_letterbox_unit_price( $quantity, $base_price );

		if ( $unit_price !== $base_price ) {
			$product->set_price( $unit_price );
		}
	}
}, 20, 1 );

/**
 * Show the active discount tier on the product page and in the cart so the
 * saving is visible before checkout, not a surprise at the total.
 */
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product || (int) $product->get_id() !== TT_BULK_LETTERBOX_PRODUCT_ID ) {
		return;
	}
	echo '<p class="tt-bulk-pricing-note" style="font-size:14px;color:#44543F;margin-top:8px;">'
		. 'Ordering to one address? <strong>20+ boxes: £13.75/box.</strong> <strong>50+ boxes: £13.00/box.</strong> Price updates automatically in your cart.'
		. '</p>';
}, 25 );

/**
 * Bulk pricing for the standard-size One-Off Treat Trunk box (variation ID
 * 7077, "Standard (20-25 Snacks)" of parent product 7076). Same one-click
 * bulk-to-one-address mechanism as the Letterbox tiers above, for buyers who
 * want the bigger full-size box in bulk rather than the Letterbox size.
 * Confirmed by user 2026-07-06:
 *   1-19 units: full price, £44.99/box
 *   20-49 units: £37.50/box (20 boxes = £750.00)
 *   50+ units:   £35.00/box (50 boxes = £1750.00)
 *
 * Deliberately scoped to the variation ID, not the parent product, so the
 * Mini variation (7078) is never discounted by this hook.
 */
define( 'TT_BULK_ONEOFF_VARIATION_ID', 7077 );

function tt_bulk_oneoff_unit_price( int $quantity, float $regular_price ): float {
	if ( $quantity >= 50 ) {
		return 35.00;
	}
	if ( $quantity >= 20 ) {
		return 37.50;
	}
	return $regular_price;
}

add_action( 'woocommerce_before_calculate_totals', function ( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {
		if ( (int) ( $cart_item['variation_id'] ?? 0 ) !== TT_BULK_ONEOFF_VARIATION_ID ) {
			continue;
		}

		$product    = $cart_item['data'];
		$quantity   = (int) $cart_item['quantity'];
		$base_price = (float) $product->get_regular_price();
		$unit_price = tt_bulk_oneoff_unit_price( $quantity, $base_price );

		if ( $unit_price !== $base_price ) {
			$product->set_price( $unit_price );
		}
	}
}, 20, 1 );

/**
 * Show the active discount tier on the product page. Note: on a variable
 * product page $product is the parent (7076) on initial load, so this shows
 * for the whole product rather than switching in/out per selected variation
 * - acceptable since the note calls out "the full-size box" by name.
 */
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product || (int) $product->get_id() !== 7076 ) {
		return;
	}
	echo '<p class="tt-bulk-pricing-note" style="font-size:14px;color:#44543F;margin-top:8px;">'
		. 'Ordering the full-size box to one address? <strong>20+ boxes: £37.50/box.</strong> <strong>50+ boxes: £35.00/box.</strong> Price updates automatically in your cart.'
		. '</p>';
}, 25 );

/**
 * PageSpeed: Total Recipe Generator enqueues its CSS/JS on every single page
 * via the Elementor asset pipeline, with no check for whether the page
 * actually uses its widget. On pages without a recipe card (e.g. the
 * homepage) this was Lighthouse's single biggest render-blocking resource
 * (~22KB, 98% unused). Only keep the assets on pages that actually contain
 * the widget.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_singular() ) {
		$elementor_data = get_post_meta( get_the_ID(), '_elementor_data', true );
		if ( $elementor_data && strpos( $elementor_data, 'total-recipe-generator' ) !== false ) {
			return;
		}
	}
	wp_dequeue_style( 'trg-plugin-css' );
	wp_dequeue_style( 'trg-el-fontawesome-css' );
	wp_dequeue_script( 'trg-plugin-functions' );
}, 9999 );

/**
 * PageSpeed: defer CSS that isn't needed for first paint (cookie consent
 * banner, ActiveCampaign popup form) using the standard preload-then-swap
 * async loading pattern, instead of blocking render.
 */
add_filter( 'style_loader_tag', function ( $html, $handle ) {
	if ( ! TT_MANUAL_CSS_OPT ) {
		return $html;
	}
	$defer_handles = array(
		'cookie-law-info',
		'cookie-law-info-gdpr',
		'activecampaign-form-block',
		'wxp_front_style',
		'pinterest-for-woocommerce-pins',
		'wp_mailjet_form_builder_widget-widget-front-styles',
		// NOT 'e-transitions' - defines opacity/transform states for Elementor
		// hover-animation effects (fade-in/zoom/etc). Deferring it left an
		// above-the-fold animated image invisible/mistransformed until load,
		// which regressed LCP from 3.4s to 4.6s. Confirmed via PSI re-test.
	);

	/**
	 * Homepage-only: defer the generic (non-page-specific) theme/plugin core
	 * CSS too, now that tt_critical_css_home() below inlines real critical
	 * CSS covering what's needed for above-the-fold content. Deliberately
	 * excludes the per-page Elementor-generated CSS (elementor-post-*),
	 * which contains actual layout/positioning rules unique to this page's
	 * widget arrangement, not just decorative styling - riskier to defer
	 * without perfect critical-CSS coverage of every breakpoint. Also still
	 * excludes 'e-transitions' (see above) since the critical CSS extraction
	 * confirmed it captured no .elementor-animated-item rules either.
	 */
	if ( is_front_page() ) {
		$defer_handles = array_merge( $defer_handles, array(
			'wc-blocks-integration',
			'woocommerce-layout',
			'woocommerce-smallscreen',
			'woocommerce-general',
			'hello-elementor',
			'hello-elementor-theme-style',
			'custom',
			'elementor-frontend',
			'widget-nav-menu',
			'widget-image',
			'widget-woocommerce-menu-cart',
			'widget-heading',
			'widget-icon-list',
			'widget-spacer',
			'widget-gallery',
			'elementor-gallery',
			'swiper',
			'e-swiper',
			'widget-testimonial-carousel',
			'widget-carousel-module-base',
			'elementor-gf-local-roboto',
			'elementor-gf-local-robotoslab',
			'elementor-gf-local-knewave',
			'elementor-gf-local-fredokaone',
			'elementor-gf-local-damion',
			'jetpack-forms-layout',
		) );
	}

	if ( in_array( $handle, $defer_handles, true ) ) {
		$html = str_replace(
			"rel='stylesheet'",
			"rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"",
			$html
		);
	}
	return $html;
}, 10, 2 );

/**
 * PageSpeed: inline real critical CSS on the homepage so deferring the bulk
 * of the theme/plugin stylesheets above doesn't cause a flash of unstyled
 * content. Generated with the `critical` npm package (Puppeteer-based)
 * against the live homepage at a 412x823 mobile viewport, matching
 * Lighthouse's mobile emulation - not WP Rocket's critical-CSS feature,
 * which has been failing authentication against WP Rocket's own SaaS
 * build service since at least 2026-06-03 (open support ticket).
 * Homepage-only for now; would need regenerating per-template to extend
 * this same treatment to other page types.
 */
add_action( 'wp_head', function () {
	if ( ! TT_MANUAL_CSS_OPT ) {
		return;
	}
	if ( ! is_front_page() ) {
		return;
	}
	$path = __DIR__ . '/assets/critical-home.css';
	if ( ! file_exists( $path ) ) {
		return;
	}
	echo '<style id="tt-critical-home">' . file_get_contents( $path ) . '</style>';
}, 1 );

/**
 * PageSpeed: WordPress core's MediaElement player CSS is enqueued on every
 * page regardless of whether any audio/video is actually present - the
 * homepage has neither, yet paid the render-blocking cost of it (~1.1s
 * combined across its two cache variants). Only keep it when the page
 * really has a video/audio block, shortcode, or oEmbed.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_singular() ) {
		$content = get_post_field( 'post_content', get_the_ID() );
		if ( $content && (
			has_block( 'core/video', $content ) ||
			has_block( 'core/audio', $content ) ||
			has_shortcode( $content, 'video' ) ||
			has_shortcode( $content, 'audio' ) ||
			preg_match( '#youtube\.com|youtu\.be|vimeo\.com#i', $content )
		) ) {
			return;
		}
	}
	wp_dequeue_style( 'wp-mediaelement' );
}, 9999 );

/**
 * Accessibility: fixes for links with no discernible accessible name,
 * flagged by Lighthouse's "link-name" audit (and agentic-browsing tools
 * that rely on a well-formed accessibility tree).
 *
 * - Elementor's Gallery widget renders each item as an <a> whose only child
 *   is a `role="img"` background-image div, not a real <img> - the div has
 *   its own aria-label, but the wrapping link doesn't. Elementor already
 *   puts good descriptive text in `data-elementor-lightbox-title`; copy it
 *   into an aria-label on the link itself.
 * - Elementor's Social Icons widget, and separately its Icon List widget
 *   (used for the footer social links - a different widget with different
 *   markup, `li.elementor-icon-list-item > a` instead of `a.elementor-icon`)
 *   both render icon-only links with no text or label at all; label them
 *   from the recognisable domain in their href.
 *
 * Runs unconditionally in the footer (after all page content, so no need
 * to wait for DOMContentLoaded - by the time this script tag is reached
 * the gallery/icon markup already exists in the DOM). Excluded from WP
 * Rocket's "delay JS execution" via rocket_delay_js_exclusions below:
 * that feature was rewriting this into a script that only runs after a
 * real user interaction (scroll/click/etc), which broke it for automated
 * audits entirely, and even for real visitors meant the DOMContentLoaded
 * listener this used to have would never fire, since DOMContentLoaded has
 * long since happened by the time any interaction-triggered script runs.
 */
add_filter( 'rocket_delay_js_exclusions', function ( $excluded ) {
	$excluded[] = 'tt-a11y-link-labels';
	/* The corporate quote form's inline handler (corporate-ui template) must
	   attach before the visitor's first interaction, or a fast submit hits
	   the native no-feedback fallback. No dependencies, so a lone exclusion
	   is safe (unlike the cart-fragments chain below). */
	$excluded[] = 'tt-corp-quote';
	$excluded[] = 'site-modernize';
	$excluded[] = 'tt-mystery-box-toggle';
	$excluded[] = 'tt-submenu-toggle';
	$excluded[] = 'tt-desktop-submenu-hover';
	$excluded[] = 'tt-mobile-menu-toggle-fallback';
	$excluded[] = 'tt-basket-toggle-fallback';
	$excluded[] = 'tt-popup-action-fallback';
	/* WooCommerce's own cart-fragments.js is what corrects the header
	   basket badge (price/count) after a full-page-cached HTML response -
	   the cached markup reflects whatever cart state existed when that
	   page was cached, not the current visitor's actual cart, and
	   cart-fragments' AJAX refresh on page load is supposed to fix that
	   up immediately. Delayed, it doesn't run until first interaction,
	   so every fresh page load shows a stale/empty basket (confirmed:
	   £0.00/0 items) until the visitor clicks something. */
	$excluded[] = 'cart-fragments';
	/* jQuery itself must also be excluded, or cart-fragments (undelayed
	   above) runs before window.jQuery exists and throws "jQuery is not
	   a function" - caught immediately after deploying the cart-fragments
	   exclusion (2026-07-15/16), present sitewide since jquery-core is a
	   dependency of nearly everything, not just cart-fragments. */
	$excluded[] = 'jquery';
	/* cart-fragments' full WooCommerce-declared dependency list is exactly
	   ['jquery', 'wc-js-cookie'] (checked directly in WooCommerce core's
	   class-wc-frontend-scripts.php, not guessed) - js.cookie.min.js was
	   the second piece still delayed, throwing "Cookies is not defined"
	   right after the jquery fix above. */
	$excluded[] = 'js-cookie';
	/* Nav menu "Gift"/"One Off Boxes" dropdowns stopped opening on hover
	   after Elementor/Elementor Pro were updated 3.x -> 4.1.x (a version
	   gap the original site-inventory audit explicitly flagged as a
	   "treat as planned, tested activity" risk - this is that risk
	   materializing). Root cause has nothing to do with the earlier
	   nav z-index/stacking fix (still fine, header transform confirmed
	   "none"): the actual SmartMenus dropdown-open code lives inside
	   Elementor Pro's 'pro-elements-handlers' bundle, which WP Rocket
	   delays until a real user-interaction event fires - so a visitor's
	   very first hover on a dropdown item does nothing, because the code
	   that would open it hasn't loaded yet. The 'smartmenus' library
	   itself was never delayed, only the code that calls it.
	   Full dependency chain confirmed by reading the exact
	   wp_register_script() calls in elementor/includes/frontend.php and
	   elementor-pro/plugin.php (not guessed): pro-elements-handlers ->
	   elementor-frontend -> elementor-frontend-modules ->
	   elementor-webpack-runtime (+jquery, already excluded above), and
	   elementor-frontend also depends on jquery-ui-position (a small
	   WP-core-bundled script, safe to exclude). */
	$excluded[] = 'pro-elements-handlers';
	$excluded[] = 'elementor-frontend';
	$excluded[] = 'elementor-frontend-modules';
	$excluded[] = 'elementor-webpack-runtime';
	$excluded[] = 'jquery-ui-position';
	$excluded[] = 'smartmenus';
	/* Same bug, same root cause, different Elementor Pro bundle: the header
	   discount banner and footer newsletter popup-action links stayed
	   completely dead on real iOS Safari/Chrome (confirmed via an on-page
	   debug log read directly off a real iPhone, 2026-07-18 - polling for
	   up to 7.5s after the tap, elementorProFrontend.modules.popup never
	   became available at all, not just slow). window.elementorProFrontend
	   itself exists early because print_js_config() inline-prints its
	   settings object separately, but the actual code that builds
	   .modules.popup lives in the 'elementor-pro-frontend' script
	   (assets/js/frontend.js, enqueued in
	   Plugin::enqueue_frontend_scripts() in elementor-pro/plugin.php) -
	   still delayed by WP Rocket like pro-elements-handlers was. Its own
	   declared dependencies (Plugin::get_frontend_depends(), same file):
	   elementor-pro-webpack-runtime + elementor-frontend-modules (already
	   excluded above). Without excluding elementor-pro-webpack-runtime too,
	   elementor-pro-frontend can end up running before its own webpack
	   runtime exists once WP Rocket's delayed-load trigger fires, throwing
	   and never finishing module registration - a permanent failure, not
	   a timing one, which matches what was observed on the real device. */
	$excluded[] = 'elementor-pro-frontend';
	$excluded[] = 'elementor-pro-webpack-runtime';
	/* Same class of bug as the smartmenus one above, this time hitting the
	   homepage testimonial carousel: wp_register_script( 'swiper', ..., [],
	   ... ) in elementor/includes/frontend.php has no dependencies of its
	   own, so nothing else pulls it into the excluded chain automatically,
	   but pro-elements-handlers (already excluded, loads immediately) calls
	   `new Swiper(...)` on it as soon as the page loads. With 'swiper'
	   itself still delayed, that init either fails silently or runs late
	   against a library that only finishes loading once the visitor's
	   first real interaction fires the delay-JS trigger - by then the
	   pagination dots end up wired up, but the very touch gesture that
	   triggered the delayed load is the one that gets missed, so swiping
	   didn't reliably advance the carousel. Confirmed via CDP-dispatched
	   touch events on a fresh load: dot clicks worked, real touch swipes
	   did not. */
	$excluded[] = 'swiper';
	return $excluded;
} );

add_action( 'wp_footer', function () {
	?>
	<script id="tt-a11y-link-labels">
	(function () {
		document.querySelectorAll( 'a.e-gallery-item:not([aria-label])' ).forEach( function ( link ) {
			var label = link.getAttribute( 'data-elementor-lightbox-title' );
			if ( ! label ) {
				var img = link.querySelector( '[role="img"][aria-label]' );
				label = img ? img.getAttribute( 'aria-label' ) : null;
			}
			link.setAttribute( 'aria-label', label || 'View image' );
		} );

		var socialLabels = {
			'facebook.com': 'Facebook',
			'instagram.com': 'Instagram',
			'twitter.com': 'Twitter',
			'x.com': 'Twitter',
			'pinterest.': 'Pinterest',
			'youtube.com': 'YouTube',
			'tiktok.com': 'TikTok',
			'linkedin.com': 'LinkedIn',
			'contact-us': 'Contact us'
		};
		document.querySelectorAll( 'a.elementor-icon:not([aria-label]), li.elementor-icon-list-item > a:not([aria-label])' ).forEach( function ( link ) {
			var href = link.getAttribute( 'href' ) || '';
			for ( var domain in socialLabels ) {
				if ( href.indexOf( domain ) !== -1 ) {
					link.setAttribute( 'aria-label', socialLabels[ domain ] );
					break;
				}
			}
		} );

		// Belt-and-suspenders for a handful of "our-snacks" sticker links: the
		// underlying images already have correct alt text in the media library
		// (_wp_attachment_image_alt), but Elementor's live render doesn't
		// reliably carry it through to these specific instances - cause not
		// fully isolated. Label the wrapping link directly so the audit
		// passes regardless of what the <img>/alt pipeline does upstream.
		var knownIconLabels = {
			'wp-image-7142': 'Fun',
			'wp-image-7144': 'Nutritious',
			'wp-image-7146': 'Surprises'
		};
		document.querySelectorAll( 'a:not([aria-label])' ).forEach( function ( link ) {
			// Note: deliberately not gating this on link.textContent being
			// empty - these links contain a <noscript><img></noscript>
			// fallback, and with JS enabled, textContent includes that
			// noscript's raw markup as literal (unparsed) text, which made
			// an earlier version of this check wrongly treat the link as
			// already having visible text and skip it.
			for ( var cls in knownIconLabels ) {
				if ( link.querySelector( '.' + cls ) ) {
					link.setAttribute( 'aria-label', knownIconLabels[ cls ] );
					break;
				}
			}
		} );

		// A11y (aria-hidden-focus): Elementor Pro's menu-cart panel keeps its
		// 6 links/buttons focusable while closed, even though the container is
		// aria-hidden="true" - so keyboard users tab into an invisible cart.
		// Sync the `inert` attribute to aria-hidden: inert while closed removes
		// the panel from the tab order AND the accessibility tree. This is safe
		// because Elementor's own showCart()/hideCart() set aria-hidden to
		// false/true on this exact container (confirmed in the woocommerce-menu
		// -cart bundle), so opening the cart clears inert and it stays fully
		// interactive. Older browsers without `inert` support just ignore it.
		document.querySelectorAll( '.elementor-menu-cart__container' ).forEach( function ( cart ) {
			var sync = function () {
				if ( cart.getAttribute( 'aria-hidden' ) === 'true' ) {
					cart.setAttribute( 'inert', '' );
				} else {
					cart.removeAttribute( 'inert' );
				}
			};
			sync();
			new MutationObserver( sync ).observe( cart, { attributes: true, attributeFilter: [ 'aria-hidden' ] } );
		} );
	})();
	</script>
	<?php
}, 20 );

add_action( 'wp_footer', function () {
	?>
	<script id="tt-submenu-toggle">
	(function () {
		// Mobile off-canvas menu: Elementor's own click handler correctly
		// toggles aria-expanded on the parent <a>, but SmartMenus' internal
		// display:none inline style on the nested <ul class="sub-menu"> never
		// updates to match on this "toggle" layout, so tapping Gift/One Off
		// Boxes never visually reveals the submenu items. This listens on the
		// same click (which fires after Elementor's own handler already
		// updated aria-expanded, since that's bound directly on the element
		// and runs before a document-level delegated listener during bubble)
		// and syncs the submenu's visibility to match.
		document.addEventListener( 'click', function ( e ) {
			var toggle = e.target.closest( '.menu-mob .elementor-item.has-submenu' );
			if ( ! toggle ) {
				return;
			}
			var submenu = toggle.parentElement.querySelector( ':scope > .sub-menu' );
			if ( ! submenu ) {
				return;
			}
			submenu.style.display = toggle.getAttribute( 'aria-expanded' ) === 'true' ? 'block' : 'none';
		} );
	})();
	</script>
	<?php
}, 20 );

/**
 * Mobile hamburger toggle (.elementor-menu-toggle) reported completely
 * unresponsive on a real iPhone in both Safari and Chrome (both WebKit -
 * Apple requires every iOS browser to use it) - not a visibility/contrast
 * bug, the icon never even swaps to the close (X) state, meaning the tap
 * never registers as a click at all. Every Chromium/Playwright diagnostic
 * came back clean on this same markup: element is visible, correctly
 * hit-testable at its own coordinates (elementFromPoint returns the
 * button's own icon, not some overlapping element), touch-action/
 * pointer-events are "auto" all the way up the ancestor chain, and a
 * plain btn.click() immediately and correctly flips aria-expanded and
 * reveals .elementor-nav-menu--dropdown. So the click handler Elementor
 * Pro binds to this element does exist and works - it's specifically the
 * real-device tap-to-click delivery that's failing, something Chromium's
 * touch emulation (CDP) has no fidelity to reproduce or debug directly.
 *
 * Rather than keep guessing at a WebKit-only touch bug blind, this adds a
 * self-healing fallback: capture the dropdown's open/closed state before
 * anything else can react (capture-phase listener always runs before
 * Elementor's own target/bubble-phase handler, regardless of registration
 * order), then check one macrotask later whether anything actually
 * changed. If Elementor's own handler fired normally, nothing to do here.
 * If nothing changed at all, force the same result ourselves. Safe to
 * coexist with a working native handler - it only ever acts when the
 * native one visibly didn't.
 */
add_action( 'wp_footer', function () {
	?>
	<script id="tt-mobile-menu-toggle-fallback">
	(function () {
		var toggle = document.querySelector( '.elementor-menu-toggle' );
		var dropdown = document.querySelector( '.elementor-nav-menu--dropdown' );
		if ( ! toggle || ! dropdown ) {
			return;
		}
		function isOpen() {
			return getComputedStyle( dropdown ).display !== 'none';
		}
		function setOpen( open ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			toggle.classList.toggle( 'elementor-active', open );
			dropdown.style.display = open ? 'block' : 'none';
		}
		toggle.addEventListener( 'click', function () {
			var wasOpen = isOpen();
			setTimeout( function () {
				if ( isOpen() === wasOpen ) {
					setOpen( ! wasOpen );
				}
			}, 50 );
		}, true );
	})();
	</script>
	<?php
}, 20 );

/**
 * Same real-device symptom as the hamburger toggle above, reported on the
 * same visit: tapping the header basket icon (Elementor Pro's WooCommerce
 * Menu Cart widget, side-cart type) does nothing on a real iPhone. Its
 * open/closed state is driven by an "elementor-menu-cart--shown" class on
 * the widget root (toggled alongside aria-hidden on the slide-out panel
 * and aria-expanded on the toggle button) - confirmed directly by clicking
 * the button and diffing the widget's className before/after, not guessed.
 * Same fallback shape as the hamburger fix: capture-phase listener records
 * the open state before anything else can react, and forces the same
 * result itself only if nothing changed one macrotask later.
 *
 * The panel's own close button (.elementor-menu-cart__close-button) is a
 * separate plain <div> with no click handling of its own beyond whatever
 * Elementor Pro binds to it - same real-device failure reported on it
 * separately once the open side of this fix let people actually reach it.
 * It only ever closes (never toggles), so it gets the same treatment but
 * pinned to the "closed" end state rather than a flip.
 */
add_action( 'wp_footer', function () {
	?>
	<script id="tt-basket-toggle-fallback">
	(function () {
		var toggle = document.querySelector( '.elementor-menu-cart__toggle_button' );
		var widget = toggle ? toggle.closest( '.elementor-widget-woocommerce-menu-cart' ) : null;
		var container = document.querySelector( '.elementor-menu-cart__container' );
		var closeBtn = document.querySelector( '.elementor-menu-cart__close-button' );
		if ( ! toggle || ! widget || ! container ) {
			return;
		}
		function isOpen() {
			return widget.classList.contains( 'elementor-menu-cart--shown' );
		}
		function setOpen( open ) {
			widget.classList.toggle( 'elementor-menu-cart--shown', open );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			container.setAttribute( 'aria-hidden', open ? 'false' : 'true' );
		}
		toggle.addEventListener( 'click', function () {
			var wasOpen = isOpen();
			setTimeout( function () {
				if ( isOpen() === wasOpen ) {
					setOpen( ! wasOpen );
				}
			}, 50 );
		}, true );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', function () {
				setTimeout( function () {
					if ( isOpen() ) {
						setOpen( false );
					}
				}, 50 );
			}, true );
		}
	})();
	</script>
	<?php
}, 20 );

/**
 * Same real-device symptom again, this time on the "Grab your 10% discount
 * code" header banner: it's an Elementor "action link"
 * (#elementor-action:action=popup:open&settings=...), the same generic
 * mechanism the footer's "Signup for newsletter" button uses (see the
 * newsletter popup work above) - not specific to one widget, so this fix
 * is scoped to the href pattern rather than one element, and covers both.
 * Elementor Pro's own frontend JS decodes the base64 "settings" segment of
 * the href to get the popup ID and calls its popup module to show it -
 * rather than reimplement that (and its focus-trap/overlay markup) by
 * hand, the fallback decodes the exact same href and calls Elementor
 * Pro's own already-loaded popup module directly
 * (elementorProFrontend.modules.popup.showPopup), confirmed working when
 * called this way directly in the console. Only acts if no popup became
 * visible at all after a real tap.
 *
 * A fixed 50ms check (the first version of this fallback) worked
 * reliably in testing but was confirmed - via an on-page debug log read
 * directly off a real iPhone, 2026-07-18 - to fail there specifically
 * because elementorProFrontend.modules.popup itself doesn't exist yet
 * 50ms after the tap on that device (window.elementorProFrontend was
 * present, but .modules.popup was not). This is a real-device JS-init
 * race, not a caching or delegation issue as first suspected. Polling
 * for the module to become available (instead of one fixed-delay check)
 * fixes this regardless of how long that device's Elementor Pro init
 * actually takes.
 */
add_action( 'wp_footer', function () {
	?>
	<script id="tt-popup-action-fallback">
	(function () {
		function popupIdFromHref( href ) {
			var decoded;
			try {
				decoded = decodeURIComponent( href );
			} catch ( e ) {
				return null;
			}
			var match = decoded.match( /action=popup:open&settings=([^&]+)/ );
			if ( ! match ) {
				return null;
			}
			try {
				return JSON.parse( atob( match[ 1 ] ) ).id;
			} catch ( e ) {
				return null;
			}
		}
		function popupVisible( id ) {
			var popup = document.querySelector( '.elementor-location-popup[data-elementor-id="' + id + '"]' );
			return !! popup && getComputedStyle( popup ).display !== 'none';
		}
		/* Bound directly to each matching link at load time, not delegated
		   via a single document-level listener - the hamburger and basket
		   fixes above both needed a listener bound straight to the exact
		   tapped element to reliably receive a real touch-originated click
		   on iOS Safari/Chrome, and delegation is one more variable this
		   doesn't need given those two are now confirmed working on a real
		   device this way. */
		var links = document.querySelectorAll( 'a[href^="#elementor-action"]' );
		for ( var i = 0; i < links.length; i++ ) {
			( function ( link ) {
				var id = popupIdFromHref( link.getAttribute( 'href' ) );
				if ( ! id ) {
					return;
				}
				link.addEventListener( 'click', function () {
					var wasVisible = popupVisible( id );
					if ( wasVisible ) {
						return;
					}
					var attempts = 0;
					var maxAttempts = 60; // 60 * 125ms = 7.5s ceiling.
					var poll = setInterval( function () {
						attempts++;
						if ( popupVisible( id ) ) {
							clearInterval( poll );
							return;
						}
						if ( window.elementorProFrontend && window.elementorProFrontend.modules && window.elementorProFrontend.modules.popup ) {
							window.elementorProFrontend.modules.popup.showPopup( { id: id } );
							clearInterval( poll );
							return;
						}
						if ( attempts >= maxAttempts ) {
							clearInterval( poll );
						}
					}, 125 );
				}, true );
			} )( links[ i ] );
		}
	})();
	</script>
	<?php
}, 20 );

/**
 * Desktop nav dropdowns ("Gift", "One Off Boxes") stopped opening on hover
 * after Elementor/Elementor Pro were updated 3.x -> 4.1.x (the version gap
 * the original site-inventory audit explicitly flagged as a "treat as
 * planned, tested activity" risk - this is that risk materializing).
 * Root cause confirmed directly in devtools, not guessed: calling
 * jQuery('.elementor-nav-menu--main').smartmenus() by hand throws
 * "Cannot read properties of null (reading 'parentNode')" inside
 * SmartMenus' own menuInit() (elementor-pro/assets/lib/smartmenus/
 * jquery.smartmenus.min.js) - the exact library Elementor Pro bundles to
 * drive these dropdowns. Something about this site's nav markup under
 * Elementor Pro 4.1.x no longer matches what this bundled SmartMenus
 * 1.2.1 build expects, so it never successfully attaches, and the
 * sub-menu's default display:none is never overridden by anything.
 * Unrelated to the earlier nav z-index/stacking fix (still fine - header
 * transform confirmed "none").
 *
 * Hand-patching Elementor Pro's own vendored copy of SmartMenus would be
 * overwritten on the next plugin update, so instead of that: plain
 * hover-driven show/hide, bypassing SmartMenus entirely. Scoped to
 * '.elementor-nav-menu--main' specifically - excludes '.menu-mob' (the
 * separate mobile nav instance, which already has its own working
 * click-based fix above) and excludes the corporate-orders page (its own
 * template doesn't use this nav-menu widget's dropdown layout).
 */
add_action( 'wp_footer', function () {
	?>
	<script id="tt-desktop-submenu-hover">
	(function () {
		/* Delegated on the stable nav container rather than attaching
		   listeners to each <li> directly - SmartMenus' own init still
		   runs (and still throws, per the note above) but appears to
		   touch/rebuild parts of this menu's markup before it does,
		   which could silently orphan listeners bound straight to the
		   original <li> elements. mouseover/mouseout bubble (mouseenter/
		   mouseleave don't), so relatedTarget is checked manually to
		   only fire on a genuine enter/leave of the <li>, not on every
		   move between its descendants. */
		var nav = document.querySelector( '.elementor-nav-menu--main' );
		if ( ! nav ) {
			return;
		}
		function handle( e, show ) {
			var li = e.target.closest( 'li.menu-item-has-children' );
			if ( ! li || ! nav.contains( li ) ) {
				return;
			}
			if ( e.relatedTarget && li.contains( e.relatedTarget ) ) {
				return;
			}
			var submenu = li.querySelector( ':scope > .sub-menu' );
			if ( submenu ) {
				submenu.style.display = show ? 'block' : 'none';
			}
		}
		nav.addEventListener( 'mouseover', function ( e ) { handle( e, true ); } );
		nav.addEventListener( 'mouseout', function ( e ) { handle( e, false ); } );
	})();
	</script>
	<?php
}, 20 );

/**
 * Site-wide brand-direction pilot.
 *
 * Extends the corporate-orders page's palette (forest green, parchment/
 * cream, gold, terracotta) and typography (DM Sans, already self-hosted -
 * see custom.css - dropped to one family instead of the current mix)
 * to the rest of the site. Deliberately does NOT touch the sticker
 * illustrations or the logo - kept exactly as-is per explicit feedback.
 * Excludes the corporate-orders page itself (ID 36634): it already has
 * its own complete, hand-built design system and this is a blanket
 * !important override, so letting the two overlap risks fighting each
 * other rather than agreeing. Image framing is scoped to specific
 * element IDs (the homepage's "how our subscription works" icons) rather
 * than a blanket image rule, so it can never accidentally catch the
 * stickers/logo. Reversible by removing this one enqueue call.
 *
 * Deployed to production 2026-07-15 after a staging audit found and fixed
 * three bugs: nav-dropdown contrast, sitewide invisible button text (both
 * caused by this file's !important link-color rules outranking
 * Elementor/WooCommerce button styles), and a nav z-index/stacking bug
 * (site-modernize.js was giving the header a permanent transform via the
 * scroll-reveal system, trapping the dropdown in a new stacking context).
 *
 * v2 (staging trial, not yet on production): palette moved from forest
 * green/gold/terracotta/parchment to one locked accent - the brand's own
 * logo teal - on a clean warm-white canvas. See the header comment in
 * site-modernize.css for the full rationale and the old->new CSS variable
 * name mapping.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_page( 36634 ) ) {
		return;
	}
	wp_enqueue_style( 'tt-site-modernize', plugins_url( 'assets/site-modernize.css', __FILE__ ), array(), '1.6.1' );
	wp_enqueue_script( 'tt-site-modernize', plugins_url( 'assets/site-modernize.js', __FILE__ ), array(), '1.6.1', true );
}, 20 );

/**
 * Footer copyright year - was hardcoded ("© Copyright 2025 Treat Trunk...")
 * directly in the sitewide Footer Elementor template's _elementor_data
 * (post 173), so it silently went stale the moment the year changed.
 * [tt_current_year] is used in that text widget's content instead, so it
 * never needs a manual edit again.
 */
add_shortcode( 'tt_current_year', function () {
	return date( 'Y' );
} );

/* =============================================================================
 * SEO / GEO / AEO audit fixes - 2026-07-16
 *
 * Everything below is additive (new wp_head/wp_footer output, new filters)
 * and deliberately does not touch any _elementor_data - avoids the
 * multi-cache-clear dance direct Elementor DB edits require, and keeps
 * every change here reversible by deleting the relevant block.
 *
 * Deliberately NOT included in this pass (need real-world input, not code):
 *   - Citing an external health authority in existing blog posts (content
 *     edit that deserves a real source chosen deliberately)
 *   - B Corp / ISO certification (a business/legal process, not code)
 *   - A phone number or live chat (needs a real number to publish)
 *   - A dedicated Halal/allergen product line (a product/sourcing decision)
 *
 * The "Share the Meal" charity donation checklist item from the audit was
 * dropped entirely, not deferred: the claim only ever existed in the
 * /our-mission-values/ meta description (never the visible page), and the
 * user confirmed 2026-07-16 it isn't a real feature - see
 * scripts/fix-oversized-meta-descriptions.sh for the correction.
 * ============================================================================= */

/**
 * FAQPage schema for /faq/ (page 84) and the "Corporate FAQs" accordion on
 * /corporate-orders/ (page 36634). Both already have the Q&A content live
 * on the page in the right shape - this only adds the missing markup, as a
 * standalone JSON-LD block alongside (not replacing) Yoast's own graph.
 * Question/answer text below is a faithful, trimmed paraphrase of what's
 * actually on each page - schema must reflect visible content.
 */
add_action( 'wp_head', function () {
	$faqs = null;

	if ( is_page( 84 ) ) {
		$faqs = array(
			array( 'How does the healthy snack box subscription work?', 'Choose Standard (20+ snacks) or Mini (10+ snacks), then decide whether to receive your first box straight away or wait for the next delivery date, posted around the 10th of the month. Subsequent boxes are billed monthly.' ),
			array( 'Do you sell one off/gift healthy snack boxes?', 'Yes - Standard and Mini sizes are available as one-off purchases, customisable for adults, kids or both, with gift messaging and gift wrap available at checkout. Subscribers get 10% off one-off boxes.' ),
			array( "What's in my healthy snack box?", 'The Standard box contains 20+ vegan-friendly healthy snacks including premium items and larger pack sizes; the Mini contains 10+. Contents are kept a secret until you open the box.' ),
			array( 'What is your delivery policy?', 'UK postage is free (first class upgrade available). Delivery is typically 2 working days from posting. Subscriptions post around the 10th of each month. We no longer post to Europe, due to Brexit customs requirements.' ),
			array( 'Can I cancel, pause or switch my subscription?', 'Yes, any time via your account. Changes are instant, but if payment has already been collected for the coming month, the change applies from the following month.' ),
			array( "What's your return policy?", 'Faulty items can be returned within 14 days of receipt via tracked delivery. Exchanges are posted within 2 working days of us receiving the return; refunds take 5-10 days to clear.' ),
			array( 'Are the healthy snack boxes gluten free?', "Boxes aren't labelled gluten free, as some snacks are packaged in a factory that also handles gluten. Very few snacks contain gluten as an ingredient though, so a gluten-conscious box is easy to arrange - just note it in the allergy box at checkout." ),
			array( 'What is Sugar Sensible?', "Sugar Sensible is Treat Trunk's practical, common-sense approach to nutrition - favouring snacks with balanced macros and lower-GI sugars over a strict refined-sugar-free rule, since some genuinely healthy snacks contain a small amount of natural sugar." ),
			array( 'Do you cater for allergies?', "We do our best to. Simple requirements can be noted in the allergy box at checkout; more complex allergies should be emailed to hello@treattrunk.co.uk first to confirm we can accommodate them. Customers are responsible for checking each item's ingredients." ),
			array( "What if we don't like something in our healthy snack box?", "Let us know - feedback shapes future boxes. Trying new things is part of the fun, and if a snack isn't for you, it's often one a friend or family member will enjoy." ),
			array( "What's your ethical policy?", 'Treat Trunk works with small, ethical UK businesses, using recyclable, acid-free and biodegradable packaging materials wherever the packaging technology allows it, while keeping healthy snacking affordable and accessible.' ),
			array( 'How can I contact you about your healthy snack boxes?', 'Email hello@treattrunk.co.uk or use the contact form on the Contact Us page.' ),
		);
	} elseif ( is_page( 36634 ) ) {
		// Kept in sync with the visible Corporate FAQs accordion in
		// corporate-ui/templates/corporate-orders-template.php - update both together.
		$faqs = array(
			array( 'What is a corporate snack box?', 'A curated selection of office snacks delivered to your workplace or straight to staff at home - ideal for office kitchens, client visits, staff wellbeing and team perks. Boxes are predominantly vegan, sugar sensible and sourced from independent UK brands.' ),
			array( 'How much do office snacks cost for a team?', 'The Letterbox snack box is £15.99/box, dropping automatically to £13.75/box on 20+ box orders and £13.00/box on 50+ box orders to one address. A one-off Deluxe Corporate Snack Box (60+ snacks) is £125, about £2 a snack. For remote teams that works out from £13 per person with every box delivered to its own address. No minimum order, no discount code needed.' ),
			array( 'Can you deliver to lots of individual home addresses?', "Yes - this is a speciality. Send a spreadsheet of names and addresses and we'll post a tracked, letterbox-friendly box to each one. We've handled orders of 800+ boxes." ),
			array( 'Do the bulk discounts need a code?', 'No - order 20 or more Letterbox boxes to one address and the discount applies automatically in the cart.' ),
			array( 'Can you handle dietary requirements and allergies?', 'All boxes are vegetarian (mostly vegan), low sugar and health-conscious throughout. Individual boxes within a single bulk order can be tailored for gluten-free, nut-free and other requirements.' ),
			array( 'Can we add our company branding?', "Yes - branding can be incorporated on boxes, stickers, wrapping and gift cards. Mention it in your enquiry and we'll quote for it." ),
			array( 'Can we pay by invoice?', "Yes - for corporate orders we can invoice your company directly. Mention it in the enquiry form and we'll set it up." ),
			array( 'How fast is corporate delivery?', "We aim to post orders within 2 working days on a tracked 2 working day service, with a first class upgrade available. For larger bespoke orders, tell us your date in the enquiry and we'll work to it." ),
			array( 'How many snacks should I order per employee?', 'For remote or hybrid teams, one Letterbox box per person posted to their home works best. For a shared office kitchen, one 60+ snack Deluxe box covers roughly 15 to 20 people for a week of snacking as a rule of thumb, or arrives weekly on the Deluxe subscription.' ),
		);
	}

	if ( ! $faqs ) {
		return;
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_map( function ( $qa ) {
			return array(
				'@type'          => 'Question',
				'name'           => $qa[0],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $qa[1],
				),
			);
		}, $faqs ),
	);

	echo '<script type="application/ld+json" class="tt-faqpage-schema">' . wp_json_encode( $schema ) . '</script>' . "\n";
}, 5 );

/**
 * HowTo schema for /how-it-works/ (page 34) - the page already documents a
 * clear 3-step process, just never marked it up.
 */
add_action( 'wp_head', function () {
	if ( ! is_page( 34 ) ) {
		return;
	}

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'HowTo',
		'name'        => 'How the Treat Trunk healthy snack box subscription works',
		'description' => 'How to set up a Treat Trunk healthy snack box subscription, from choosing a plan to receiving your first box.',
		'step'        => array(
			array(
				'@type' => 'HowToStep',
				'name'  => 'Select your plan and box type',
				'text'  => 'Choose from full-size (Standard) or Mini, and customise to your personal dietary needs.',
			),
			array(
				'@type' => 'HowToStep',
				'name'  => 'Choose your delivery',
				'text'  => 'Receive your first box straight away, or wait until the next shipment date on the 10th of the following month.',
			),
			array(
				'@type' => 'HowToStep',
				'name'  => 'Enjoy fun, healthy snacks, delivered',
				'text'  => 'A range of healthy, delicious snacks arrives at your door every month, fully sorted.',
			),
		),
	);

	echo '<script type="application/ld+json" class="tt-howto-schema">' . wp_json_encode( $schema ) . '</script>' . "\n";
}, 5 );

/**
 * Person schema for both named founders on /our-story/ (page 37), plus
 * Review schema for the real, named on-page testimonials (Anni; Julie
 * Baker of Julie Clark Nutrition; Jenny Tschiesche of LunchboxDoctor.com -
 * all shown at 5 stars on the page itself).
 */
add_action( 'wp_head', function () {
	if ( ! is_page( 37 ) ) {
		return;
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'       => 'Person',
				'name'        => 'Sally',
				'jobTitle'    => 'Creator, Treat Trunk',
				'description' => 'Founded Treat Trunk while pregnant with her first child, sharing her journey of overcoming sugar addiction through real-food snacking.',
				'worksFor'    => array( '@id' => 'https://treattrunk.co.uk/#organization' ),
			),
			array(
				'@type'       => 'Person',
				'name'        => 'Abhi',
				'jobTitle'    => 'Treat Trunk',
				'description' => 'Leads Treat Trunk today, with a background in mobile app development and The Meal Prep Market.',
				'worksFor'    => array( '@id' => 'https://treattrunk.co.uk/#organization' ),
			),
			array(
				'@type'         => 'Review',
				'reviewRating'  => array( '@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5' ),
				'author'        => array( '@type' => 'Person', 'name' => 'Anni' ),
				'reviewBody'    => "Great treats and great customer service, I've tried a dozen different snack/treat boxes in the last few months and if I could only recommend one, it would be Treat Trunk.",
				'itemReviewed'  => array( '@id' => 'https://treattrunk.co.uk/#organization' ),
			),
			array(
				'@type'         => 'Review',
				'reviewRating'  => array( '@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5' ),
				'author'        => array( '@type' => 'Person', 'name' => 'Julie Baker, Julie Clark Nutrition' ),
				'reviewBody'    => 'As a Nutritionist who works with kids and young families I was so delighted to be introduced to Treat Trunk. Ideal for those times when you need to grab and go or as an after school snack or for the packed lunches. Brilliant company ethos and great value boxes.',
				'itemReviewed'  => array( '@id' => 'https://treattrunk.co.uk/#organization' ),
			),
			array(
				'@type'         => 'Review',
				'reviewRating'  => array( '@type' => 'Rating', 'ratingValue' => '5', 'bestRating' => '5' ),
				'author'        => array( '@type' => 'Person', 'name' => 'Jenny Tschiesche, LunchboxDoctor.com' ),
				'reviewBody'    => "There are so many brands that I recommend to my clients already in this box. It's a great deal pricewise and we love having these options in the house.",
				'itemReviewed'  => array( '@id' => 'https://treattrunk.co.uk/#organization' ),
			),
		),
	);

	echo '<script type="application/ld+json" class="tt-person-review-schema">' . wp_json_encode( $schema ) . '</script>' . "\n";
}, 5 );

/**
 * AggregateRating schema + a small visible trust badge, tied to the real
 * Trustpilot profile (uk.trustpilot.com/review/treattrunk.co.uk). Numbers
 * below are read directly from Trustpilot's own embedded schema.org markup
 * on a saved copy of that page (2026-07-16) - not scraped, not guessed via
 * search (an earlier search attempt returned self-contradictory numbers
 * that were rightly not trusted). This is a point-in-time snapshot, not a
 * live API pull - the trustpilot-reviews plugin already active on the site
 * has a business key (of71tqWFPb9BPSru) but no TrustBox widget currently
 * placed anywhere (its stored settings show an empty trustboxes array), so
 * there's no live-synced number to read from instead. Re-verify and update
 * these two constants periodically rather than leaving them to go stale.
 */
define( 'TT_TRUSTPILOT_RATING', '3.8' );
define( 'TT_TRUSTPILOT_REVIEW_COUNT', '114' );
define( 'TT_TRUSTPILOT_URL', 'https://uk.trustpilot.com/review/treattrunk.co.uk' );

add_action( 'wp_head', function () {
	if ( ! is_front_page() ) {
		return;
	}
	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'Organization',
		'@id'             => 'https://treattrunk.co.uk/#organization',
		'aggregateRating' => array(
			'@type'       => 'AggregateRating',
			'ratingValue' => TT_TRUSTPILOT_RATING,
			'bestRating'  => '5',
			'worstRating' => '1',
			'reviewCount' => TT_TRUSTPILOT_REVIEW_COUNT,
		),
	);
	echo '<script type="application/ld+json" class="tt-aggregate-rating-schema">' . wp_json_encode( $schema ) . '</script>' . "\n";
}, 5 );

add_action( 'wp_footer', function () {
	echo '<p style="text-align:center;font-size:12px;color:#8a8a8a;padding:0 20px 10px;margin:0;">'
		. 'Rated <strong>' . esc_html( TT_TRUSTPILOT_RATING ) . ' out of 5</strong> from '
		. esc_html( TT_TRUSTPILOT_REVIEW_COUNT ) . ' reviews on <a href="' . esc_url( TT_TRUSTPILOT_URL ) . '" target="_blank" rel="noopener nofollow" style="color:inherit;">Trustpilot</a>'
		. '</p>';
}, 29 );

/**
 * H1 fix for /blog/ (page 7001, a "posts page" archive - not a singular
 * post, so `the_content` never fires for it) and /affiliates/ (page 32607,
 * a static page whose hero text was never actually wrapped in a heading
 * tag). Both currently render zero <h1> elements.
 *
 * Implemented via output buffering rather than `the_content` because the
 * blog archive's markup comes from Elementor's Theme Builder archive
 * template, not a single post's filtered content - `the_content` simply
 * never runs for it. Anchors on the Yoast breadcrumb block
 * (`id="breadcrumbs"`), confirmed present in the rendered HTML of both
 * pages, and is a no-op (page renders unchanged) if that anchor is ever
 * not found, so this can't break the page even if the breadcrumb markup
 * changes later.
 *
 * /blog/ (page 7001) is configured as the site's "Posts page" (Settings ->
 * Reading) - WordPress's own conditional-tags behaviour means `is_page()`
 * always returns false for whatever page is set as the Posts page, even
 * though it unambiguously is one (confirmed via the "blog" body class,
 * which core only adds for `is_home() && ! is_front_page()`). `is_home()`
 * is the correct check here, not `is_page( 7001 )`.
 */
add_action( 'template_redirect', function () {
	if ( ( is_home() && ! is_front_page() ) || is_page( 32607 ) ) {
		$is_blog = is_home() && ! is_front_page();
		ob_start( function ( $html ) use ( $is_blog ) {
			$anchor = '<p style="margin-top:8px;" id="breadcrumbs">';
			$pos    = strpos( $html, $anchor );
			if ( false === $pos ) {
				return $html;
			}
			$end = strpos( $html, '</p>', $pos );
			if ( false === $end ) {
				return $html;
			}
			$end += strlen( '</p>' );

			$title = $is_blog ? 'Blog' : 'Brand Rep & Affiliate Programme';
			$h1    = '<h1 class="elementor-heading-title elementor-size-xl" style="margin:16px 0 8px;">' . esc_html( $title ) . '</h1>';

			return substr( $html, 0, $end ) . $h1 . substr( $html, $end );
		} );
	}
}, 1 );

/**
 * Trim the HTTP -> HTTPS redirect to a permanent 301. Bitnami/Apache
 * appears to be issuing a 302 for this today (confirmed via `curl -I
 * http://treattrunk.co.uk/`) - if that redirect is happening at the Apache
 * vhost level, this WP-level hook never actually runs (Apache answers
 * before PHP loads) and the real fix is a one-line change to the vhost's
 * mod_rewrite rule, not this file. Left in as a safe, no-op-if-unreachable
 * defensive fix in case WP/a plugin controls it instead.
 */
add_action( 'template_redirect', function () {
	if ( ! is_ssl() && ! is_admin() ) {
		$redirect_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		wp_safe_redirect( $redirect_url, 301 );
		exit;
	}
}, 0 );

/**
 * noindex,follow the thin monthly "what's in this month's box" recap posts
 * (2019-2023) - near-duplicate content that dilutes topical authority
 * without adding unique value. `follow` is kept so internal link equity
 * still flows; this only removes them from the index, doesn't block
 * crawling. Genuinely useful posts (recipes, interviews, "10 tips"
 * listicles) are deliberately NOT in this list.
 */
add_filter( 'wpseo_robots_array', function ( $robots ) {
	static $thin_recap_slugs = array(
		'july2020', 'june2020-2', 'may2020', 'april2020', 'march2020', 'february2020',
		'january-2020', 'december-2019-treat-trunk', 'november2019', 'october2019',
		'september2019', 'august2020', 'september2020', 'october2020', 'november2020',
		'december2020', 'jan21', 'feb21', 'march21', 'april21', 'may21', 'june21',
		'july21', 'aug21', 'sep21', 'nov21', 'oct21', 'dec21', 'jan22', 'feb22',
		'march22', 'april22', 'may22', 'june22', 'july22', 'aug22', 'sept22', 'oct22',
		'nov22', 'dec22', 'jan23', 'feb23', 'mar23', 'apr23', 'may23', 'june23',
		'july23', 'aug23', 'jan25',
	);

	if ( is_singular( 'post' ) && in_array( get_post_field( 'post_name' ), $thin_recap_slugs, true ) ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
	}

	return $robots;
} );

/**
 * Company registration line - a standard UK ecommerce trust signal that
 * was missing entirely. Real details confirmed live on Companies House
 * (company 15624707) 2026-07-16. Echoed via wp_footer rather than added to
 * the Elementor-built global Footer template (post 173), to avoid another
 * direct _elementor_data edit - same reasoning as the copyright-year fix
 * above.
 */
add_action( 'wp_footer', function () {
	echo '<p style="text-align:center;font-size:12px;color:#8a8a8a;padding:10px 20px;margin:0;">'
		. 'Treat Trunk Ltd &middot; Company No. 15624707 &middot; Registered office: 86-90 Paul Street, London, EC2A 4NE'
		. '</p>';
}, 30 );

/**
 * [tt_box_comparison] - Mini vs Standard box comparison table. Not
 * auto-injected anywhere: /subscribe/ and /shop/ sit right at the top of
 * the checkout funnel, and per the standing safety rules that's not a page
 * to blind-inject markup into via output buffering the way the H1 fix
 * above does for lower-stakes pages. Drop this shortcode into place via an
 * Elementor Shortcode widget on staging once reviewed.
 */
add_shortcode( 'tt_box_comparison', function () {
	ob_start();
	?>
	<table class="tt-box-comparison" style="width:100%;border-collapse:collapse;margin:24px 0;font-size:15px;">
		<thead>
			<tr style="background:#f4f5f3;">
				<th style="text-align:left;padding:12px 14px;border:1px solid #e0e0e0;">&nbsp;</th>
				<th style="text-align:left;padding:12px 14px;border:1px solid #e0e0e0;">Mini</th>
				<th style="text-align:left;padding:12px 14px;border:1px solid #e0e0e0;">Standard</th>
			</tr>
		</thead>
		<tbody>
			<tr><td style="padding:12px 14px;border:1px solid #e0e0e0;font-weight:600;">Snacks per box</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">10+</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">20+, including premium items and larger pack sizes</td></tr>
			<tr><td style="padding:12px 14px;border:1px solid #e0e0e0;font-weight:600;">Price</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">From £24.99/month</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">From £39.99/month</td></tr>
			<tr><td style="padding:12px 14px;border:1px solid #e0e0e0;font-weight:600;">Best for</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">Trying Treat Trunk, or snacking for one</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">Families, or anyone who wants more variety each month</td></tr>
			<tr><td style="padding:12px 14px;border:1px solid #e0e0e0;font-weight:600;">Customisable for adults/kids</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">Yes</td><td style="padding:12px 14px;border:1px solid #e0e0e0;">Yes</td></tr>
		</tbody>
	</table>
	<?php
	return ob_get_clean();
} );

/**
 * Newsletter duplicate-check endpoint (added 2026-07-19).
 *
 * ActiveCampaign's proc.php returns an identical success response for new
 * and already-subscribed emails, so the signup popup can't distinguish the
 * two client-side. This same-origin AJAX endpoint checks (via the AC API,
 * using the credentials the AC WooCommerce plugin already stores) whether
 * an email is already a contact, so the popup can honestly say "we've
 * re-sent your code" to existing subscribers (AC's welcome automation has
 * multientry enabled, so the code email genuinely is re-sent).
 *
 * Privacy trade-off, considered deliberately: this discloses whether an
 * email is subscribed (enumeration). Standard for newsletter forms
 * (Mailchimp's embeds disclose the same), mitigated here by returning only
 * a boolean and rate-limiting to 8 checks per 10 minutes per IP.
 */
add_action( 'wp_ajax_tt_nl_check', 'tt_newsletter_duplicate_check' );
add_action( 'wp_ajax_nopriv_tt_nl_check', 'tt_newsletter_duplicate_check' );
function tt_newsletter_duplicate_check() {
	$email = isset( $_GET['email'] ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
	if ( ! $email || ! is_email( $email ) ) {
		wp_send_json( array( 'exists' => false ) );
	}

	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
	$key = 'tt_nl_chk_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 8 ) {
		wp_send_json( array( 'exists' => false ) );
	}
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

	$settings = get_option( 'activecampaign_for_woocommerce_settings' );
	if ( empty( $settings['api_url'] ) || empty( $settings['api_key'] ) ) {
		wp_send_json( array( 'exists' => false ) );
	}

	$resp = wp_remote_get(
		trailingslashit( $settings['api_url'] ) . 'api/3/contacts?email=' . rawurlencode( $email ),
		array(
			'timeout' => 4,
			'headers' => array( 'Api-Token' => $settings['api_key'] ),
		)
	);
	if ( is_wp_error( $resp ) || wp_remote_retrieve_response_code( $resp ) !== 200 ) {
		wp_send_json( array( 'exists' => false ) );
	}

	$body = json_decode( wp_remote_retrieve_body( $resp ), true );
	wp_send_json( array( 'exists' => ! empty( $body['contacts'] ) ) );
}

/**
 * Corporate quote enquiry -> email the Treat Trunk team (added 2026-07-19).
 *
 * The /corporate-orders/ "Request a quote" form posts to ActiveCampaign form 1
 * (the consumer newsletter) which only ever subscribed the enquirer and, for a
 * brand-new email, sent the 10% welcome code - the team was never notified of a
 * B2B enquiry. This same-origin endpoint emails hello@treattrunk.co.uk on every
 * submission so leads actually reach the team (the AC subscribe still happens in
 * parallel client-side, preserving AC lead capture).
 *
 * Abuse protection: honeypot field (bots fill it -> silently dropped) plus a
 * rate limit of 5 sends per 15 minutes per IP. No nonce, deliberately: the page
 * is full-page-cached by WP Rocket so a nonce would be stale on first paint -
 * same reasoning as tt_nl_check above.
 */
add_action( 'wp_ajax_tt_corp_enquiry', 'tt_corporate_enquiry_notify' );
add_action( 'wp_ajax_nopriv_tt_corp_enquiry', 'tt_corporate_enquiry_notify' );
function tt_corporate_enquiry_notify() {
	// Honeypot: a real user never fills this hidden field.
	if ( ! empty( $_POST['tt_hp'] ) ) {
		wp_send_json( array( 'ok' => true ) );
	}

	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
	$key  = 'tt_corp_enq_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 5 ) {
		wp_send_json_error( array( 'message' => 'Too many enquiries in a short time. Please email hello@treattrunk.co.uk directly.' ) );
	}

	$firstname = isset( $_POST['firstname'] ) ? sanitize_text_field( wp_unslash( $_POST['firstname'] ) ) : '';
	$lastname  = isset( $_POST['lastname'] ) ? sanitize_text_field( wp_unslash( $_POST['lastname'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message   = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( empty( $firstname ) || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Please enter your name and a valid email.' ) );
	}

	set_transient( $key, $hits + 1, 15 * MINUTE_IN_SECONDS );

	$name    = trim( $firstname . ' ' . $lastname );
	$subject = 'New corporate enquiry: ' . $name;
	$body    = "New corporate snack box enquiry from the /corporate-orders/ quote form.\n\n"
		. "Name:  $name\n"
		. "Email: $email\n\n"
		. "Message:\n" . ( $message !== '' ? $message : '(none provided)' ) . "\n\n"
		. "Reply directly to this email to respond to the enquirer.";
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( 'hello@treattrunk.co.uk', $subject, $body, $headers );

	// wp_mail returning false is rare (SMTP handoff failure). Report it so the
	// front-end can tell the enquirer to email us directly rather than assume
	// the lead was captured when it wasn't.
	if ( $sent ) {
		wp_send_json_success( array( 'message' => 'sent' ) );
	}
	wp_send_json_error( array( 'message' => "Sorry, that didn't go through. Please email hello@treattrunk.co.uk directly." ) );
}

/**
 * Homepage hero LCP: stop WP Rocket's Above-The-Fold optimizer from also
 * preloading the hero (added 2026-07-20). The hero is an Elementor CSS
 * background image; WP Rocket auto-detects it and emits its own
 * `data-rocket-preload as="image" fetchpriority="high"` for the full-size
 * (106KB) file with no media query, so on mobile the browser fetches that at
 * high priority alongside the 50KB responsive image the hero actually needs,
 * delaying the paint. jgreen_preload_homepage_hero() (theme functions.php)
 * already preloads the correct per-viewport image at high priority, so WP
 * Rocket's is pure waste here. Scoped to the front page only - the ATF
 * optimizer still runs on product/other pages where the LCP is a real <img>.
 */
add_filter( 'rocket_atf_elements', function ( $elements ) {
	return is_front_page() ? array() : $elements;
} );
