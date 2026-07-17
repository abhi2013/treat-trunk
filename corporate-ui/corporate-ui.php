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
 * Load the polish/responsive stylesheet - only on this one page, not
 * site-wide. No longer loads a separate Quicksand/Nunito Sans Google
 * Fonts request now that this page uses DM Sans (already self-hosted and
 * loaded theme-wide) to match the rest of the site's teal direction.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_page() || get_page_template_slug( get_queried_object_id() ) !== TT_CORP_UI_TEMPLATE_KEY ) {
		return;
	}
	wp_enqueue_style(
		'tt-corp-orders-css',
		plugins_url( 'assets/corporate-orders.css', __FILE__ ),
		array(),
		filemtime( TT_CORP_UI_DIR . 'assets/corporate-orders.css' )
	);
} );

/**
 * WeWork free-box claim form: was mailto-only, which silently does nothing
 * on any device without a configured default mail client (common on
 * managed office laptops - exactly the audience this form targets). Sends
 * a real email server-side via wp_mail() instead; the template still keeps
 * a mailto link as a visible fallback if the AJAX request itself fails
 * (e.g. the visitor is offline).
 */
add_action( 'wp_ajax_tt_wework_claim', 'tt_handle_wework_claim' );
add_action( 'wp_ajax_nopriv_tt_wework_claim', 'tt_handle_wework_claim' );
function tt_handle_wework_claim() {
	check_ajax_referer( 'tt_wework_claim', 'nonce' );

	$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$company  = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$location = isset( $_POST['location'] ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : '';
	$notes    = isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '';

	if ( '' === $name || '' === $company || '' === $location ) {
		wp_send_json_error( array( 'message' => 'Please fill in your name, company, and WeWork building.' ) );
	}

	$subject = 'WeWork free welcome box - ' . $company;
	$body    = "Name: {$name}\nCompany: {$company}\nWeWork building: {$location}"
		. ( '' !== $notes ? "\nNotes: {$notes}" : '' )
		. "\n\nSubmitted via the corporate-orders WeWork claim form.";

	$sent = wp_mail( 'hello@treattrunk.co.uk', $subject, $body, array( 'Reply-To: ' . $name . ' <hello@treattrunk.co.uk>' ) );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => "Thanks! We'll be in touch shortly to arrange your free welcome box." ) );
	} else {
		wp_send_json_error( array( 'message' => 'Something went wrong sending that - please email hello@treattrunk.co.uk directly, or use the button below to open your email client.' ) );
	}
}
