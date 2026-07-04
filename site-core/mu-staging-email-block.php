<?php
/**
 * STAGING-ONLY SAFETY NET: block all outgoing email.
 *
 * Staging is a clone of production's database, which means wp-mail-smtp
 * carries real, working SMTP credentials by default (confirmed 2026-07-05 -
 * enabling a payment gateway triggered a real WooCommerce admin notification
 * email). This must never be deployed to production - it is intentionally
 * placed in the mu-plugins loader so it can't be accidentally left
 * deactivated, unlike a normal plugin.
 *
 * Deploy target: staging server's wp-content/mu-plugins/ only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'pre_wp_mail', function ( $return, $atts ) {
	error_log( 'STAGING email blocked - to: ' . ( is_array( $atts['to'] ) ? implode( ',', $atts['to'] ) : $atts['to'] ) . ' subject: ' . $atts['subject'] );
	return true; // short-circuits wp_mail() - nothing is actually sent
}, 10, 2 );
