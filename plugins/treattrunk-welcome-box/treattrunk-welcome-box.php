<?php
/**
 * Plugin Name: Treat Trunk Welcome Box
 * Version: 1.0.0
 * Author: Emily Tyler
 */
 
// WC RADIO BUTTONS

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'woovr_init' ) ) {
	add_action( 'plugins_loaded', 'woovr_init', 11 );

	function woovr_init() {
		// load text-domain
		load_plugin_textdomain( 'wpc-variations-radio-buttons', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0.0', '>=' ) ) {
			add_action( 'admin_notices', 'woovr_notice_wc' );

			return;
		}

		if ( ! class_exists( 'WPclever_Woovr' ) ) {
			class WPclever_Woovr {
				function __construct() {
					// product data tabs
					//add_filter( 'woocommerce_product_data_tabs', array( $this, 'woovr_product_data_tabs' ), 10, 1 );
					//add_action( 'woocommerce_product_data_panels', array( $this, 'woovr_product_data_panels' ) );
					add_action( 'wp_enqueue_scripts', array( $this, 'woovr_enqueue_scripts' ), 99 );

					// functions
					add_action( 'woocommerce_before_variations_form', array( $this, 'woovr_before_variations_form' ) );
				}

				function woovr_before_variations_form() {
					global $product;

					$variations =  $product->get_available_variations();
					if (get_post_meta(get_the_ID(), 'welcome_box_option', true) != 1 || isset($_GET['switch-subscription'])) {
						remove_action( 'wp_enqueue_scripts', array( $this, 'woovr_enqueue_scripts' ), 99 );
						if (get_post_meta(get_the_ID(), 'welcome_box_option', true) == 1 && isset($_GET['switch-subscription'])) {
							?><style>table.variations{display:none;}</style>
							<script>//jQuery(function(){ jQuery('.table.variations select').val(jQuery('.table.variations select option').last().attr('value')) });</script><?php
						}
						return;
					}
			
					$this->woovr_variations_form( $product );
				}

				function woovr_enqueue_scripts() {
					//wp_enqueue_style( 'woovr-frontend', plugin_dir_url( __FILE__ ).'frontend.css' );
					wp_enqueue_script( 'woovr-frontend', plugin_dir_url( __FILE__ ).'frontend.js', array( 'jquery' ), false, true );
				}

				public static function woovr_variations_form( $product ) {
					
					$variations =  $product->get_available_variations();
					if (get_post_meta(get_the_ID(), 'welcome_box_option', true) != 1 || isset($_GET['switch-subscription'])) {
						remove_action( 'wp_enqueue_scripts', array( $this, 'woovr_enqueue_scripts' ), 99 );
						return;
					}
					?><style>
					<?php include 'frontend.css'; ?>
					</style>
					<?php 

					global $shipping_dates;
					$product_id = $product->get_id();

					{
						$woovr_children = $product->get_children();
						if ( is_array( $woovr_children ) && count( $woovr_children ) > 0 ) {
							?><div class="woovr-variations <?php esc_attr( 'woovr-variations-default' );?>"><?php
								$count = 0;
								$option = 1;
								$headers = array("I don't want to wait!", "I'll wait for the next one");
								$next_month = '';
								$descriptions = array('Pay and receive your welcome box now. Your first subscription box will be charged on '.end($shipping_dates).' and posted around the '.date('jS F', strtotime(end($shipping_dates) . ' + 10 day')).'. Any first month offers will be deducted from the first subscription payment, not the welcome box.', 'Create your account now and pay for your first suscription box on '.end($shipping_dates).'. Your first box will be posted around the '.date('jS F', strtotime(end($shipping_dates) . ' + 10 day')).' and around the 10th of each following month.');
								foreach ( $woovr_children as $i => $woovr_child ) {
									$count++;
									if (count($woovr_children) > 1 && (count($woovr_children) - $count >= 2 || count($woovr_children) - $count == 0)) {
										?><div class="option">
											<div class="option_header"><i class="fas fa-circle"></i> Option <?php echo $option . (isset($headers[$option-1]) ? ': <span>'.$headers[$option-1].'</span>':'');?></div>
											<?php echo isset($descriptions[$option-1]) ? '<div class="option_description">'.$descriptions[$option-1].'</div>':'';?><?php
										$option++;
									}
									$woovr_child_product = wc_get_product( $woovr_child );
									if ( ! $woovr_child_product ) {
										continue;
									}
									$woovr_child_attrs = htmlspecialchars( json_encode( $woovr_child_product->get_variation_attributes() ), ENT_QUOTES, 'UTF-8' );
?>
									<div class="woovr-variation woovr-variation-radio" data-id="<?php echo $woovr_child; ?>" data-price="<?php echo  wc_get_price_to_display( $woovr_child_product ); ?>" data-purchasable="<?php echo (!$woovr_child_product->is_in_stock() || ! $woovr_child_product->is_purchasable() ? 'no' : 'yes'); ?>" data-attrs="<?php echo $woovr_child_attrs; ?>">
										<div class="woovr-variation-selector"><input type="radio" name="woovr_variation_<?php echo $product->get_id(); ?>" style="display:none;"/><i class="fas fa-check-circle"></i></div>
										<div class="woovr-variation-name"><?php echo explode(': ',$woovr_child_product->get_attribute_summary())[1]//apply_filters( 'woovr_variation_name', $woovr_child_product->get_name(), $woovr_child_product ); ?></div>
										<div class="woovr-variation-description"><?php echo apply_filters( 'woovr_variation_description', $woovr_child_product->get_description(), $woovr_child_product ); ?></div>
										<div class="woovr-variation-price">&pound;<?php echo apply_filters( 'woovr_variation_price', $woovr_child_product->get_sign_up_fee(), $woovr_child_product ); ?></div>
									</div>
									<?php
									if (count($woovr_children) > 1 && (count($woovr_children) - $count == 1 || count($woovr_children) - $count == 0)) {
										?></div><?php
									}
								}
							?></div><?php
						}
					}
					
				}
			}

			new WPclever_Woovr();
		}
	}
}

// WELCOME BOX PRICE VALUES
add_action( 'woocommerce_product_options_advanced', 'admin_edit_product_fields_custom' );
function admin_edit_product_fields_custom() {
	global $post;
?>
	<div class="options_group welcome-box-price">
	<?php
	woocommerce_wp_select(
		array(
			'id'                => 'welcome_box_option',
			'value'             => get_post_meta( get_the_ID(), 'welcome_box_option', true ),
			'label'             => __( 'Welcome Box Option', 'woocommerce' ),
			'type'              => 'dropdown',
	        'options' =>  array('No', 'Yes')
		)
	);
	?>
	</div>
	<div class="options_group welcome-box-price">
	<?php
	woocommerce_wp_text_input(
		array(
			'id'                => 'gift_wrapping_costs',
			'value'             => get_post_meta( get_the_ID(), 'gift_wrapping_costs', true ),
			'label'             => __( 'Gift Wrapping Costs', 'woocommerce' ),
		)
	);
	?>
	</div>
	<?php
}

add_action( 'woocommerce_process_product_meta', 'admin_save_product_fields_custom', 10, 2 );
function admin_save_product_fields_custom( $id, $post ){
	if( !empty( $_POST['welcome_box_option'] ) )
		update_post_meta( $id, 'welcome_box_option', $_POST['welcome_box_option'] );
	if( !empty( $_POST['gift_wrapping_costs'] ) )
		update_post_meta( $id, 'gift_wrapping_costs', $_POST['gift_wrapping_costs'] );
}
/*
// WELCOME BOX OPTIONS
add_action( 'woocommerce_before_add_to_cart_button', 'output_woocommerce_product_field', 10 );
function output_woocommerce_product_field() {
    global $product;
 
//    if ( $product->get_id() !== 1741 )
//        return;
 
    ?>
	<div class="options">
		<label for="mini" class="mini">
			<input type="radio" id="mini" name="box_option" value="mini">
			Mini Welcome Box
		</label>
		<label for="standard" class="standard">
			<input type="radio" id="standard" name="box_option" value="standard">
			Standard Welcome Box
		</label>
		<label for="none" class="none">
			<input type="radio" id="none" name="box_option" value="none">
			Option 2: I'll wait for the next one
		</label>
	</div>
    <?php
} 
add_filter( 'woocommerce_add_cart_item_data', 'process_woocommerce_cart_item_field', 10, 3 );
function process_woocommerce_cart_item_field( $cart_item_data, $product_id, $variation_id ) {
    $option = $_POST['box_option'];
 
    if ( empty( $option ) )
        return $cart_item_data;
 
    $cart_item_data['box_option'] = $option;
 
    return $cart_item_data;
}
add_filter( 'woocommerce_get_item_data', 'display_woocommerce_cart_item_field', 100, 2 );
function display_woocommerce_cart_item_field( $item_data, $cart_item ) {
    if ( empty( $cart_item['box_option'] ) ) {
        return $item_data;
    }
 
    $item_data[] = array(
        'key'     => 'Selected Option',
        'value'   => wc_clean( $cart_item['box_option'] ),
        'display' => '',
    );
 
    return $item_data;
}
//apply_filters( 'woocommerce_subscriptions_payment_upfront', $is_upfront, $product );


add_filter( 'woocommerce_subscriptions_payment_upfront', function($is_upfront, $product){
	return $is_upfront;
}, 10, 2); 
*/

add_filter( 'woocommerce_subscriptions_product_price_string', function($subscription_string, $product, $include){
		global $wp_locale;
		$WC_Product_Data_Store_CPT = new WC_Product_Data_Store_CPT();

		$include = wp_parse_args( $include, array(
				'tax_calculation'     => get_option( 'woocommerce_tax_display_shop' ),
				'subscription_price'  => true,
				'subscription_period' => true,
				'subscription_length' => true,
				'sign_up_fee'         => true,
				'trial_length'        => true,
			)
		); 

		$include = apply_filters( 'woocommerce_subscriptions_product_price_string_inclusions', $include, $product );

		$base_price = WC_Subscriptions_Product::get_price( $product );

		if ( true === $include['sign_up_fee'] ) {
			$sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $product );
		} elseif ( false !== $include['sign_up_fee'] ) { // Allow override of product's sign-up fee
			$sign_up_fee = $include['sign_up_fee'];
		} else {
			$sign_up_fee = 0;
		}

		if ( false != $include['tax_calculation'] ) {

			if ( in_array( $include['tax_calculation'], array( 'exclude_tax', 'excl' ) ) ) { // Subtract Tax

				if ( isset( $include['price'] ) ) {
					$price = $include['price'];
				} else {
					$price = wcs_get_price_excluding_tax( $product );
				}

				if ( true === $include['sign_up_fee'] ) {
					$sign_up_fee = wcs_get_price_excluding_tax( $product, array( 'price' => WC_Subscriptions_Product::get_sign_up_fee( $product ) ) );
				}
			} else { // Add Tax

				if ( isset( $include['price'] ) ) {
					$price = $include['price'];
				} else {
					$price = wcs_get_price_including_tax( $product );
				}

				if ( true === $include['sign_up_fee'] ) {
					$sign_up_fee = wcs_get_price_including_tax( $product, array( 'price' => WC_Subscriptions_Product::get_sign_up_fee( $product ) ) );
				}
			}
		} else {

			if ( isset( $include['price'] ) ) {
				$price = $include['price'];
			} else {
				$price = wc_price( $base_price );
			}
		}

		$price .= ' <span class="subscription-details">';

		$billing_interval    = WC_Subscriptions_Product::get_interval( $product );
		$billing_period      = WC_Subscriptions_Product::get_period( $product );
		$subscription_length = WC_Subscriptions_Product::get_length( $product );
		$trial_length        = WC_Subscriptions_Product::get_trial_length( $product );
		$trial_period        = WC_Subscriptions_Product::get_trial_period( $product );

		if ( is_numeric( $sign_up_fee ) ) {
			$sign_up_fee = wc_price( $sign_up_fee );
		}

		if ( $include['subscription_length'] ) {
			$ranges = wcs_get_subscription_ranges( $billing_period );
		}

		if ( $include['subscription_length'] && 0 != $subscription_length ) {
			$include_length = true;
		} else {
			$include_length = false;
		}

		$subscription_string = '';

		if ( $include['subscription_price'] && $include['subscription_period'] ) { // Allow extensions to not show price or billing period e.g. Name Your Price
			if ( $include_length && $subscription_length == $billing_interval ) {
				$subscription_string = $price; // Only for one billing period so show "$5 for 3 months" instead of "$5 every 3 months for 3 months"
			} elseif ( WC_Subscriptions_Synchroniser::is_product_synced( $product ) && in_array( $billing_period, array( 'week', 'month', 'year' ) ) ) {
				$subscription_string = '';

				// Include string for upfront payment.
				if ( WC_Subscriptions_Synchroniser::is_payment_upfront( $product ) ) {
					/* translators: %1$s refers to the price. This string is meant to prefix another string below, e.g. "$5 now, and $5 on March 15th each year" */
					$subscription_string = sprintf( __( '%1$s now, and ', 'woocommerce-subscriptions' ), $price );
				}

				$payment_day = WC_Subscriptions_Synchroniser::get_products_payment_day( $product );
				switch ( $billing_period ) {
					case 'week':
						$payment_day_of_week = WC_Subscriptions_Synchroniser::get_weekday( $payment_day );
						if ( 1 == $billing_interval ) {
							// translators: 1$: recurring amount string, 2$: day of the week (e.g. "$10 every Wednesday")
							$subscription_string .= sprintf( __( '%1$s every %2$s', 'woocommerce-subscriptions' ), $price, $payment_day_of_week );
						} else {
							// translators: 1$: recurring amount string, 2$: period, 3$: day of the week (e.g. "$10 every 2nd week on Wednesday")
							$subscription_string .= sprintf( __( '%1$s every %2$s on %3$s', 'woocommerce-subscriptions' ), $price, wcs_get_subscription_period_strings( $billing_interval, $billing_period ), $payment_day_of_week );
						}
						break;
					case 'month':
						if ( 1 == $billing_interval ) {
							if ( $payment_day > 27 ) {
								// translators: placeholder is recurring amount
								$subscription_string .= sprintf( __( '%s on the last day of each month', 'woocommerce-subscriptions' ), $price );
							} else {
								// translators: 1$: recurring amount, 2$: day of the month (e.g. "23rd") (e.g. "$5 every 23rd of each month")
								$subscription_string .= sprintf( __( '%1$s on the %2$s of each month', 'woocommerce-subscriptions' ), $price, WC_Subscriptions::append_numeral_suffix( $payment_day ) );
							}
						} else {
							if ( $payment_day > 27 ) {
								// translators: 1$: recurring amount, 2$: interval (e.g. "3rd") (e.g. "$10 on the last day of every 3rd month")
								$subscription_string .= sprintf( __( '%1$s on the last day of every %2$s month', 'woocommerce-subscriptions' ), $price, WC_Subscriptions::append_numeral_suffix( $billing_interval ) );
							} else {
								// translators: 1$: <price> on the, 2$: <date> day of every, 3$: <interval> month (e.g. "$10 on the 23rd day of every 2nd month")
								$subscription_string .= sprintf( __( '%1$s on the %2$s day of every %3$s month', 'woocommerce-subscriptions' ), $price, WC_Subscriptions::append_numeral_suffix( $payment_day ), WC_Subscriptions::append_numeral_suffix( $billing_interval ) );
							}
						}
						break;
					case 'year':
						if ( 1 == $billing_interval ) {
							// translators: 1$: <price> on, 2$: <date>, 3$: <month> each year (e.g. "$15 on March 15th each year")
							$subscription_string .= sprintf( __( '%1$s on %2$s %3$s each year', 'woocommerce-subscriptions' ), $price, $wp_locale->month[ $payment_day['month'] ], WC_Subscriptions::append_numeral_suffix( $payment_day['day'] ) );
						} else {
							// translators: 1$: recurring amount, 2$: month (e.g. "March"), 3$: day of the month (e.g. "23rd") (e.g. "$15 on March 15th every 3rd year")
							$subscription_string .= sprintf( __( '%1$s on %2$s %3$s every %4$s year', 'woocommerce-subscriptions' ), $price, $wp_locale->month[ $payment_day['month'] ], WC_Subscriptions::append_numeral_suffix( $payment_day['day'] ), WC_Subscriptions::append_numeral_suffix( $billing_interval ) );
						}
						break;
				}
			} else {
				// translators: 1$: recurring amount, 2$: subscription period (e.g. "month" or "3 months") (e.g. "$15 / month" or "$15 every 2nd month")
				$subscription_string = sprintf( _n( '%1$s / %2$s', '%1$s every %2$s', $billing_interval, 'woocommerce-subscriptions' ), $price, wcs_get_subscription_period_strings( $billing_interval, $billing_period ) );
			}
		} elseif ( $include['subscription_price'] ) {
			$subscription_string = $price;
		} elseif ( $include['subscription_period'] ) {
			// translators: billing period (e.g. "every week")
			$subscription_string = '<span class="subscription-details">' . sprintf( __( 'every %s', 'woocommerce-subscriptions' ), wcs_get_subscription_period_strings( $billing_interval, $billing_period ) );
		} else {
			$subscription_string = '<span class="subscription-details">';
		}

		// Add the length to the end
		if ( $include_length ) {
			// translators: 1$: subscription string (e.g. "$10 up front then $5 on March 23rd every 3rd year"), 2$: length (e.g. "4 years")
			$subscription_string = sprintf( __( '%1$s for %2$s', 'woocommerce-subscriptions' ), $subscription_string, $ranges[ $subscription_length ] );
		}

/*		if ( $include['trial_length'] && 0 != $trial_length ) {
			$trial_string = wcs_get_subscription_trial_period_strings( $trial_length, $trial_period );
			$subscription_string = sprintf( __( '%1$s with %2$s free trial', 'woocommerce-subscriptions' ), $subscription_string, $trial_string );
		}*/
		if (!($WC_Product_Data_Store_CPT->get_product_type(get_queried_object_id()) == 'grouped' && isset($_GET['switch-subscription'])))
			$subscription_string = sprintf( __( '%1$s starting %2$s', 'woocommerce-subscriptions' ), $subscription_string, str_replace('First payment: ', '', WC_Subscriptions_Synchroniser::get_products_first_payment_date( $product ) ));

		if ( $include['sign_up_fee'] && WC_Subscriptions_Product::get_sign_up_fee( $product ) > 0 ) {
			// translators: 1$: subscription string (e.g. "$15 on March 15th every 3 years for 6 years with 2 months free trial"), 2$: signup fee price (e.g. "and a $30 sign-up fee")
			$subscription_string = sprintf( __( '%2$s now, and %1$s', 'woocommerce-subscriptions' ), $subscription_string, $sign_up_fee );
		}
		
		if ( $include['sign_up_fee'] && WC_Subscriptions_Product::get_sign_up_fee( $product ) > 0 && WC_Subscriptions_Product::get_price($product) == '0' ) {
			// translators: 1$: subscription string (e.g. "$15 on March 15th every 3 years for 6 years with 2 months free trial"), 2$: signup fee price (e.g. "and a $30 sign-up fee")
			$subscription_string = sprintf( __( '%2$s', 'woocommerce-subscriptions' ), $subscription_string, $sign_up_fee );
		}

		$subscription_string .= '</span>';

		return $subscription_string;
}, 100, 3 );


add_action('plugins_loaded', function(){
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 25 );
	add_action( 'woocommerce_single_product_summary', function(){
		global $product;
		$price_html = $product->get_price_html();
		$price_html = format_from_price($price_html);
		?>
		<p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) );?>"><?php echo $price_html ?></p><?php
	}, 5 );
});

function format_from_price($price_html) {
	global $product;
	
	if(in_array($product->id, [221, 359, 360, 361, 7055, 7056, 7057, 7058])) {
		if (strpos($price_html, 'From: ') !== false) {
			if (strpos($price_html, ' now, and ') !== false) {
				$price_array = explode('From: ', $price_html);
				$price_html = 'From ' . $price_array[1];
			}
			$price_array = explode(' starting', $price_html);
			$price_html = $price_array[0];
			$price_html = str_replace(' / month', '', $price_html);
			$price_html = preg_replace('/ for [0-9] months/', '', $price_html);
			$price_html = str_replace('From:', 'From', $price_html);
		}
	}
	if(in_array($product->id, [7009, 7185, 37760])) {
		$price_array = explode('now', $price_html);
		$price_html = "From ".$price_array[0];
	}
	return $price_html;
}

add_action( 'elementor/page_templates/header-footer/before_content', 'header_title');
add_action( 'woocommerce_before_main_content', 'header_title');
function header_title(){
	if (!is_front_page()) {
	?>
	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<header class="page-header elementor">
			<?php 
			if ( is_home() )
				echo '<h1 class="entry-title">Blog</h1>';
			else if ( is_archive() ) {
				 if (is_category()) {
					 $title = single_cat_title('', false);
				 } elseif (is_tag()) {
					 $title = single_tag_title('', false);
				 } elseif (is_author()) {
					 the_post();
					 $title = '<a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta("ID"))) . '" title="' . esc_attr(get_the_author()) . '" rel="me">' . get_the_author() . '</a>';
					 rewind_posts();
				 } elseif (is_day()) {
					 $title = '<span>' . get_the_date() . '</span>';
				 } elseif (is_month()) {
					 $title = '<span>' . get_the_date('F Y');
				 } elseif (is_year()) {
					 $title = '<span>' . get_the_date('Y');
				 } else if (is_shop()) {
					$title = 'Shop';
				 } else {
					 $title = single_term_title('', false);
				 }
				echo '<h1 class="entry-title archive">'.$title.'</h1>';
			}
			else
				the_title( '<h1 class="entry-title else">', '</h1>' );
			?>
		</header>
	<?php endif; ?>
	
	<div class="elementor-section elementor-section-boxed">
		<div class="elementor-container">
			<?php
			if ( function_exists('yoast_breadcrumb') ) {
			  yoast_breadcrumb( '<p style="margin-top:8px;" id="breadcrumbs">','</p>' );
			}
			?>
		</div>
	</div>
	<?php
	}
}

add_filter('woocommerce_show_page_title',  function($flag) {
	return false;
}, 10, 1);

// ADDITIONAL GIFT WRAPPING AMOUNT
add_action( 'woocommerce_before_calculate_totals', 'add_custom_price', 20, 1);
function add_custom_price( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
        return;

    foreach ( $cart->get_cart() as $item ) {
        if (isset($item['thwepof_options']) && isset($item['thwepof_options']['gift_wrapping']) && $item['thwepof_options']['gift_wrapping']['value'] == 'Yes' && $price = get_post_meta($item['product_id'], 'gift_wrapping_costs', true))
		$item['data']->set_price( $item['data']->get_price() + floatval($price) );
    }
}

if ( ! function_exists( 'woocommerce_review_display_gravatar' ) ) {
function woocommerce_review_display_gravatar( $comment ) {
	if (email_exists($comment->comment_author_email))
		echo get_avatar( $comment, apply_filters( 'woocommerce_review_gravatar_size', '60' ), '' );
}
}

// SET NO WELCOME BOX FOR SUBSCRIPTION UPGRADE
add_filter( 'woocommerce_product_get_default_attributes' ,'custom_get_default_attributes', 10, 2 );
function custom_get_default_attributes( $value, $product ) {
	if (isset($_GET['switch-subscription']) && isset($value['welcome-box'])) {
		$value['welcome-box'] = "";
	}

    return $value;
}
