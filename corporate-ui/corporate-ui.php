<?php
/**
 * Plugin Name: Treat Trunk Corporate UI
 * Description: Custom page template for the redesigned Corporate Orders page. Registers a selectable page template (Page Attributes > Template) so the page can be switched between the original Elementor design and this one without deleting either.
 * Version: 1.0.0
 * Author: Treat Trunk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TT_CORP_UI_TEMPLATE_KEY', 'templates/corporate-orders-template.php' );
define( 'TT_CORP_UI_TEMPLATE_LABEL', 'Corporate Orders (Custom Redesign)' );
define( 'TT_CORP_UI_DIR', plugin_dir_path( __FILE__ ) );

add_filter( 'theme_page_templates', function ( array $templates ): array {
	$templates[ TT_CORP_UI_TEMPLATE_KEY ] = TT_CORP_UI_TEMPLATE_LABEL;
	return $templates;
} );

add_filter( 'template_include', function ( string $template ): string {
	if ( is_page() ) {
		$assigned = get_page_template_slug( get_queried_object_id() );
		if ( $assigned === TT_CORP_UI_TEMPLATE_KEY ) {
			$custom = TT_CORP_UI_DIR . TT_CORP_UI_TEMPLATE_KEY;
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
	}
	return $template;
} );

/**
 * Load the real Quicksand/Nunito Sans webfonts (matching the original design)
 * plus the polish/responsive stylesheet - only on this one page, not site-wide.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_page() || get_page_template_slug( get_queried_object_id() ) !== TT_CORP_UI_TEMPLATE_KEY ) {
		return;
	}
	wp_enqueue_style(
		'tt-corp-fonts',
		'https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&family=Nunito+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'tt-corp-orders-css',
		plugins_url( 'assets/corporate-orders.css', __FILE__ ),
		array(),
		filemtime( TT_CORP_UI_DIR . 'assets/corporate-orders.css' )
	);
} );
