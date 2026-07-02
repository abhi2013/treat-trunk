<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '9.9.9' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_load_textdomain', [ true ], '2.0', 'hello_elementor_load_textdomain' );
		if ( apply_filters( 'hello_elementor_load_textdomain', $hook_result ) ) {
			load_theme_textdomain( 'hello-elementor', get_template_directory() . '/languages' );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_menus', [ true ], '2.0', 'hello_elementor_register_menus' );
		if ( apply_filters( 'hello_elementor_register_menus', $hook_result ) ) {
			register_nav_menus( array( 'menu-1' => __( 'Primary', 'hello-elementor' ) ) );
		}

		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_theme_support', [ true ], '2.0', 'hello_elementor_add_theme_support' );
		if ( apply_filters( 'hello_elementor_add_theme_support', $hook_result ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
				)
			);
			add_theme_support(
				'custom-logo',
				array(
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				)
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'editor-style.css' );

			/*
			 * WooCommerce.
			 */
			$hook_result = apply_filters_deprecated( 'elementor_hello_theme_add_woocommerce_support', [ true ], '2.0', 'hello_elementor_add_woocommerce_support' );
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', $hook_result ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$enqueue_basic_style = apply_filters_deprecated( 'elementor_hello_theme_enqueue_style', [ true ], '2.0', 'hello_elementor_enqueue_style' );
		$min_suffix          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', $enqueue_basic_style ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		wp_enqueue_style(
			'custom',
			get_template_directory_uri() . '/custom.css',
			array(),
			HELLO_ELEMENTOR_VERSION
		);
		wp_enqueue_script(
			'custom',
			get_template_directory_uri() . '/custom.js',
			array('jquery'),
			HELLO_ELEMENTOR_VERSION
		);
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		$hook_result = apply_filters_deprecated( 'elementor_hello_theme_register_elementor_locations', [ true ], '2.0', 'hello_elementor_register_elementor_locations' );
		if ( apply_filters( 'hello_elementor_register_elementor_locations', $hook_result ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check hide title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );


// Add share the meal text to theme customiser
function mytheme_customize_register( $wp_customize ) {

    $wp_customize->add_section( 'mytheme_company_section' , array(
        'title'      => __( 'Share The Meal Product text', 'mytheme' ),
        'priority'   => 30,
    ));

    $wp_customize->add_setting( 'mytheme_company-name', array());
    $wp_customize->add_control( new WP_Customize_Control(
        $wp_customize,
        'mytheme_company_control',
            array(
                'label'      => __( 'Share The Meal Product text', 'mytheme' ),
                'section'    => 'mytheme_company_section',
                'settings'   => 'mytheme_company-name',
                'priority'   => 1
            )
        )
    );

    // ..repeat ->add_setting() and ->add_control() for mytheme_company-division
}
add_action( 'customize_register', 'mytheme_customize_register' );




/**
 * Add a custom product data tab
 */
add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
function woo_new_product_tab( $tabs ) {
	
	// Adds the new tab
	
	$tabs['test_tab'] = array(
		'title' 	=> __( 'Share The Meal Charity', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'woo_new_product_tab_content'
	);

	return $tabs;

}


function woo_new_product_tab_content() {

	echo get_theme_mod( "mytheme_company-name" );
	
}


if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page();
	
}

//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );




// EXTRA DIVS
add_action( 'woocommerce_after_order_notes', function(){ echo '</div></div>'; }, 11 );



// How did you heatr about us in checkout
add_action( 'woocommerce_after_order_notes', 'how_did_you_hear_checkout_field', 12 );
function how_did_you_hear_checkout_field( $checkout ) {

    woocommerce_form_field( 'how_did_you_hear', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('How did you hear about us?'),
        'placeholder'   => __('eg: Friend, Facebook Advert, Blog, Magazine'),
        ), $checkout->get_value( 'how_did_you_hear' ));


}
// Add to order
add_action( 'woocommerce_checkout_update_order_meta', 'how_did_you_hear_checkout_field_update_order_meta' );
function how_did_you_hear_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['how_did_you_hear'] ) ) {
        update_post_meta( $order_id, 'Additional', sanitize_text_field( $_POST['how_did_you_hear'] ) );
    }
}
// Add to admin
add_action( 'woocommerce_admin_order_data_after_billing_address', 'how_did_you_hear_checkout_field_display_admin_order_meta', 10, 1 );
function how_did_you_hear_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('How did you hear about us').':</strong> ' . get_post_meta( $order->get_id(), 'Additional', true ) . '</p>';
}

// // Add family names in checkout
// add_action( 'woocommerce_after_order_notes', 'family_names_checkout_field', 13 );
// function family_names_checkout_field( $checkout ) {
//     echo '<div id="checkout_fields"><h2>' . __('Personalisation') . '</h2>';
//     woocommerce_form_field( 'family_names', array(
//         'type'          => 'text',
//         'class'         => array('my-field-class form-row-wide'),
//         'label'         => __('Enter your names so we can personalise your box!'),
//         'placeholder'   => __('eg: Sally & Emily'),
//         ), $checkout->get_value( 'family_names' ));
//     echo '</div>';
// }

// // Add to order
// add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );
// function my_custom_checkout_field_update_order_meta( $order_id ) {
//     if ( ! empty( $_POST['family_names'] ) ) {
//         update_post_meta( $order_id, 'Personalisation', sanitize_text_field( $_POST['family_names'] ) );
//     }
// }
// // Add to admin
// add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
// function my_custom_checkout_field_display_admin_order_meta($order){
//     echo '<p><strong>'.__('Personalisation').':</strong> ' . get_post_meta( $order->get_id(), 'Personalisation', true ) . '</p>';
// }

// // if is gift category 
// // Add gift message checkout
// add_action( 'woocommerce_after_order_notes', 'gift_message_checkout_field', 15 );
// function gift_message_checkout_field( $checkout ) {
// 	$is_gist = false;
// 	//print_r(WC()->cart->get_cart());
// 	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
// 		if ( has_term( 'gifts', 'product_cat', $cart_item['product_id'] ) ) {
// 			$is_gist = true;
// 			break;
// 		}
// 	}
	   
// 	if ( $is_gist ) {
// 		echo '<div id="checkout_fields"><h2>' . __('Gift message') . '</h2>';
	
// 		woocommerce_form_field( 'gift_message', array(
// 			'type'          => 'textarea',
// 			'class'         => array('my-field-class form-row-wide'),
// 			'label'         => __('Enter a a short gift message'),
// 			'placeholder'   => __('eg: Hi Sally! Happy birthday, enjoy your Treat Trunk!'),
// 			), $checkout->get_value( 'gift_message' ));
	
// 		echo '</div>';
// 	}
// }
// // Add to order
// add_action( 'woocommerce_checkout_update_order_meta', 'gift_message_checkout_field_update_order_meta' );
// function gift_message_checkout_field_update_order_meta( $order_id ) {
//     if ( ! empty( $_POST['gift_message'] ) ) {
//         update_post_meta( $order_id, 'gift_message', sanitize_text_field( $_POST['gift_message'] ) );
//     }
// }
// // Add to admin
// add_action( 'woocommerce_admin_order_data_after_billing_address', 'gift_checkout_order_meta_admin', 10, 1 );
// function gift_checkout_order_meta_admin($order){
//     echo '<p><strong>'.__('Gift message').':</strong> ' . get_post_meta( $order->get_id(), 'gift_message', true ) . '</p>';
// }

// EXTRA DIVS FIX
add_action( 'woocommerce_after_order_notes', function(){ echo '<div><div>';}, 16 );

//remove product title only
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

// SYNC SHIPPING ADDRESS
add_action( 'woocommerce_after_save_address_validation', 'jsforwp_update_address_for_orders', 10, 4 );
function jsforwp_update_address_for_orders( $user_id, $load_address, $address, $customer ) {
	if ($load_address != 'shipping')
		return;

	$customer_orders = wc_get_orders( array(
		'type'        => 'shop_order',
		'limit'       => - 1,
		'customer_id' => $user_id
	) );
    foreach( $customer_orders as $order ) {
	  if ($order->get_status() != 'processing' || time() - strtotime($order->get_date_created()) > 10 * 24 * 3600 )
			continue;

		foreach($_POST as $key => $value) 
			if (strpos($key, $load_address) !== false) {
				update_post_meta( $order->get_id(), '_'.$key, $value );
			}
    }
};







add_action( 'woocommerce_before_add_to_cart_button', 'add_fields_before_add_to_cart' );
function add_fields_before_add_to_cart( ) {
	?>
	<table>
		<tr>
			<td>
				<?php _e( "Name Personalisation:", "aoim"); ?>
			</td>
			<td>
				<input type = "text" name = "customer_name" id = "customer_name" placeholder = "Who is your box for?">
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( "Gift Message:", "aoim"); ?>
			</td>
			<td>
				<input type = "text" name = "gift_message" id = "gift_message" placeholder = "Write a little message here...">
			</td>
		</tr>
	</table>
	<?php
}




/**
 * Add data to cart item
 */
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 25, 2 );
function add_cart_item_data( $cart_item_meta, $product_id ) {
	if ( isset( $_POST ['customer_name'] ) && isset( $_POST ['gift_message'] ) ) {
		$custom_data  = array() ;
		$custom_data [ 'customer_name' ]    = isset( $_POST ['customer_name'] ) ?  sanitize_text_field ( $_POST ['customer_name'] ) : "" ;
		$custom_data [ 'gift_message' ] = isset( $_POST ['gift_message'] ) ? sanitize_text_field ( $_POST ['gift_message'] ): "" ;
		$cart_item_meta ['custom_data']     = $custom_data ;
	}
	
	return $cart_item_meta;
}


/**
 * Display custom data on cart and checkout page.
 */
add_filter( 'woocommerce_get_item_data', 'get_item_data' , 25, 2 );
function get_item_data ( $other_data, $cart_item ) {
	if ( isset( $cart_item [ 'custom_data' ] ) ) {
		$custom_data  = $cart_item [ 'custom_data' ];
			
		$other_data[] = array( 'name' => 'Name Personalisation',
					'display'  => $custom_data['customer_name'] );
		$other_data[] = array( 'name' => 'Gift Message',
				       'display'  => $custom_data['gift_message'] );
	}
	
	return $other_data;
}



/**
 * Add order item meta.
 */
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 10, 2);
function add_order_item_meta ( $item_id, $values ) {
	if ( isset( $values [ 'custom_data' ] ) ) {
		$custom_data  = $values [ 'custom_data' ];
		wc_add_order_item_meta( $item_id, 'Name Personalisation', $custom_data['customer_name'] );
		wc_add_order_item_meta( $item_id, 'Gift Message', $custom_data['gift_message'] );
	}
}