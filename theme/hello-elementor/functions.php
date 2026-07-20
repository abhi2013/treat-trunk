<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '9.9.992' );

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
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

function enqueue_custom_styles() {
	wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/custom.css', array(), '1.0.23');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_styles');

function enqueue_custom_scripts() {
	wp_enqueue_script('custom', get_template_directory_uri() . '/custom.js', array('jquery'), '1.0.0');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

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
add_filter( 'hello_elementor_page_title', '__return_false' );

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
/* add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );
function woo_new_product_tab( $tabs ) {
	
	// Adds the new tab
	
	$tabs['test_tab'] = array(
		'title' 	=> __( 'Share The Meal Charity', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'woo_new_product_tab_content'
	);

	return $tabs;

} */


function woo_new_product_tab_content() {

	echo get_theme_mod( "mytheme_company-name" );
	
}


if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page();
	
}

//remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );




// EXTRA DIVS
add_action( 'woocommerce_after_order_notes', function(){ echo '</div></div>'; }, 11 );

function product_is_gift($id) {
	return has_term( 'gifts', 'product_cat', $id );
}

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
/*add_action( 'woocommerce_after_order_notes', 'gift_message_checkout_field', 15 );
function gift_message_checkout_field( $checkout ) {
	$is_gift = false;
	//print_r(WC()->cart->get_cart());
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		if ( product_is_gift($cart_item['product_id']) ) {
			$is_gift = true;
			break;
		}
	}
	   
	if ( $is_gift ) {
		echo '<div id="checkout_fields"><h2>' . __('Gift message') . '</h2>';
	
		woocommerce_form_field( 'gift_message', array(
			'type'          => 'textarea',
			'class'         => array('my-field-class form-row-wide'),
			'label'         => __('Enter a a short gift message'),
			'placeholder'   => __('eg: Hi Sally! Happy birthday, enjoy your Treat Trunk!'),
			), $checkout->get_value( 'gift_message' ));
	
		echo '</div>';
	}
}*/
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




//add_action( 'woocommerce_before_add_to_cart_button', 'add_fields_before_add_to_cart' );
function add_fields_before_add_to_cart( ) {
	global $product;
	
	$name = $gift = '';

	if (isset($_GET['switch-subscription'])) {
		$subscription = wcs_get_subscription( $_GET['switch-subscription'] );
		if ( wcs_is_subscription( $subscription ) ) {
			$parent_order = $subscription->get_parent();

			if (!empty($parent_order)) {
				foreach( $parent_order->get_items() as $item_id => $item ){
					$name = wc_get_order_item_meta( $item_id, 'Name Personalisation', true );
					$gift = wc_get_order_item_meta( $item_id, 'Gift Message', true );
					break;
				}
			}
		}
	}
	
	if ($product->get_type() != 'grouped') {
		if (!product_is_gift($product->get_id())) {
		?>
			<div>
				<h4><?php _e( "Name Personalisation:", "aoim"); ?></h4>
				<p>To personalise your box, please leave your family’s first names here.</p>
				<input type = "text" name = "customer_name" id = "customer_name" placeholder = "eg: Sally & Emily" value="<?php echo $name; ?>">
			</div>
		<?php } else { ?>
			<div style="margin-bottom:30px;">
				<h4><?php _e( "Gift Message:", "aoim"); ?></h4>	
				<input type = "textarea" name = "gift_message" id = "gift_message" placeholder = "Write a little message here..." value="<?php echo $gift; ?>">
		
			</div>
		<?php
		}
	}
}

/**
 * Add data to cart item
 */
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data', 0, 2 );
function add_cart_item_data( $cart_item_meta, $product_id ) {
	if ( !empty( $_POST ['customer_name'] ) ) {
		$custom_data  = array() ;
		$custom_data [ 'customer_name' ]    = isset( $_POST ['customer_name'] ) ?  sanitize_text_field ( $_POST ['customer_name'] ) : "" ;
		$cart_item_meta ['custom_data']     = $custom_data ;
		unset($_POST ['customer_name']);
	}
	else if ( !empty( $_POST ['gift_message'] )) {
		$custom_data [ 'gift_message' ] = isset( $_POST ['gift_message'] ) ? sanitize_text_field ( $_POST ['gift_message'] ): "" ;
		$cart_item_meta ['custom_data']     = $custom_data ;
		unset($_POST ['gift_message']);
	}
	
	return $cart_item_meta;
}


/**
 * Display custom data on cart and checkout page.
 */
add_filter( 'woocommerce_get_item_data', 'get_item_data' , 1000, 2 );
function get_item_data ( $other_data, $cart_item ) {
	if ( isset( $cart_item [ 'custom_data' ] ) ) {
		$custom_data  = $cart_item [ 'custom_data' ];
			
		if (!empty($custom_data['customer_name']))
		$other_data[] = array( 'name' => 'Name Personalisation',
					'display'  => $custom_data['customer_name'] );
		if (!empty($custom_data['gift_message']))
		$other_data[] = array( 'name' => 'Gift Message',
				       'display'  => $custom_data['gift_message'] );
	}

	// REMOVE EMPTY WELCOME BOX
	foreach($other_data as $key => $value) {
		if (isset($value['key']) && $value['key'] == 'Welcome Box' && strlen($value['value']) <= 5)
			unset($other_data[$key]);
	}
	
	return $other_data;
}



/**
 * Add order item meta.
 */
add_action( 'woocommerce_add_order_item_meta', 'add_order_item_meta' , 1000, 2);
function add_order_item_meta ( $item_id, $values ) {
	if ( isset( $values [ 'custom_data' ] ) ) {
		$custom_data  = $values [ 'custom_data' ];
		if (!empty($custom_data['customer_name']))
			wc_add_order_item_meta( $item_id, 'Name Personalisation', $custom_data['customer_name'] );
		if (!empty($custom_data['gift_message']))
			wc_add_order_item_meta( $item_id, 'Gift Message', $custom_data['gift_message'] );
	}
	
	// CHECK EMPTY WELCOME BOX
	if (strlen(wc_get_order_item_meta($item_id, 'welcome-box', true)) <= 5) {
		wc_update_order_item_meta($item_id, 'welcome-box', false);
	}
}

// REMOVE WELCOME FROM RENEWALS
add_action( 'woocommerce_add_subscription_item_meta', function($item_id, $cart_item, $cart_item_key) {
	wc_update_order_item_meta($item_id, 'welcome-box', false);
}, 1000, 3 );
add_action( 'woocommerce_subscription_status_pending_to_active', function($order) {
	foreach( $order->get_items() as $item_id => $item ){
		wc_update_order_item_meta($item_id, 'welcome-box', false);
	}
}, 1000, 4 );

// REMOVE VARIATION FROM TITLE
add_filter( 'woocommerce_product_variation_title_include_attributes', function(){ return false; } );
add_filter( 'woocommerce_is_attribute_in_product_name', function(){ return false; } );
add_filter( 'woocommerce_order_item_name', 'custom_get_name', 1000, 2 );
add_filter( 'woocommerce_product_variation_get_name' ,'custom_get_name', 1000, 2 );
function custom_get_name( $value, $product ) {
	if (!strpos($value, 'Welcome') !== false) {
		$value = explode(' - ', $value)[0];
	}
    return $value;
}

// ADD ADMIN COLUMN NOTICE ABOUT WELCOME PACKAGE
add_action( 'manage_shop_order_posts_custom_column', function($column){
	global $post;

	if ( 'subscription_relationship' == $column && wcs_order_contains_subscription( $post->ID, 'parent' ) ) {
		$order = wc_get_order( $post->ID );
		foreach( $order->get_items() as $item_id => $item ){
			$value = wc_get_order_item_meta($item_id, 'welcome-box', true);
			if (!empty($value) && strlen($value) > 5) {
				echo '<div style="line-height:1;margin-top:6px;font-weight:600;'.($order->get_status()!='completed'&&$order->get_status()!='cancelled'&&$order->get_status()!='refunded'?'background-color:#ffff00;color:#000;':'').'">'.$value.'</div>';
			}
		}
	}
}, 10, 1 );


// GET ORDER NUMBER META VALUE
add_filter( 'woocommerce_order_number', function($id) {
	$meta = get_post_meta($id, '_order_number', true);
	if (!empty($meta))
		return $meta;
	else
		return $id;
}, 100, 1);

add_filter('limit_login_whitelist_ip', function($allow, $ip){
	 if ($ip == '80.211.176.100')
	 	return true;
	 
	 return $allow;
}, 10, 2);

// DISABLE RECIPE PLUGIN OG META
add_filter( 'trg_add_og_tags', function(){return false;});



/*--------------------------------------------------------------
# Add duplicate button to Wordpress
--------------------------------------------------------------*/

/*
* Function for post duplication. Dups appear as drafts. User is redirected to the edit screen
*/

function rd_duplicate_post_as_draft(){
global $wpdb;
if (! ( isset( $_GET['post']) || isset( $_POST['post']) || ( isset($_REQUEST['action']) && 'rd_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
wp_die('No post to duplicate has been supplied!');
}

/*
* Nonce verification
*/

if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) )
return;

/*
* get the original post id
*/

$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

/*
* and all the original post data then
*/

$post = get_post( $post_id );

/*
* if you don't want current user to be the new post author,
* then change next couple of lines to this: $new_post_author = $post->post_author;
*/

$current_user = wp_get_current_user();
$new_post_author = $current_user->ID;

/*
* if post data exists, create the post duplicate
*/

if (isset( $post ) && $post != null) {

/*
* new post data array
*/

$args = array(
'comment_status' => $post->comment_status,
'ping_status' => $post->ping_status,
'post_author' => $new_post_author,
'post_content' => $post->post_content,
'post_excerpt' => $post->post_excerpt,
'post_name' => $post->post_name,
'post_parent' => $post->post_parent,
'post_password' => $post->post_password,
'post_status' => 'draft',
'post_title' => $post->post_title,
'post_type' => $post->post_type,
'to_ping' => $post->to_ping,
'menu_order' => $post->menu_order
);

/*
* insert the post by wp_insert_post() function
*/

$new_post_id = wp_insert_post( $args );

/*
* get all current post terms ad set them to the new post draft
*/

$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");

foreach ($taxonomies as $taxonomy) {
$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
}

/*
* duplicate all post meta just in two SQL queries
*/

$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
if (count($post_meta_infos)!=0) {
$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
foreach ($post_meta_infos as $meta_info) {
$meta_key = $meta_info->meta_key;
if( $meta_key == '_wp_old_slug' ) continue;
$meta_value = addslashes($meta_info->meta_value);
$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
}
$sql_query.= implode(" UNION ALL ", $sql_query_sel);
$wpdb->query($sql_query);
}

/*
* finally, redirect to the edit post screen for the new draft
*/

wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
exit;
}
 else {
wp_die('Post creation failed, could not find original post: ' . $post_id);
}
}

add_action( 'admin_action_rd_duplicate_post_as_draft', 'rd_duplicate_post_as_draft' );

/*
* Add the duplicate link to action list for post_row_actions
*/

function rd_duplicate_post_link( $actions, $post ) {
if (current_user_can('edit_posts')) {
$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=rd_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
}
return $actions;
}

add_filter( 'post_row_actions', 'rd_duplicate_post_link', 10, 2 );
add_filter('page_row_actions', 'rd_duplicate_post_link', 10, 2);

/* Custom Dashboard Subscriber Count Widget */
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
  
function my_custom_dashboard_widgets() {
	global $wp_meta_boxes;
 
// 	wp_add_dashboard_widget('custom_help_widget', 'Subscriber Counts', 'custom_dashboard_sub_counts');
}
 
function custom_dashboard_sub_counts() {
	echo '<p>This is a test, please ignore</p>';
	$num_subs = wp_count_posts($post_type = 'shop_subscription');
	
	$args = array(
		'taxonomy' => 'product_type',
		'include' => 221
	);
	$terms = get_terms($args);
	
	$args = array(
		'post_type' => 'shop_subscription',
		'post_status' => 'wc-active',
		'meta_key' => 'product_id',
		'meta_query' => array(
			array (
				'key' => 'product_id',
				'compare' => '=',
				'value' => 221,
				'type' => 'numeric'
			)
		),
	);
	$the_query = new WP_Query($args);
	$num_subs = $the_query->found_posts;
	
	$num = number_format_i18n($num_subs);
	$text = _n('Subscription', 'Subscriptions', $num_subs);
	
	
	echo $num.' '.$text;
}

function sub_count() {
	$args = array(
		'post_type' => 'shop_subscription',
		'post_status' => 'wc-active'
	);
	$the_query = new WP_Query($args);
	$num_subs = $the_query->found_posts;
}

add_filter( 'wc_stripe_hide_payment_request_on_product_page', '__return_true' );

add_filter( 'woocommerce_email_recipient_customer_completed_order', 'jgreen_disable_customer_order_email_if_free', 10, 2 );
  
function jgreen_disable_customer_order_email_if_free( $recipient, $order ) {
    $page = $_GET['page'] = isset( $_GET['page'] ) ? $_GET['page'] : '';
    if ( 'wc-settings' === $page ) {
        return $recipient; 
    }
    if ( $order->get_total() == 0 ) $recipient = '';
    return $recipient;
}

function eg_remove_my_subscriptions_button( $actions, $subscription ) {

	foreach ( $actions as $action_key => $action ) {
		switch ( $action_key ) {
			// case 'change_payment_method':	// Hide "Change Payment Method" button?
//			case 'change_address':		// Hide "Change Address" button?
//			case 'switch':			// Hide "Switch Subscription" button?
			case 'resubscribe':		// Hide "Resubscribe" button from an expired or cancelled subscription?
//			case 'pay':			// Hide "Pay" button on subscriptions that are "on-hold" as they require payment?
//			case 'reactivate':		// Hide "Reactive" button on subscriptions that are "on-hold"?
//			case 'cancel':			// Hide "Cancel" button on subscriptions that are "active" or "on-hold"?
				unset( $actions[ $action_key ] );
				break;
			default: 
				error_log( '– $action = ' . print_r( $action, true ) );
				break;
		}
	}

	return $actions;
}
add_filter( 'wcs_view_subscription_actions', 'eg_remove_my_subscriptions_button', 100, 2 );


function wcsrgp_is_giftable_product( $is_giftable, $product ) {
    $giftable_product_ids = array( 221, 359, 360, 361, 7055, 7056, 7057, 7058 );

    return in_array( $product->get_id() , $giftable_product_ids );
}

add_filter( 'wcsg_is_giftable_product', 'wcsrgp_is_giftable_product', 10, 2 );

add_filter( 'automatewoo_email_templates', 'my_automatewoo_email_templates' );

function my_automatewoo_email_templates( $templates ) {
    
	// SIMPLE
	// register a template by adding a slug and name to the $templates array
	$templates['block-sub-gift-card'] = 'Block Subscription Gift Card';
	
	// SETTING A CUSTOM PATH
	// As of version 4.8, it's possible to set a custom template path
/*
	$templates['custom-3'] = array(
		'template_name' => __( 'Custom Template with a custom path', 'my-plugin' ),
		'path'          => dirname( __FILE__ ) . '/templates/custom-1'
	);
*/

	return $templates;
}

function notify_address_change($user_id) {
	$current_user = wp_get_current_user();
	
	$email = get_option('admin_email', '');
	$subject = "Customer Changed Address";
	
	$message = "Customer has changed their address - " . esc_html($current_user->user_firstname) . " " . esc_html($current_user->user_lastname) . " (" . $current_user->user_email . ")";
	
	wp_mail($email, $subject, $message);
}
add_action('woocommerce_customer_save_address', 'notify_address_change', 20);

function treattrunk_settings_init() {
	add_settings_section(
		'top_bar_settings_section',
		'Top Bar Settings',
		'top_bar_settings_callback_function',
		'general'
	);
	
	add_settings_field(
		'top_bar_msg',
		'Top Bar Message',
		'top_bar_msg_callback_function',
		'general',
		'top_bar_settings_section'
	);
	
	register_setting('general','top_bar_msg');
}
add_action('admin_init', 'treattrunk_settings_init');

function top_bar_settings_callback_function() {
	echo '<p>Set the top bar message</p>';
}

function top_bar_msg_callback_function() {
	echo '<input name="top_bar_msg" id="top_bar_msg" type="text" value="'.get_option('top_bar_msg').'"></input>';
}

function my_custom_styles() {
	echo '
<style>
.wc-action-button-processing.processing {
	display:none!important;
}
</style>';
}

add_action('admin_head', 'my_custom_styles');

// Remove Additional Info and Order Notes field from Checkout
// add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );

add_filter( 'woocommerce_checkout_fields' , 'remove_order_notes' );

function remove_order_notes( $fields ) {
     unset($fields['order']['order_comments']);
     return $fields;
}

function get_private_order_notes_cancellation_reason( $order_id){
    global $wpdb;

    $table_perfixed = $wpdb->prefix . 'comments';
    $results = $wpdb->get_results("
        SELECT *
        FROM $table_perfixed
        WHERE  `comment_post_ID` = $order_id
        AND  `comment_type` LIKE  'order_note'
        AND `comment_content` LIKE '%Cancellation Reason:%'
    ");

    foreach($results as $note){
        $order_note[]  = array(
            'note_id'      => $note->comment_ID,
            'note_date'    => $note->comment_date,
            'note_author'  => $note->comment_author,
            'note_content' => $note->comment_content,
        );
    }
    return $order_note;
}

/**
 * Add custom tracking code to the thank-you page
 */
add_action( 'woocommerce_thankyou', 'my_custom_tracking' );

function my_custom_tracking( $order_id ) {

	// Lets grab the order
	$order = wc_get_order( $order_id );

	/**
	 * Put your tracking code here
	 * You can get the order total etc e.g. $order->get_total();
	 */
	 
	// This is the order total
	$p = $order->get_total();
	$t = $order->get_id();
	$r = $_SESSION['reqid'];
	$e = '';
	foreach($order->get_items() as $item_id => $item) {
		$pid = $item->get_product_id();
		if($pid == 221) {
			$e = 646;
		} elseif($pid == 7055) {
			$e = 647;
		} else {
			$e = 648;
		}
	}
	// This is how to grab line items from the order 
/*
	$line_items = $order->get_items();
	// This loops over line items
	foreach ( $line_items as $item ) {
  		// This will be a product
  		$product = $order->get_product_from_item( $item );
  		// This is the products SKU
		$sku = $product->get_sku();
		switch ($sku) {
			case array('TT-MS-S','TT-MS-S-WM','TT-MS-S-WS','TT-MS-S-WN'):
				$e = 646;
				break;
			case array('TT-MS-M','TT-MS-M-WM','TT-MS-M-WS','TT-MS-M-WN'):
				$e = 647;
				break;
			default:
				$e = 648;
				break;
		}
	}
*/
	
	echo '<img src="https://mywebconect.com/p.ashx?o=1583&e='.$e.'&f=img&t='.$t.'&r='.$r.'&p='.$p.'" width="1" height="1" border="0" />';
	
}
function get_conectia_reqid() {
	if(!session_id()) {
		session_start();
	}
	if(isset($_GET['reqid'])) {
		$_SESSION['reqid'] = $_GET['reqid'];
	}
}
add_action('init', 'get_conectia_reqid');

add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id ) {
    global $the_order, $post;

    if ( 'order_status' === $column ) {
        $products_names = []; // Initializing

        // Loop through order items
        foreach ( $the_order->get_items() as $item ) {
            $product = $item->get_product(); // Get the WC_Product object
            $products_names[]  = $item->get_name(); // Store in an array
        }
        // Display
        echo '<ul style="list-style: none;"><li>' . implode('</li><li>', $products_names) . '</li></ul>';
    }
}

function jgreen_hide_additional_tab($tabs) {
	unset($tabs['additional_information']);
	return $tabs;
}
add_filter('woocommerce_product_tabs', 'jgreen_hide_additional_tab', 9999);

function jgreen_add_meta_to_description($tabs) {
	$tabs['description']['callback'] = 'jgreen_product_meta_and_description_tab';

    // (optional) We can also overwrite the title
    $tabs['description']['title'] = __('Description', 'woocommerce');

    return $tabs;
}

function jgreen_product_meta_and_description_tab() { // this is where you indicate what appears in the description tab
    wc_get_template( 'single-product/tabs/description.php' ); // The product description after
    wc_get_template( 'single-product/meta.php' ); // The meta content first
}
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
add_filter('woocommerce_product_tabs', 'jgreen_add_meta_to_description', 999);

// Preload the homepage hero background image (the LCP element). It's an Elementor CSS
// background image, which WP Rocket's own ATF preload doesn't size responsively, so this
// is added manually with the correct per-viewport image AND fetchpriority=high, so the
// browser fetches exactly the image the hero needs, first. WP Rocket's own (unresponsive,
// full-size) ATF preload for this page is disabled via rocket_atf_elements in site-core.php
// so the two don't compete on mobile. home_url() keeps it correct on any environment.
function jgreen_preload_homepage_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$mobile  = esc_url( home_url( '/wp-content/uploads/2025/03/Treat-Trunk-Healthy-Snack-Box-Subscrition-Gift-Resize-768x513.webp' ) );
	$desktop = esc_url( home_url( '/wp-content/uploads/2025/03/Treat-Trunk-Healthy-Snack-Box-Subscrition-Gift-Resize.webp' ) );
	echo '<link rel="preload" as="image" fetchpriority="high" media="(max-width: 767px)" href="' . $mobile . '">' . "\n";
	echo '<link rel="preload" as="image" fetchpriority="high" media="(min-width: 768px)" href="' . $desktop . '">' . "\n";

	// NB: deliberately NOT preloading the DM Sans fonts here. Tested 2026-07-20:
	// preloading the 3 weights (84KB, high priority) stole throttled bandwidth
	// from the hero background image and regressed mobile LCP ~2.5s -> ~4.4s.
	// The fonts use font-display:swap so they don't block render anyway (text
	// paints in a fallback and swaps in), and they sit on PageSpeed's "critical
	// chain" only as an Unscored/informational item. The hero image (the actual
	// LCP element) keeps the connection to itself. Do not re-add font preloads
	// without re-measuring LCP.
}
add_action('wp_head', 'jgreen_preload_homepage_hero', 1);