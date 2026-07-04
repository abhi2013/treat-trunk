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
 * get an automatic per-unit discount based on cart quantity. No coupon code
 * required - this removes the "email us for pricing" friction for the common
 * bulk-to-one-address case.
 *
 * Tiers, off the real unit price of £15.99 (confirmed 2026-07-04):
 *   1-19 units: full price
 *   20-49 units: 15% off (~£13.59/box)
 *   50+ units:   20% off (~£12.79/box)
 */
define( 'TT_BULK_LETTERBOX_PRODUCT_ID', 40245 );

function tt_bulk_letterbox_discount_percent( int $quantity ): float {
	if ( $quantity >= 50 ) {
		return 0.20;
	}
	if ( $quantity >= 20 ) {
		return 0.15;
	}
	return 0.0;
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

		$product  = $cart_item['data'];
		$quantity = (int) $cart_item['quantity'];
		$discount = tt_bulk_letterbox_discount_percent( $quantity );

		if ( $discount > 0 ) {
			$base_price = (float) $product->get_regular_price();
			$product->set_price( round( $base_price * ( 1 - $discount ), 2 ) );
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
		. 'Ordering to one address? <strong>20+ boxes: 15% off.</strong> <strong>50+ boxes: 20% off.</strong> Discount applies automatically in your cart.'
		. '</p>';
}, 25 );
