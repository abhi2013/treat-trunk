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
	})();
	</script>
	<?php
}, 20 );
