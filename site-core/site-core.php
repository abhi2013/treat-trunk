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
 * reCAPTCHA v3 badge: hide it on mobile, where the fixed bottom-right icon eats
 * screen space and overlaps content. Google's terms allow hiding the badge as
 * long as the reCAPTCHA attribution text is shown in its place - the mobile-only
 * footer line below provides that. Desktop keeps the standard badge (which
 * carries its own attribution), so no change there.
 *
 * The hide rule is an inline <style> rather than custom.css on purpose: the
 * .grecaptcha-badge element is injected by reCAPTCHA's JS after the initial
 * HTML, so RUCSS would strip a rule targeting it from an enqueued stylesheet as
 * "unused". A developer inline <style> is left untouched by RUCSS.
 */
add_action( 'wp_head', function () {
	echo '<style id="tt-recaptcha-badge-hide">@media (max-width:767px){.grecaptcha-badge{visibility:hidden !important;}}</style>' . "\n";
}, 1 );

/**
 * Consistent clickable product cards across the WHOLE site. Every Elementor
 * image that links to a /product/ page (the one-off / gift / subscription
 * illustration cards on the homepage, /send-a-gift/, etc.) gets rounded
 * corners, a soft shadow and a hover lift, so each product image reads as the
 * same tappable card. Scoped to /product/ links, so it never touches the logo
 * (which links home), nav, or social icons.
 *
 * Inline <style> rather than custom.css on purpose: RUCSS strips the
 * a[href*="/product/"] attribute selector from an enqueued stylesheet as
 * "unused" (its SaaS renderer doesn't retain it), which left the gift-page
 * cards flat. A developer inline <style> is left untouched by RUCSS.
 */
add_action( 'wp_head', function () {
	echo '<style id="tt-product-cards">'
		. '.elementor-widget-image a[href*="/product/"]{display:block;border-radius:18px;}'
		. '.elementor-widget-image a[href*="/product/"] img{border-radius:18px !important;box-shadow:0 14px 34px -18px rgba(11,89,81,0.4);transition:transform 0.22s ease,box-shadow 0.22s ease;display:block;}'
		. '.elementor-widget-image a[href*="/product/"]:hover img{transform:translateY(-6px);box-shadow:0 24px 48px -18px rgba(11,89,81,0.55);}'
		. '</style>' . "\n";
}, 1 );

/**
 * Keep the JS-toggled scroll-reveal classes in RUCSS's used-CSS. The
 * .tt-stagger-in state (added by site-modernize.js only once the section
 * scrolls into view) may not be present when RUCSS's renderer snapshots the
 * page, so RUCSS could strip its rule and leave the "why choose us" feature
 * columns permanently at opacity:0. Safelisting the token keeps every
 * .tt-stagger* rule regardless. (There is a JS safety-timeout too, but that
 * only helps if the CSS rule survives.)
 */
add_filter( 'rocket_rucss_safelist', function ( $safelist ) {
	$safelist[] = 'tt-stagger';
	// Keep the product-card rules (below) in the used CSS. RUCSS's renderer
	// drops the a[href*="/product/"] attribute selector as "unused", which left
	// the gift-page / homepage product images flat; safelisting the fragment
	// forces every rule whose selector contains it to be retained.
	$safelist[] = '[href*="/product/"]';
	// The header's slide-out basket is off-canvas (aria-hidden) when RUCSS
	// snapshots the page, so its "View basket"/"Checkout" button rules
	// (corporate-ui/assets/corporate-orders.css, added 2026-09-13) would be
	// dropped as unused and the buttons fall back to Elementor's grey.
	$safelist[] = '.elementor-menu-cart__footer-buttons';
	return $safelist;
} );

/**
 * Raise WP Rocket's preload floor so the cache can actually get warm.
 *
 * WP Rocket sizes each preload batch as `round( -5 * $average_duration + 55 )`,
 * clamped to [ min_in_progress_jobs, pending_jobs_cron_rows ] - see
 * PreloadUrl::process_pending_jobs(). That estimate needs the
 * `rocket_preload_previous_requests_durations` transient, which on this install
 * is never set, so the batch permanently falls back to the floor of 5.
 *
 * At 5 URLs per run against a system cron that fires every 5 minutes, that is
 * 60 URLs/hour. With ~560 URLs queued a full warm-up took ~9.2 hours, while the
 * cache lifespan expired pages after 10 - so the site was never more than
 * partly cached and most crawler/visitor requests paid the ~900ms
 * regenerate-from-PHP cost instead of the ~100ms cache-hit cost. That is the
 * cause of the Ahrefs "Slow page" warnings (their own numbers show the HTML
 * itself downloads in ~80ms; the time is all TTFB).
 *
 * 15 is deliberately conservative rather than WP Rocket's own 45 ceiling: each
 * job is a loopback HTTP request, and this is a 2-core box that was taken down
 * on 2026-07-22 by exactly that kind of cron/loopback pile-up. 15 brings a full
 * warm-up to ~3 hours, which is comfortably inside the (now much longer) cache
 * lifespan, without tripling concurrent request pressure.
 */
add_filter( 'rocket_preload_cache_min_in_progress_jobs_count', function () {
	return 15;
} );

add_action( 'wp_footer', function () {
	echo '<div class="tt-recaptcha-tos" style="display:none;text-align:center;font-size:12px;line-height:1.5;color:#666;padding:12px 16px;">'
		. 'This site is protected by reCAPTCHA and the Google '
		. '<a href="https://policies.google.com/privacy" target="_blank" rel="nofollow noopener" style="color:#666;text-decoration:underline;">Privacy Policy</a> and '
		. '<a href="https://policies.google.com/terms" target="_blank" rel="nofollow noopener" style="color:#666;text-decoration:underline;">Terms of Service</a> apply.'
		. '</div>'
		. '<style>@media (max-width:767px){.tt-recaptcha-tos{display:block !important;}}</style>' . "\n";
} );

/**
 * Contact Form 7 loads its reCAPTCHA v3 script (and pulls in Google's api.js +
 * the badge) on EVERY page by default, even pages with no form - a well-known
 * CF7 behaviour and pure dead weight on articles, the FAQ-less pages, etc.
 * Dequeue it unless the current page actually embeds a CF7 form, so reCAPTCHA
 * only loads where it's needed (Contact, Brand Rep/Affiliates, Corporate Orders,
 * FAQ - the pages that contain [contact-form-7], whether in post content or in
 * Elementor data). Verified no CF7 form lives in any global header/footer/popup
 * template, so dropping it elsewhere is safe. On CF7 pages nothing is dequeued,
 * so those forms keep their reCAPTCHA token generation. Handles confirmed from
 * CF7 6.x source: 'google-recaptcha' (api.js) and 'wpcf7-recaptcha' (module).
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_admin() ) {
		return;
	}
	$has_cf7_form = false;
	if ( is_singular() ) {
		$post = get_post();
		if ( $post instanceof WP_Post ) {
			if ( false !== strpos( (string) $post->post_content, 'contact-form-7' ) ) {
				$has_cf7_form = true;
			} else {
				$elementor = get_post_meta( $post->ID, '_elementor_data', true );
				if ( is_string( $elementor ) && false !== strpos( $elementor, 'contact-form-7' ) ) {
					$has_cf7_form = true;
				}
			}
		}
	}
	if ( ! $has_cf7_form ) {
		wp_dequeue_script( 'wpcf7-recaptcha' );
		wp_dequeue_script( 'google-recaptcha' );
	}
}, 100 );

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
			/* 2026-09-13: WP Rocket's delay-JS swallows the real first tap,
			   loads the delayed scripts, then re-dispatches a synthetic
			   click. By the time that replayed click reaches this listener
			   the dropdown is already display:block (Elementor's handler
			   has acted on the replayed sequence) while aria-expanded is
			   still "false" - so the old "did the display change?" check
			   saw no change 50ms later and forced the menu CLOSED again.
			   Net effect: the first tap on every page did nothing. Now the
			   toggle's own aria-expanded is the primary signal: if Elementor
			   moved it, the native handler worked and we stay out of it. */
			var wasOpen     = isOpen();
			var wasExpanded = toggle.getAttribute( 'aria-expanded' );
			setTimeout( function () {
				if ( toggle.getAttribute( 'aria-expanded' ) !== wasExpanded ) {
					return;
				}
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
			// Same aria-expanded guard as the hamburger fallback above (2026-09-13):
			// if Elementor's own handler moved the state, leave it alone.
			var wasOpen     = isOpen();
			var wasExpanded = toggle.getAttribute( 'aria-expanded' );
			setTimeout( function () {
				if ( toggle.getAttribute( 'aria-expanded' ) !== wasExpanded ) {
					return;
				}
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
				/* The last top-level dropdown ("About", added 2026-09-13)
				   sits at the right edge of the header and Elementor
				   positions panels from the parent's left edge, so it ran
				   off the viewport. Anchor it to the right instead. Done
				   here as inline styles because a <style> rule for it gets
				   stripped by WP Rocket's Remove Unused CSS (the panel is
				   display:none when RUCSS scans the page). */
				if ( show && li.parentElement && li === li.parentElement.lastElementChild ) {
					submenu.style.left  = 'auto';
					submenu.style.right = '0';
				}
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
	// Skip every page built on a corporate-ui hand-built template, not just
	// /corporate-orders/ (36634): the gifting page (54774, added 2026-08-01)
	// has the same complete design system, and site-modernize's !important
	// link-color rules were turning its solid teal buttons teal-on-teal
	// (invisible) - the exact bug the 2026-07-15 staging audit fixed
	// elsewhere. Template check rather than an ID list so future corporate-ui
	// pages are excluded automatically.
	if ( is_page( 36634 ) ) {
		return;
	}
	if ( is_page() && function_exists( 'tt_corp_ui_templates' )
		&& isset( tt_corp_ui_templates()[ get_page_template_slug( get_queried_object_id() ) ] ) ) {
		return;
	}
	wp_enqueue_style( 'tt-site-modernize', plugins_url( 'assets/site-modernize.css', __FILE__ ), array(), '1.6.7' );
	wp_enqueue_script( 'tt-site-modernize', plugins_url( 'assets/site-modernize.js', __FILE__ ), array(), '1.6.9', true );
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
		// Kept in sync with the visible Corporate gifting FAQs accordion in
		// corporate-ui/templates/corporate-orders-template.php - update both
		// together. (The office and Christmas pages emit their own schema from
		// corporate-ui/templates/parts/schema.php.)
		$faqs = array(
			array( 'Is there a minimum order for corporate snack gifts?', 'No. One New Mum Box for one colleague is a real order. Volume pricing on the Letterbox Gift and Full Treat Trunk starts at 20.' ),
			array( 'Can we send employee gift boxes to home addresses?', 'Yes. Send us a spreadsheet of addresses and we handle every label, dispatch tracked and report delivery status for every recipient. The Letterbox Gift fits through any standard letterbox.' ),
			array( 'Can we pay by invoice, and can it come out of our wellbeing budget?', 'Yes. Your company is invoiced directly and we will word the invoice line to match the budget you are using, wellbeing, HR, marketing or client entertainment. Our standard terms are payment on invoice, then dispatch.' ),
			array( 'Can you add our branding?', 'Yes: gift cards with your artwork and message, branded stickers on the box, and ribbon on the gift-wrapped tiers. We proof everything first. Allow around two weeks.' ),
			array( 'What is in the New Mum and Menopause boxes, and are they suitable as employee gifts?', 'Both are 20+ healthier-for-you snacks chosen for the moment: one-handed and energy-steady for new parents; energy, sleep and steadiness for menopause. Both are widely used as returning-from-leave and wellbeing-programme gifts. We suggest offering the Menopause Box as a choice within a wellbeing menu rather than sending it unrequested.' ),
			array( 'Can you set up recurring gifts for new starters or anniversaries?', 'Yes. Give us a rolling list or the dates and we send a box each time, with a card, and invoice monthly.' ),
			array( 'What dietary options are there?', 'Vegetarian throughout and mostly vegan. Vegan, gluten-free and nut-aware versions at no extra cost, per recipient. Kosher case by case. Every box carries a contents and allergen card.' ),
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
			$h1    = '<h1 class="elementor-heading-title elementor-size-xl tt-archive-h1"'
				. ' style="margin:6px 0 0;line-height:1.2;">' . esc_html( $title ) . '</h1>';

			/*
			 * The anchor's parent is an Elementor `.elementor-container`, which
			 * is `display:flex; flex-wrap:nowrap`. Appending the <h1> as a
			 * sibling of the breadcrumb <p> therefore made the two share a
			 * single flex row - the title rendered jammed against the end of
			 * "Home » Blog" on the same line. Wrapping both in one full-width
			 * block gives the container a single flex item, inside which the
			 * <p> and <h1> stack normally.
			 */
			$breadcrumb = substr( $html, $pos, $end - $pos );
			$head       = '<div class="tt-archive-head" style="flex:0 0 100%;width:100%;">'
				. $breadcrumb . $h1 . '</div>';

			return substr( $html, 0, $pos ) . $head . substr( $html, $end );
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
 * Single source of truth for the thin monthly "what's in this month's box"
 * recap post slugs (2019-2023). Used by BOTH the robots-meta filter (renders
 * noindex on the page) and the sitemap-exclusion filter below, so the two can
 * never drift out of sync - the original bug being that the page said
 * "noindex" while the XML sitemap still listed the URL, which crawlers flag as
 * a "noindex page in sitemap" contradiction.
 */
/**
 * Functional/account utility pages that carry no search value (login, email
 * preferences, account address forms - two even share the duplicate title
 * "Shipping Addresses"). noindexed and kept out of the sitemap so they stop
 * counting as thin/no-H1/indexable clutter. Paths (not slugs) because several
 * are child pages. /email-offer/ is deliberately NOT here - it may be a live
 * campaign landing page; left for a human decision.
 */
function tt_noindex_utility_page_paths() {
	return array(
		'affiliate-home/affiliate-login',
		'communication-preferences',
		'checkout/shipping-addresses',
		'my-account/account-addresses',
	);
}

/**
 * Retired products whose WooCommerce catalog visibility is "hidden"
 * (exclude-from-catalog + exclude-from-search). Because they are hidden they
 * can never appear in /shop/ or any product loop, so nothing on the site is
 * able to link to them - they crawl as indexable orphan pages that still sit in
 * product-sitemap.xml. Precedent: product 38923 (virgin-gift-experience) is
 * already hidden AND out of the sitemap; these were simply missed.
 *
 * IDs, not slugs, because WooCommerce products are looked up far more cheaply
 * by ID and these are stable. Products that are hidden but still actively sold
 * through a campaign link must NOT be added here.
 *
 * 43461 vegan-snack-box - retired, confirmed 2026-08-01.
 * 7589  gift-treat-trunk - hidden duplicate of one-off-treat-trunk; its URL
 *       already 301s there, yet product-sitemap.xml still listed it and GSC
 *       reported it as "crawled - currently not indexed" (2026-09-17).
 */
function tt_noindex_product_ids() {
	return array( 43461, 7589 );
}

function tt_thin_recap_slugs() {
	return array(
		'july2020', 'june2020-2', 'may2020', 'april2020', 'march2020', 'february2020',
		'january-2020', 'december-2019-treat-trunk', 'november2019', 'october2019',
		'september2019', 'august2020', 'september2020', 'october2020', 'november2020',
		'december2020', 'jan21', 'feb21', 'march21', 'april21', 'may21', 'june21',
		'july21', 'aug21', 'sep21', 'nov21', 'oct21', 'dec21', 'jan22', 'feb22',
		'march22', 'april22', 'may22', 'june22', 'july22', 'aug22', 'sept22', 'oct22',
		'nov22', 'dec22', 'jan23', 'feb23', 'mar23', 'apr23', 'may23', 'june23',
		'july23', 'aug23', 'jan25',
	);
}

/**
 * noindex,follow the thin recap posts (see tt_thin_recap_slugs) plus the thin
 * tag archives (post_tag + product_tag): 207 post tags + 65 product tags for
 * ~106 posts, no unique H1/description/content, near-duplicate index bloat that
 * competes with the real money pages. `follow` is kept everywhere so internal
 * link equity still flows; this only removes them from the index, doesn't block
 * crawling. Genuinely useful posts (recipes, interviews, "10 tips" listicles)
 * are deliberately NOT in the recap list.
 */
add_filter( 'wpseo_robots_array', function ( $robots ) {
	$is_recap = is_singular( 'post' )
		&& in_array( get_post_field( 'post_name' ), tt_thin_recap_slugs(), true );

	// Note: post_tag is a built-in taxonomy, so is_tag() must be used - is_tax(
	// 'post_tag' ) returns false. product_tag is custom, so is_tax() is correct.
	// Junk product categories: WooCommerce's default "Uncategorised" and a
	// stray numeric "27" category - thin, no search value. "Block Subscription"
	// (blocksub) is deliberately NOT here - it's a real gift-subscription group.
	$junk_cat = is_product_category( array( 'uncategorised', '27' ) );

	// Hidden/retired products - see tt_noindex_product_ids.
	$is_retired_product = is_singular( 'product' )
		&& in_array( (int) get_queried_object_id(), tt_noindex_product_ids(), true );

	// product_shipping_class archives ("Small Box", "Subscription Box (Large)"
	// etc). The taxonomy has no rewrite rule, so these only exist as raw
	// ?taxonomy=product_shipping_class&term=... query-string URLs - never linked
	// from anywhere on the site because no template would ever link a shipping
	// class, yet Yoast was publishing them in product_shipping_class-sitemap.xml
	// as indexable orphans. Zero search value; they are a shipping
	// implementation detail.
	//
	// Author archives: a near-duplicate listing of the blog with no unique
	// content. Only surfaced because guest interviewees (e.g. /author/zoewilliams/)
	// were given user accounts, so each one spawned its own orphan archive.
	if ( $is_recap || $is_retired_product || is_tag() || is_tax( 'product_tag' )
		|| is_tax( 'product_shipping_class' ) || is_author()
		|| is_page( tt_noindex_utility_page_paths() ) || $junk_cat ) {
		$robots['index']  = 'noindex';
		$robots['follow'] = 'follow';
	}

	return $robots;
} );

/**
 * Keep the noindexed recap posts out of the XML sitemap too. The robots filter
 * above runs on the front-end template render, but Yoast builds its sitemap
 * separately and does NOT run that filter - so without this the URLs kept
 * appearing in post-sitemap.xml while serving noindex (the contradiction
 * crawlers flag). Slugs are resolved to IDs once and cached.
 */
/**
 * Resolve tt_thin_recap_slugs() to post IDs. Shared by the sitemap exclusion
 * below and the blog-listing query filter further down, so both always operate
 * on the same set. ~49 get_page_by_path() lookups is too much to repeat on
 * every blog page view, so the result is cached in a transient keyed on a hash
 * of the slug list - editing tt_thin_recap_slugs() changes the key and busts
 * the cache automatically, with no manual flush needed.
 */
function tt_thin_recap_post_ids() {
	static $ids = null;
	if ( null !== $ids ) {
		return $ids;
	}

	$slugs = tt_thin_recap_slugs();
	$key   = 'tt_recap_ids_' . substr( md5( implode( ',', $slugs ) ), 0, 12 );
	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		$ids = $cached;
		return $ids;
	}

	$ids = array();
	foreach ( $slugs as $slug ) {
		$post = get_page_by_path( $slug, OBJECT, 'post' );
		if ( $post ) {
			$ids[] = (int) $post->ID;
		}
	}
	set_transient( $key, $ids, WEEK_IN_SECONDS );

	return $ids;
}

add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', function ( $excluded ) {
	static $ids = null;
	if ( null === $ids ) {
		$ids = tt_thin_recap_post_ids();
		// WooCommerce cart/checkout/my-account pages: correctly noindexed (their
		// noindex comes from a runtime filter, so Yoast's sitemap builder doesn't
		// see it and kept listing them - the same noindex-in-sitemap contradiction
		// as the recap posts). Resolve their IDs by their assigned Woo option so
		// this stays correct even if the pages are swapped.
		foreach ( array( 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_myaccount_page_id' ) as $opt ) {
			$pid = (int) get_option( $opt );
			if ( $pid ) {
				$ids[] = $pid;
			}
		}
		// The noindexed utility pages (see tt_noindex_utility_page_paths) - their
		// noindex comes from the runtime filter above, so exclude them here too or
		// they become fresh noindex-in-sitemap contradictions.
		foreach ( tt_noindex_utility_page_paths() as $path ) {
			$p = get_page_by_path( $path );
			if ( $p ) {
				$ids[] = (int) $p->ID;
			}
		}
		// Retired hidden products (see tt_noindex_product_ids) - noindexed by the
		// robots filter above, so they must leave product-sitemap.xml too.
		$ids = array_merge( $ids, tt_noindex_product_ids() );
	}
	return array_merge( (array) $excluded, $ids );
} );

/**
 * Crawl-budget hygiene for the virtual robots.txt (there is no physical file;
 * WooCommerce and Yoast both append to this same filter). Three weeks of
 * Googlebot traffic to 2026-09-17 showed ~5% of its requests going to
 * ?wc-ajax=get_refreshed_fragments (a JSON cart endpoint) and a steady stream of
 * WooPayments express-checkout URLs - each carries a fresh _wpnonce, so Google
 * scrapes an endless supply of unique URLs out of the "you must log in" JSON
 * blob in product-page HTML. GSC listed 16 of them under "crawled - currently
 * not indexed". The product_shipping_class query URLs are already noindexed
 * above but have no reason to be fetched at all. None of these is ever needed
 * for rendering, so blocking them costs nothing.
 */
add_filter( 'robots_txt', function ( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	$output .= "\n# Treat Trunk: crawl-budget hygiene (site-core)\n";
	$output .= "User-agent: *\n";
	$output .= "Disallow: /*?wc-ajax=\n";
	$output .= "Disallow: /*&wc-ajax=\n";
	$output .= "Disallow: /*wcpay_express_checkout_redirect_url=\n";
	$output .= "Disallow: /*?taxonomy=product_shipping_class\n";
	return $output;
}, 20, 2 );

/**
 * Drop the post_tag and product_tag archives from the XML sitemap so they match
 * their noindex robots state above (same contradiction-avoidance as the recap
 * posts). Yoast passes the taxonomy name; return an empty array to emit no
 * entries for that taxonomy's sitemap.
 */
add_filter( 'wpseo_sitemap_exclude_taxonomy', function ( $excluded, $taxonomy ) {
	if ( in_array( $taxonomy, array( 'post_tag', 'product_tag', 'product_shipping_class' ), true ) ) {
		return true;
	}
	return $excluded;
}, 10, 2 );

/**
 * Drop author-sitemap.xml entirely, matching the is_author() noindex above.
 * Yoast's author sitemap provider bails out of get_index_links() when the
 * filtered user list comes back empty, so returning an empty array removes the
 * whole author sitemap from sitemap_index.xml rather than leaving an empty one
 * behind (verified against wordpress-seo
 * inc/sitemaps/class-author-sitemap-provider.php on production 2026-08-01).
 * Without this the author URLs would keep being listed while serving noindex -
 * the same contradiction the recap-post exclusion above exists to prevent.
 */
add_filter( 'wpseo_sitemap_exclude_author', '__return_empty_array' );

/**
 * Keep the two junk product categories (noindexed in the robots filter above)
 * out of the product_cat XML sitemap, so they don't read as a
 * noindex-in-sitemap contradiction. Slugs resolved to term IDs.
 */
add_filter( 'wpseo_exclude_from_sitemap_by_term_ids', function ( $excluded ) {
	foreach ( array( 'uncategorised', '27' ) as $slug ) {
		$t = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $t ) {
			$excluded[] = (int) $t->term_id;
		}
	}
	return $excluded;
} );

/**
 * Restore the WooCommerce archive H1 on the Shop and product-category archives.
 * The treattrunk-welcome-box plugin registers a blanket
 * add_filter('woocommerce_show_page_title','__return_false') at priority 10,
 * which strips the <h1> from EVERY Woo archive - so /shop/ and every
 * /product-category/ page shipped with no H1 at all (an SEO crawl error, and no
 * visible page heading either). This higher-priority filter re-enables the
 * title only where it's genuinely wanted: the shop page (its money page) and
 * product category archives. Runs at priority 20 so it wins over the plugin's
 * priority-10 false. Left alone everywhere else (e.g. tag archives, now
 * noindexed above), so this is a targeted restore, not a global revert.
 */
add_filter( 'woocommerce_show_page_title', function ( $show ) {
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_category() ) ) {
		return true;
	}
	return $show;
}, 20 );

/**
 * Give blog category archives an H1. They render through a shared Elementor
 * Archive template (post 7001) that has only a search box + posts grid and no
 * title widget, so /category/recipes/, /interviews/, /healthy-living/ and
 * /past-boxes/ all shipped with no <h1> (an SEO crawl error). Rather than
 * hand-edit that shared template's _elementor_data (high blast radius across
 * every archive), print a proper H1 with the category name in the archive
 * content area, just before Elementor prints the location. Scoped to real
 * category archives: tag archives are noindexed above, and product archives get
 * their H1 from the woocommerce_show_page_title restore. Inherits the site's
 * global `body h1` styling (DM Sans / teal) from custom.css; the inline layout
 * keeps it inside the same 1140px content measure as the grid below it.
 */
add_action( 'elementor/theme/before_do_archive', function () {
	if ( is_category() ) {
		printf(
			'<h1 class="tt-archive-title" style="max-width:1140px;margin:28px auto 4px;padding:0 20px;">%s</h1>',
			esc_html( single_cat_title( '', false ) )
		);
	}
} );

/**
 * Keep the thin monthly recap posts (tt_thin_recap_slugs - 49 of them) out of
 * the /blog/ listing.
 *
 * They are already noindex, so they contribute nothing to search, but they were
 * still filling the main blog loop: at 10 per page they stretched /blog/ to 11
 * pages and pushed genuinely useful posts as deep as /blog/page/9/. Ahrefs
 * reported two of them (the labour-snacks and CBD posts) as orphans for exactly
 * this reason - they ARE linked, just from pagination too deep for the crawler
 * to reach, which is functionally the same thing for ranking.
 *
 * Scoped to the main blog listing only ( $query->is_home() ), so
 * /category/past-boxes/ still lists every recap post and remains their home -
 * this hides them from the front door, it does not delete or unlink them.
 *
 * Side effect to expect: /blog/ drops from 11 pages to ~6, so /blog/page/7/
 * through /page/11/ will start returning 404. They carried no traffic and
 * Google drops empty pagination on its own.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_home() ) {
		return;
	}

	$ids = tt_thin_recap_post_ids();
	if ( ! $ids ) {
		return;
	}

	$query->set( 'post__not_in', array_merge( (array) $query->get( 'post__not_in' ), $ids ) );
} );

/**
 * Category navigation on the blog listing and category archives.
 *
 * The shared Elementor Archive template (post 7001) has only a search box and a
 * posts grid, and /blog/ linked exactly one category (/category/past-boxes/),
 * so /category/recipes/ and /category/interviews/ had no incoming internal
 * links at all and crawled as orphans. Printing the nav on category archives
 * too means every category is reachable from every other one, which keeps them
 * linked even if the blog listing template changes later.
 *
 * Rendered in PHP rather than by editing the shared template's _elementor_data,
 * matching the H1 restore above - same reasoning, much lower blast radius.
 */
add_action( 'elementor/theme/before_do_archive', function () {
	if ( ! is_home() && ! is_category() ) {
		return;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'hide_empty' => true,
		)
	);
	if ( is_wp_error( $terms ) || count( $terms ) < 2 ) {
		return;
	}

	$current = is_category() ? (int) get_queried_object_id() : 0;

	// The colours carry !important because the site's global stylesheet forces
	// its teal on every archive <a> with !important of its own. Without it the
	// current-category pill rendered teal-on-teal (computed rgb(18,120,108) text
	// on rgb(13,107,98) background - a ~1.05:1 contrast ratio, i.e. invisible).
	// Verified on production 2026-08-01.
	echo '<nav class="tt-cat-nav" aria-label="Blog categories" style="max-width:1140px;margin:8px auto 0;padding:0 20px;display:flex;flex-wrap:wrap;gap:10px;">';
	foreach ( $terms as $term ) {
		$is_current = ( $term->term_id === $current );
		printf(
			'<a href="%s"%s style="font-size:14px;line-height:1;padding:9px 14px;border:1px solid %s;border-radius:999px;text-decoration:none;color:%s !important;background:%s !important;">%s</a>',
			esc_url( get_term_link( $term ) ),
			$is_current ? ' aria-current="page"' : '',
			$is_current ? '#0d6b62' : '#cfdedb',
			$is_current ? '#ffffff' : '#0d6b62',
			$is_current ? '#0d6b62' : 'transparent',
			esc_html( $term->name )
		);
	}
	echo '</nav>';
}, 20 );

/**
 * Company registration line - a standard UK ecommerce trust signal that
 * was missing entirely. Real details confirmed live on Companies House
 * (company 15624707) 2026-07-16. Echoed via wp_footer rather than added to
 * the Elementor-built global Footer template (post 173), to avoid another
 * direct _elementor_data edit - same reasoning as the copyright-year fix
 * above.
 */
/**
 * Site-wide footer links for two pages that had no incoming internal links at
 * all and crawled as orphans: /freebox/ (the Free Box offer) and
 * /affiliate-home/ (the Store Affiliates portal).
 *
 * The obvious fix - adding them to the "Information" WP menu - does nothing
 * here: the Elementor footer template renders its link column as hardcoded
 * elementor-icon-list widgets, NOT a nav-menu widget, so the Information menu
 * is not actually used anywhere on the front end despite listing the same URLs.
 * Verified on production 2026-08-01 (no menu-item-* classes render in the
 * footer). Printing them via wp_footer instead of editing the footer template's
 * _elementor_data matches the company-registration line below - same reasoning,
 * much lower blast radius, and revertible by deleting this block.
 *
 * /email-offer/ is deliberately excluded: it has no H1 and its title tag is
 * just "Email Offer", so a link would clear the crawl error without giving it
 * anything to rank on. It needs real content first.
 */
add_action( 'wp_footer', function () {
	// Three corporate pages, one intent each (2026-09-13): commercial anchors
	// match each page's primary keyword - see docs/corporate-seo-pages-2026-09.md.
	$links = array(
		'/freebox/'                     => 'Free Box Offer',
		'/affiliate-home/'              => 'Affiliate Portal',
		'/office-snack-boxes/'          => 'Office Snack Boxes',
		'/corporate-orders/'            => 'Corporate Snack Gifts',
		'/corporate-christmas-hampers/' => 'Corporate Christmas Hampers',
	);

	$out = array();
	foreach ( $links as $path => $label ) {
		$out[] = sprintf(
			'<a href="%s" style="color:#0d6b62;text-decoration:none;">%s</a>',
			esc_url( home_url( $path ) ),
			esc_html( $label )
		);
	}

	echo '<p style="text-align:center;font-size:12px;color:#8a8a8a;padding:10px 20px 0;margin:0;">'
		. implode( ' &middot; ', $out )
		. '</p>';
}, 29 );

add_action( 'wp_footer', function () {
	echo '<p style="text-align:center;font-size:12px;color:#8a8a8a;padding:10px 20px;margin:0;">'
		. 'Treat Trunk Ltd &middot; Company No. 15624707 &middot; Registered office: 86-90 Paul Street, London, EC2A 4NE'
		. '</p>';
}, 30 );

/**
 * Corporate cross-links from the two Elementor-built pages in the linking map
 * (docs/corporate-seo-pages-2026-09.md, Task 4) that cannot take a plain
 * content edit: /send-a-gift/ (7013) gets a "Buying for a team?" block
 * appended to its Elementor output, and the homepage gets a seasonal
 * announcement bar until the last Christmas posting deadline. Both print
 * server-side rather than editing _elementor_data (same reasoning as the
 * footer links above) and are removed by deleting this block.
 */
add_filter( 'elementor/frontend/the_content', function ( $content ) {
	if ( ! in_the_loop() ) {
		return $content;
	}
	$link      = 'color:#12786C;font-weight:700;text-decoration:underline;text-underline-offset:3px;';
	$xmas_open = time() < strtotime( '2026-12-18 00:00:00 Europe/London' );

	if ( is_page( 7013 ) ) {
		$xmas  = $xmas_open
			? ' For December, our <a href="' . esc_url( home_url( '/corporate-christmas-hampers/' ) ) . '" style="' . $link . '">corporate Christmas hampers</a> are alcohol-free as standard.'
			: '';
		$block = '<section class="tt-gift-corporate-links" style="max-width:1100px;margin:8px auto 40px;padding:22px 26px;background:#FFFFFF;border:1px solid #DCEBE9;border-radius:20px;font-family:\'DM Sans\',-apple-system,sans-serif;font-size:15.5px;line-height:1.65;color:#1B2420;">'
			. '<strong>Buying for a team?</strong> Our <a href="' . esc_url( home_url( '/corporate-orders/' ) ) . '" style="' . $link . '">employee gift boxes and client gifts</a> come with branded cards, no minimum order and invoicing, posted to one office or to every home address.'
			. $xmas
			. '</section>';
		return $content . $block;
	}

	// Homepage: seasonal bar directly under the header (the Hello Elementor
	// header template never calls wp_body_open, so this filter is the hook).
	if ( is_front_page() && $xmas_open ) {
		$bar = '<div class="tt-xmas-bar" style="background:#0B5951;color:#FAFAF8;text-align:center;padding:9px 16px;font-size:13.5px;font-weight:600;font-family:\'DM Sans\',-apple-system,sans-serif;">'
			. 'Corporate Christmas 2026 is open: branded orders close Fri 20 Nov &nbsp;&middot;&nbsp; '
			// Inline !important: site-modernize.css paints every <a> teal with
			// !important, which was rendering this link teal-on-dark-green.
			. '<a href="' . esc_url( home_url( '/corporate-christmas-hampers/' ) ) . '" style="color:#FAFAF8 !important;font-weight:700;text-decoration:underline;text-underline-offset:3px;">corporate Christmas hampers</a>'
			. '</div>';
		return $bar . $content;
	}

	return $content;
}, 20 );

/**
 * 301 for the Christmas page's previous slug. WordPress' own old-slug
 * redirect only fires for post-type queries (`name`), not page requests
 * (`pagename`), so /corporate-christmas-gifting/ was a 404 after the
 * 2026-09-13 re-slug despite the _wp_old_slug meta being in place.
 */
add_action( 'template_redirect', function () {
	$path = trim( wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
	if ( 'corporate-christmas-gifting' === $path ) {
		wp_redirect( home_url( '/corporate-christmas-hampers/' ), 301 );
		exit;
	}
}, 1 );

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

	// Structured fields added 2026-09-13 with the shared quote form
	// (corporate-ui/templates/parts/quote-form.php). All optional; only the
	// ones the enquirer filled in are listed in the email.
	$extra_fields = array(
		'company'   => 'Company',
		'headcount' => 'Headcount',
		'gifts'     => 'Gifts',
		'box'       => 'Box',
		'cadence'   => 'One-off / subscription',
		'addresses' => 'Addresses',
		'branding'  => 'Branding',
		'week'      => 'Delivery week',
		'occasion'  => 'Occasion / date',
	);
	$details = '';
	foreach ( $extra_fields as $field => $label ) {
		$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
		if ( '' !== $value ) {
			$details .= str_pad( $label . ':', 24 ) . $value . "\n";
		}
	}
	$source = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : 'corporate-orders';
	$source_labels = array(
		'corporate-orders'            => 'corporate gifting',
		'office-snack-boxes'          => 'office snack box',
		'corporate-christmas-hampers' => 'corporate Christmas hamper',
	);
	$source_label = isset( $source_labels[ $source ] ) ? $source_labels[ $source ] : 'corporate';

	$name    = trim( $firstname . ' ' . $lastname );
	$subject = 'New ' . $source_label . ' enquiry: ' . $name . ( '' !== $details && false !== strpos( $details, 'Company:' ) ? ' (' . sanitize_text_field( wp_unslash( $_POST['company'] ) ) . ')' : '' );
	$body    = "New " . $source_label . " enquiry from the /" . $source . "/ quote form.\n\n"
		. "Name:  $name\n"
		. "Email: $email\n"
		. ( '' !== $details ? "\n" . $details : '' )
		. "\nMessage:\n" . ( $message !== '' ? $message : '(none provided)' ) . "\n\n"
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

/**
 * Subscriptions: ship the welcome box on the initial order (added 2026-09-13).
 *
 * Every subscription variation is synced to the 28th with proration off, so
 * unless the customer buys on the sync day WooCommerce Subscriptions marks
 * the cart item as a one-period free trial
 * (WC_Subscriptions_Synchroniser::maybe_set_free_trial) so that only the
 * sign-up fee is charged now. Two side effects of that "free trial" then
 * remove shipping from the initial order entirely, even though the
 * sign-up-fee welcome box / gift box ships immediately:
 *   1. WC_Subscriptions_Cart::charge_shipping_up_front() says "everything is
 *      on a free trial, don't charge shipping now";
 *   2. WC_Subscriptions_Cart::set_cart_shipping_packages() (priority -10 on
 *      woocommerce_cart_shipping_packages) strips every trial item out of the
 *      initial shipping packages, so no rates are calculated at all.
 * The order is therefore created with NO shipping line. Royal Mail Click &
 * Drop silently drops any order without a shipping line, so none of these
 * welcome boxes ever reached it (every new-subscription checkout order since
 * at least Sep 2025, e.g. #54943). Renewal orders are unaffected: they
 * inherit the subscription's recurring shipping line.
 *
 * Gated on a non-zero sign-up fee so the "wait for the next box" option
 * (sign-up fee 0, nothing ships now) stays shipping-free. The UK zone offers
 * Free shipping with no minimum, so customers still normally pay £0; this
 * only makes the checkout pick a method and record it on the order.
 */
function tt_welcome_box_ships_now( $product ) {
	return $product instanceof WC_Product
		&& class_exists( 'WC_Subscriptions_Product' )
		&& WC_Subscriptions_Product::is_subscription( $product )
		&& $product->needs_shipping()
		&& (float) WC_Subscriptions_Product::get_sign_up_fee( $product ) > 0;
}

// 1. Charge shipping up front when a welcome box ships now.
add_filter( 'woocommerce_subscriptions_cart_shipping_up_front', function ( $up_front ) {
	if ( $up_front || ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $up_front;
	}
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( isset( $cart_item['data'] ) && tt_welcome_box_ships_now( $cart_item['data'] ) ) {
			return true;
		}
	}
	return $up_front;
} );

// 2. Put the welcome box back into the initial shipping packages after
//    Subscriptions strips it. Snapshot the packages before WCS runs (-10),
//    then restore the qualifying items afterwards (0).
function tt_welcome_box_package_store( $packages = null ) {
	static $store = array();
	if ( null !== $packages ) {
		$store = $packages;
	}
	return $store;
}
add_filter( 'woocommerce_cart_shipping_packages', function ( $packages ) {
	tt_welcome_box_package_store( is_array( $packages ) ? $packages : array() );
	return $packages;
}, -20 );
add_filter( 'woocommerce_cart_shipping_packages', function ( $packages ) {
	if ( ! is_array( $packages ) || ! class_exists( 'WC_Subscriptions_Cart' ) || 'none' !== WC_Subscriptions_Cart::get_calculation_type() ) {
		return $packages;
	}
	foreach ( tt_welcome_box_package_store() as $index => $original ) {
		if ( empty( $original['contents'] ) || ! is_array( $original['contents'] ) ) {
			continue;
		}
		foreach ( $original['contents'] as $key => $cart_item ) {
			if ( isset( $packages[ $index ]['contents'][ $key ] ) ) {
				continue;
			}
			if ( empty( $cart_item['data'] ) || ! tt_welcome_box_ships_now( $cart_item['data'] ) ) {
				continue;
			}
			if ( ! isset( $packages[ $index ] ) ) {
				$packages[ $index ]                  = $original;
				$packages[ $index ]['contents']      = array();
				$packages[ $index ]['contents_cost'] = 0;
			}
			$packages[ $index ]['contents'][ $key ] = $cart_item;
			$packages[ $index ]['contents_cost']   += isset( $cart_item['line_total'] ) ? (float) $cart_item['line_total'] : 0;
		}
	}
	return $packages;
}, 0 );

/**
 * Gift (block) subscriptions: N boxes in total, not N + 1.
 *
 * The 3 / 6 / 12 Month Gift Subscriptions (7009 / 7185 / 37760) are synced
 * subscriptions with the whole price as the sign-up fee, £0 renewals and a
 * length of N months. Since the shipping fix above, the initial order ships a
 * box straight away, so the subscription itself must only produce N - 1
 * renewal boxes.
 *
 * WooCommerce Subscriptions sets the end date to first renewal + N months, and
 * the AutomateWoo workflow "End Expired Block Subs" (#38783) expires the
 * subscription 27 days before that end date, i.e. a few days after the Nth
 * renewal. Net result before this hook: N renewals + the immediate box.
 *
 * Pull the end date back by one billing period, counting from the first
 * renewal date WCS has already scheduled on the subscription. Counting from
 * the real next-payment date (rather than lowering the product's length) is
 * right on every day of the month: bought on the sync day itself, the initial
 * order already counts as the first billing period and the first renewal is a
 * month later, so length - 1 would come up one box short there.
 *   end = next_payment + (N - 1) periods  →  AutomateWoo expires the
 *   subscription a few days after renewal N - 1.
 */
add_action( 'woocommerce_checkout_subscription_created', function ( $subscription ) {
	if ( ! $subscription instanceof WC_Subscription || ! class_exists( 'WC_Subscriptions_Product' ) || ! function_exists( 'wcs_add_time' ) ) {
		return;
	}
	$next_payment = (int) $subscription->get_time( 'next_payment' );
	if ( $next_payment <= 0 ) {
		return;
	}
	foreach ( $subscription->get_items() as $item ) {
		$product = $item->get_product();
		if ( ! tt_welcome_box_ships_now( $product ) ) {
			continue;
		}
		$length = (int) WC_Subscriptions_Product::get_length( $product );
		if ( $length < 2 ) {
			continue; // open-ended monthly subscriptions (length 0) are untouched
		}
		$period   = WC_Subscriptions_Product::get_period( $product );
		$interval = max( 1, (int) WC_Subscriptions_Product::get_interval( $product ) );
		$new_end  = (int) wcs_add_time( ( $length - 1 ) * $interval, $period, $next_payment );
		$old_end  = (int) $subscription->get_time( 'end' );
		if ( $new_end <= $next_payment || ( $old_end > 0 && $new_end >= $old_end ) ) {
			continue; // never extend, never end before the first renewal
		}
		try {
			$subscription->update_dates( array( 'end' => gmdate( 'Y-m-d H:i:s', $new_end ) ) );
			$subscription->add_order_note( sprintf(
				'End date set to %s: the first box ships with the initial order, so %d monthly renewals complete this %d-box gift subscription.',
				gmdate( 'j M Y', $new_end ),
				$length - 1,
				$length
			) );
		} catch ( Exception $e ) {
			error_log( 'site-core gift subscription end date: ' . $e->getMessage() );
		}
		break;
	}
}, 10, 1 );
