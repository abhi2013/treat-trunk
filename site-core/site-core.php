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
