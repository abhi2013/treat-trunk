<?php
/**
 * Data changes for the corporate SEO three-page split (2026-09-13).
 * Run once on production with:  wp eval-file scripts/deploy-corporate-seo-pages-2026-09.php
 * Every step is idempotent: re-running skips anything already applied.
 * Companion to the template/plugin changes in corporate-ui/ and site-core/.
 * See docs/corporate-seo-pages-2026-09.md for the copy and the linking map.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tt_log( $msg ) {
	WP_CLI::log( $msg );
}

function tt_set_meta( $post_id, $key, $value ) {
	$current = get_post_meta( $post_id, $key, true );
	if ( $current === $value ) {
		tt_log( "  = $key unchanged on $post_id" );
		return;
	}
	update_post_meta( $post_id, $key, wp_slash( $value ) );
	tt_log( "  + $key set on $post_id" );
}

/* -------------------------------------------------------------------------
 * 1. Christmas page 54774: re-slug + retitle + Yoast. WordPress records the
 *    old slug in _wp_old_slug and 301s it (wp_old_slug_redirect).
 * ---------------------------------------------------------------------- */
tt_log( '1. Christmas page (54774)' );
$xmas = get_post( 54774 );
if ( ! $xmas ) {
	WP_CLI::error( 'Page 54774 not found' );
}
if ( 'corporate-christmas-hampers' !== $xmas->post_name || 'Corporate Christmas Hampers' !== $xmas->post_title ) {
	$r = wp_update_post( array(
		'ID'         => 54774,
		'post_name'  => 'corporate-christmas-hampers',
		'post_title' => 'Corporate Christmas Hampers',
	), true );
	if ( is_wp_error( $r ) ) {
		WP_CLI::error( 'Re-slug failed: ' . $r->get_error_message() );
	}
	tt_log( '  + re-slugged to /corporate-christmas-hampers/ (old slug recorded: ' . implode( ',', (array) get_post_meta( 54774, '_wp_old_slug' ) ) . ')' );
} else {
	tt_log( '  = already re-slugged' );
}
if ( ! in_array( 'corporate-christmas-gifting', (array) get_post_meta( 54774, '_wp_old_slug' ), true ) ) {
	add_post_meta( 54774, '_wp_old_slug', 'corporate-christmas-gifting' );
	tt_log( '  + _wp_old_slug corporate-christmas-gifting added by hand' );
}
tt_set_meta( 54774, '_yoast_wpseo_title', 'Corporate Christmas Hampers | Healthy & Alcohol-Free UK' );
tt_set_meta( 54774, '_yoast_wpseo_metadesc', 'Luxury corporate Christmas hampers, healthy and alcohol-free as standard. Signature Hamper £149, team gifts from £13. One invoice, office or home delivery.' );
tt_set_meta( 54774, '_yoast_wpseo_focuskw', 'corporate christmas hampers' );

/* -------------------------------------------------------------------------
 * 2. New office page
 * ---------------------------------------------------------------------- */
tt_log( '2. Office page' );
$office = get_page_by_path( 'office-snack-boxes' );
if ( $office ) {
	$office_id = $office->ID;
	tt_log( "  = exists as $office_id" );
} else {
	$office_id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Office Snack Boxes',
		'post_name'    => 'office-snack-boxes',
		'post_content' => '',
		'post_author'  => 1,
	), true );
	if ( is_wp_error( $office_id ) ) {
		WP_CLI::error( 'Office page insert failed: ' . $office_id->get_error_message() );
	}
	tt_log( "  + created page $office_id" );
}
tt_set_meta( $office_id, '_wp_page_template', 'templates/office-template.php' );
tt_set_meta( $office_id, '_yoast_wpseo_title', 'Office Snack Boxes | Healthy, No Minimum, Pay by Invoice' );
tt_set_meta( $office_id, '_yoast_wpseo_metadesc', 'Healthier-for-you office snack boxes from small UK makers, delivered to your office or to remote desks. No minimum order, pay by invoice, from £13 a head.' );
tt_set_meta( $office_id, '_yoast_wpseo_focuskw', 'office snack boxes' );

/* -------------------------------------------------------------------------
 * 3. Gifting page 36634: title + Yoast
 * ---------------------------------------------------------------------- */
tt_log( '3. Gifting page (36634)' );
$gift = get_post( 36634 );
if ( 'Corporate Snack Gifts' !== $gift->post_title ) {
	wp_update_post( array( 'ID' => 36634, 'post_title' => 'Corporate Snack Gifts' ) );
	tt_log( '  + retitled (slug untouched: ' . get_post( 36634 )->post_name . ')' );
}
tt_set_meta( 36634, '_yoast_wpseo_title', 'Corporate Snack Gifts & Employee Wellbeing Gift Boxes' );
tt_set_meta( 36634, '_yoast_wpseo_metadesc', 'Corporate snack gifts with thought in them: employee gift boxes, new-mum and menopause wellbeing gifts, new starter and client gifts. No minimum, invoiced.' );
tt_set_meta( 36634, '_yoast_wpseo_focuskw', 'corporate snack gifts' );

/* -------------------------------------------------------------------------
 * 4. Main menu (21): "Corporate Orders" -> "Corporate Gifts" with two children
 * ---------------------------------------------------------------------- */
tt_log( '4. Main menu' );
$item = get_post( 36654 );
if ( $item && 'Corporate Gifts' !== $item->post_title ) {
	wp_update_post( array( 'ID' => 36654, 'post_title' => 'Corporate Gifts' ) );
	tt_log( '  + menu item 36654 retitled Corporate Gifts' );
}
$item = get_post( 54777 );
if ( $item && 'Christmas Hampers 🎄' !== $item->post_title ) {
	wp_update_post( array( 'ID' => 54777, 'post_title' => 'Christmas Hampers 🎄' ) );
	tt_log( '  + menu item 54777 retitled Christmas Hampers' );
}
$have_office_item = false;
foreach ( wp_get_nav_menu_items( 21 ) as $mi ) {
	if ( 'page' === $mi->object && (int) $mi->object_id === (int) $office_id ) {
		$have_office_item = true;
	}
}
if ( ! $have_office_item ) {
	$mid = wp_update_nav_menu_item( 21, 0, array(
		'menu-item-title'     => 'Office Snack Boxes',
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $office_id,
		'menu-item-type'      => 'post_type',
		'menu-item-parent-id' => 36654,
		'menu-item-status'    => 'publish',
		'menu-item-position'  => 19,
	) );
	tt_log( is_wp_error( $mid ) ? '  ! office menu item failed: ' . $mid->get_error_message() : "  + office menu item $mid added under Corporate Gifts" );
} else {
	tt_log( '  = office menu item exists' );
}

/* -------------------------------------------------------------------------
 * 5. Products: Deluxe renames (slugs untouched) + Yoast, and a
 *    "Ordering for a team?" line on the short descriptions in the link map.
 * ---------------------------------------------------------------------- */
tt_log( '5. Products' );
$renames = array(
	54610 => 'Deluxe Office Snack Box (60+ snacks)',
	54627 => 'Deluxe Office Snack Box Weekly Subscription',
);
foreach ( $renames as $pid => $title ) {
	$p = get_post( $pid );
	if ( $p && $p->post_title !== $title ) {
		wp_update_post( array( 'ID' => $pid, 'post_title' => $title ) );
		tt_log( "  + $pid renamed: $title (slug " . $p->post_name . ')' );
	}
}
tt_set_meta( 54610, '_yoast_wpseo_title', 'Deluxe Office Snack Box, 60+ Healthy Snacks | Treat Trunk' );
tt_set_meta( 54610, '_yoast_wpseo_metadesc', 'Our biggest office snack box: 60+ healthy, mostly vegan snacks from small UK makers, hand-packed for office kitchens and team days. £125, pay by invoice.' );
tt_set_meta( 54627, '_yoast_wpseo_title', 'Deluxe Office Snack Box Weekly Subscription | Treat Trunk' );
tt_set_meta( 54627, '_yoast_wpseo_metadesc', 'Deluxe office snack box delivered weekly: 60+ healthy, mostly vegan snacks from small UK makers for your office kitchen. £100 a week, pause anytime, pay by invoice.' );

$link_style = 'font-weight:700;';
$excerpt_lines = array(
	40245 => '<p class="tt-corp-link"><strong>Ordering for a team?</strong> See our <a href="/office-snack-boxes/">office snack boxes</a> for volume pricing, or our <a href="/corporate-orders/">employee gift boxes</a> with branded cards.</p>',
	7076  => '<p class="tt-corp-link"><strong>Ordering for an office?</strong> Volume pricing, subscriptions and <a href="/office-snack-boxes/">office snack delivery UK</a>-wide are on our office page.</p>',
	54610 => '<p class="tt-corp-link"><strong>Comparing sizes?</strong> All our <a href="/office-snack-boxes/">office snack boxes</a>, with volume pricing and invoicing, are on one page.</p>',
	54625 => '<p class="tt-corp-link"><strong>For the office kitchen?</strong> See every <a href="/office-snack-boxes/">office snack subscription</a> option, weekly or monthly, with invoicing.</p>',
	54627 => '<p class="tt-corp-link"><strong>For the office kitchen?</strong> Compare every <a href="/office-snack-boxes/">office snack subscription</a> option, with invoicing, on our office page.</p>',
	8122  => '<p class="tt-corp-link"><strong>Buying for a colleague?</strong> The New Mum Box is one of our <a href="/corporate-orders/">staff wellbeing gifts</a>, with branded cards and invoicing for companies.</p>',
	50327 => '<p class="tt-corp-link"><strong>Buying for a colleague or team?</strong> The Menopause Box is part of our range of <a href="/corporate-orders/">wellbeing gifts for staff</a>, with invoicing for companies.</p>',
	49709 => '<p class="tt-corp-link"><strong>Ordering for a company?</strong> Our <a href="/corporate-christmas-hampers/">corporate Christmas hampers</a> page has the Signature Hamper, volume pricing and the 2026 order deadlines.</p>',
	43461 => '<p class="tt-corp-link"><strong>Buying for a team?</strong> See our <a href="/corporate-orders/">corporate snack gifts</a>: branded cards, no minimum order, invoicing.</p>',
);
foreach ( $excerpt_lines as $pid => $line ) {
	$p = get_post( $pid );
	if ( ! $p ) {
		tt_log( "  ! product $pid missing" );
		continue;
	}
	if ( false !== strpos( $p->post_excerpt, 'tt-corp-link' ) ) {
		tt_log( "  = $pid excerpt already linked" );
		continue;
	}
	wp_update_post( array( 'ID' => $pid, 'post_excerpt' => wp_slash( rtrim( $p->post_excerpt ) . "\n\n" . $line ) ) );
	tt_log( "  + $pid excerpt linked" );
}

/* -------------------------------------------------------------------------
 * 6. Blog posts: featured-pick paragraphs + commercial anchors
 * ---------------------------------------------------------------------- */
tt_log( '6. Blog posts' );
function tt_edit_post_content( $post_id, array $replacements ) {
	$p = get_post( $post_id );
	if ( ! $p ) {
		tt_log( "  ! post $post_id missing" );
		return;
	}
	$content = $p->post_content;
	if ( false !== strpos( $content, 'tt-featured-pick' ) ) {
		tt_log( "  = $post_id already edited" );
		return;
	}
	$changed = 0;
	foreach ( $replacements as $r ) {
		list( $needle, $replacement ) = $r;
		if ( substr_count( $content, $needle ) !== 1 ) {
			tt_log( "  ! $post_id anchor not unique/found: " . substr( $needle, 0, 60 ) );
			continue;
		}
		$content = str_replace( $needle, $replacement, $content );
		$changed++;
	}
	if ( $changed ) {
		wp_update_post( array( 'ID' => $post_id, 'post_content' => wp_slash( $content ) ) );
		tt_log( "  + $post_id updated ($changed edits)" );
	}
}

$pick_style = 'background:#F3F8F6;border-left:4px solid #12786C;border-radius:0 12px 12px 0;padding:18px 22px;margin:24px 0;';

// /healthy-office-snack-ideas-2026/ (54665, classic HTML)
tt_edit_post_content( 54665, array(
	array(
		"going, so here's what we'd stock in an office in 2026, and why.</p>",
		"going, so here's what we'd stock in an office in 2026, and why.</p>\n\n"
		. '<div class="tt-featured-pick" style="' . $pick_style . '"><p style="margin:0;"><strong>Want the ideas below delivered, sorted, every week?</strong> That is what our <a href="/office-snack-boxes/">office snack box subscription</a> does: twenty-plus full-size, healthier-for-you snacks from small UK makers to the office kitchen, or a letterbox box to every remote colleague, no minimum order, pause anytime, pay by invoice. From £13 a head.</p></div>',
	),
	array(
		'our <a href="/corporate-orders/">corporate orders page</a> has the full range, including bulk pricing and remote-team delivery.',
		'our <a href="/office-snack-boxes/">healthy office snacks delivered</a> page has the full range, including bulk pricing, subscriptions and remote-team delivery.',
	),
) );

// /the-best-corporate-wellbeing-gifts-.../ (37444, Gutenberg blocks)
tt_edit_post_content( 37444, array(
	array(
		"<!-- wp:heading -->\n<h2>Healthy corporate snack gifts: Treat Trunk</h2>",
		"<!-- wp:html -->\n" . '<div class="tt-featured-pick" style="' . $pick_style . '"><p style="margin:0;"><strong>Our pick for 2026: a wellbeing box built for a real moment.</strong> Most wellbeing gifts are generic. What we would send, and what we pack ourselves, is a box chosen for the moment a person is actually in: the Treat Trunk New Mum Box for someone returning from parental leave, the Menopause Box offered as a choice within a wellbeing menu, or the Full Treat Trunk of twenty-plus healthier-for-you snacks from small UK makers for everyone else. No minimum order, invoiced to your wellbeing budget, and posted to one office or through every front door. See the full range of <a href="/corporate-orders/">staff wellbeing gifts</a> and tell us who they are for.</p></div>' . "\n<!-- /wp:html -->\n\n<!-- wp:heading -->\n<h2>Healthy corporate snack gifts: Treat Trunk</h2>",
	),
	array(
		'<a href="https://treattrunk.co.uk/product/new-mum/">New Mum Healthy Snack Box</a> - developed with a maternal health nutritionist - is made for exactly that.</p>',
		'<a href="https://treattrunk.co.uk/product/new-mum/">New Mum Healthy Snack Box</a> - developed with a maternal health nutritionist - is made for exactly that, and it sits alongside the Menopause Box in our <a href="/corporate-orders/">wellness gift box for employees</a> range.</p>',
	),
	array(
		'<p>We hope these corporate wellbeing gift ideas have given you some inspiration for gifting your employees or clients!</p>',
		'<p>We hope these corporate wellbeing gift ideas have given you some inspiration for gifting your employees or clients!</p>'
		. "\n<p>Looking for a <a href=\"/corporate-orders/\">wellness gift box for employees</a> you can invoice this quarter? Tell us the occasion and headcount and we reply with a quote within 24 hours. Planning ahead for December? Our <a href=\"/corporate-christmas-hampers/\">corporate Christmas gifts UK</a> page has the Signature Hamper and the 2026 order deadlines.</p>",
	),
) );

// /9-corporate-letterbox-gifts-.../ (40581, classic HTML)
tt_edit_post_content( 40581, array(
	array(
		'<h2>1. Treat Trunk healthy snack letterbox gift</h2>',
		'<h2>1. The Treat Trunk Letterbox Gift (our pick)</h2>',
	),
	array(
		"<p>A letterbox version of our famous healthy snack subscription means your recipient gets a collection of healthy snacks in the post, with no waiting in for the postman. We've covered all the snacking bases in a slim, letterbox-friendly box that pops straight through the door. As with all our boxes, every snack is chosen for its taste <em>and</em> its natural, real-food ingredients. At just £12.99, each box is full of vegan, Sugar Sensible snacks from ethical British brands - a great fit for any staff wellbeing initiative, and ideal for clients or employees working from home.</p>",
		'<div class="tt-featured-pick" style="' . $pick_style . '"><p style="margin:0;">We would say that, but here is why it earns the top spot: it is the only letterbox gift on this list where the whole box is healthier-for-you snacks from small, ethical UK makers, vegan as standard, and it goes through any door for £13.99, or £13.00 each at 50+. Seven or eight full-size snacks in a slim box that pops straight through the letterbox, so nobody waits in for the postman. Add your branding to the card and sticker, give us a spreadsheet of addresses, pay one invoice. It is the box behind our 800-employee campaign and it works just as well for a team of eight. See all our <a href="/corporate-orders/">employee gift boxes</a>, or if you are reading this in autumn, our <a href="/corporate-christmas-hampers/">Christmas hampers for clients</a>.</p></div>',
	),
	array(
		'<p>For more on Treat Trunk\'s corporate offering, take a look at our <a href="https://treattrunk.co.uk/corporate-orders/">corporate orders page</a>.</p>',
		'<p>For more on Treat Trunk\'s corporate offering, take a look at our <a href="/corporate-orders/">employee gift boxes</a> and, for the office kitchen, our <a href="/office-snack-boxes/">office snack boxes</a>.</p>',
	),
) );

/* -------------------------------------------------------------------------
 * 7. Media library alt text for the three hamper photos
 * ---------------------------------------------------------------------- */
tt_log( '7. Image alt text' );
$alts = array(
	54964 => 'The Treat Trunk Signature Hamper: a black wicker corporate Christmas hamper with leather straps and buckles, on a wooden garden table',
	54965 => 'Lifting the leather-strapped lid of the Treat Trunk Signature Hamper in a sunny English garden',
	54966 => 'A hand-held Treat Trunk gift card above an open Signature Hamper lined with yellow tissue, ready for a personalised Christmas message',
);
foreach ( $alts as $aid => $alt ) {
	tt_set_meta( $aid, '_wp_attachment_image_alt', $alt );
}

/* -------------------------------------------------------------------------
 * 8. WP Rocket "Remove Unused CSS" entries for the rebuilt URLs, so the
 *    used-CSS is regenerated against the new markup.
 * ---------------------------------------------------------------------- */
tt_log( '8. RUCSS entries' );
global $wpdb;
$table = $wpdb->prefix . 'wpr_rucss_used_css';
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) {
	$n = $wpdb->query( "DELETE FROM `$table` WHERE url LIKE '%corporate-orders%' OR url LIKE '%corporate-christmas%' OR url LIKE '%office-snack-boxes%' OR url LIKE '%send-a-gift%'" );
	tt_log( "  + removed $n used-CSS rows" );
} else {
	tt_log( '  = no RUCSS table' );
}

WP_CLI::success( 'Data changes applied. Office page ID: ' . $office_id );
