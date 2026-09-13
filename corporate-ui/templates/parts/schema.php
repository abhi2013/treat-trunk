<?php
/**
 * JSON-LD for a corporate-ui page (added 2026-09-13).
 *
 * $tt_products: array of Product nodes (already shaped for schema.org).
 * $tt_faqs:     the same array parts/faqs.php rendered, so the FAQPage
 *               markup always matches the visible accordion.
 *
 * Emitted in the body: valid for Google, and it keeps each page's schema
 * next to the page it describes instead of a growing ID list in site-core.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tt_graph = array(
	array(
		'@type'   => 'Organization',
		'@id'     => home_url( '/#organization' ),
		'name'    => 'Treat Trunk Ltd',
		'url'     => home_url( '/' ),
		'email'   => 'hello@treattrunk.co.uk',
		'address' => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => '86-90 Paul Street',
			'addressLocality' => 'London',
			'postalCode'      => 'EC2A 4NE',
			'addressCountry'  => 'GB',
		),
	),
);

if ( ! empty( $tt_products ) ) {
	foreach ( $tt_products as $tt_p ) {
		$tt_p['@type'] = 'Product';
		$tt_p['brand'] = array( '@type' => 'Brand', 'name' => 'Treat Trunk' );
		if ( isset( $tt_p['offers'] ) ) {
			$tt_p['offers']['@type']         = isset( $tt_p['offers']['@type'] ) ? $tt_p['offers']['@type'] : 'AggregateOffer';
			$tt_p['offers']['priceCurrency'] = 'GBP';
			$tt_p['offers']['availability']  = 'https://schema.org/InStock';
			$tt_p['offers']['seller']        = array( '@id' => home_url( '/#organization' ) );
		}
		$tt_graph[] = $tt_p;
	}
}

if ( ! empty( $tt_faqs ) ) {
	$tt_graph[] = array(
		'@type'      => 'FAQPage',
		'mainEntity' => array_map( function ( $qa ) {
			return array(
				'@type'          => 'Question',
				'name'           => $qa[0],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $qa[1],
				),
			);
		}, $tt_faqs ),
	);
}

echo '<script type="application/ld+json" class="tt-corp-schema">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $tt_graph ) ) . '</script>' . "\n";
