<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $shipping_dates;

// GET SHIPPING DATE
if ( $product->is_type( 'variable' ) ) {
	$variations =  $product->get_available_variations();
}
else
	$variations = array();

/*if (get_post_meta(get_the_ID(), 'welcome_box_option', true) != 1) {
?>
<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) );?>"><?php echo $product->get_price_html(); ?></p>
<?php
} else*/ {


$shipping_dates = array();
//print_r($variations);
foreach($variations as $variation) {
	 if (isset($variation['attributes']['attribute_welcome-box']) && !empty($variation['attributes']['attribute_welcome-box']) && strpos($variation['attributes']['attribute_welcome-box'], 'Welcome') !== false)
		 $shipping_dates[$variation['variation_id']] = date('j F', strtotime('+1 day'));
	 else {
		 $date = WC_Subscriptions_Synchroniser::get_products_first_payment_date( wc_get_product( array_values($variations)[0]['variation_id'] ) );
		 $date = str_replace('!', '', str_replace('First payment: ', '', wp_strip_all_tags($date)));
		 $shipping_dates[$variation['variation_id']] = date('jS F', strtotime($date));
		 //echo $shipping_dates[$variation['variation_id']].'<br/>';
	 }
}
?>
<?php if (get_post_meta(get_the_ID(), 'welcome_box_option', true) == 1 && !isset($_GET['switch-subscription'])) { ?>
 <p class="next_box">Your next box will be the 
<?php foreach ($shipping_dates as $id => $shipping_date) { ?>
<span class="next_shipping_date date_<?php echo $id; ?>"><?php echo date('F', strtotime(end($shipping_dates) . ' + 27 days')); ?></span>
<?php } ?>
 Box</p>

<p class="welcome_box">Don’t want to wait? Fear not, you have the option to receive a 
<a href="">mystery welcome box</a> first, or you can just wait for the next one.</p>

<p><a href="#" class="hidden_link">What's in the mystery welcome box?</a> <i class="fas fa-caret-down"></i></p>
<p class="hidden_paragraph">The mystery box contains a suprise selection of 22-25 snacks or 12-15 snacks if you choose the mini!</p>
<script id="tt-mystery-box-toggle">
	jQuery(function(){
		jQuery('.hidden_link').click(function(e){
			e.preventDefault();
			jQuery(this).toggleClass('show');
			jQuery('.hidden_paragraph').toggleClass('show');
		});
	});
</script>
<?php } ?>
<?php }