<?php
/**
 * Template Name: Office Snack Boxes (Custom)
 *
 * /office-snack-boxes/ (new page, 2026-09-13). One page, one intent: "office
 * snack boxes" and its variants (office snack subscription, office snacks
 * delivery uk, healthy office snacks). The subscription, bulk one-off and
 * Deluxe cards that used to sit on /corporate-orders/ moved here so that
 * page can own gifting instead - see docs/corporate-seo-pages-2026-09.md.
 *
 * Prices are the live WooCommerce prices (13 Sep 2026). The one-click bulk
 * add-to-cart URLs are the same mechanism the corporate page used: the
 * bulk-discount hooks in site-core.php price 20+/50+ automatically.
 */

get_header();

$tt_letterbox_id  = 40245;
$tt_oneoff_id     = 7076;
$tt_oneoff_std    = 7077; // Standard (20-25 Snacks)
$tt_oneoff_mini   = 7078; // Mini (10-15 Snacks)
$tt_std_attr      = 'Standard (20-25 Snacks)';
$tt_mini_attr     = 'Mini (10-15 Snacks)';
$tt_full_img      = 54985; // Two open Full Treat Trunks, real contents (13 Sep 2026).
$tt_team_img      = 54986; // Row of Letterbox boxes packed for a team.
$tt_lbx_img       = 54987; // Open Letterbox box.
$tt_clip          = 54988; // 9-second packing clip, 480p MP4, muted loop.

$tt_lb20  = esc_url( add_query_arg( array( 'add-to-cart' => $tt_letterbox_id, 'quantity' => 20 ), home_url( '/' ) ) );
$tt_lb50  = esc_url( add_query_arg( array( 'add-to-cart' => $tt_letterbox_id, 'quantity' => 50 ), home_url( '/' ) ) );
$tt_full1 = esc_url( add_query_arg( array( 'add-to-cart' => $tt_oneoff_id, 'quantity' => 1, 'variation_id' => $tt_oneoff_std, 'attribute_size' => $tt_std_attr ), home_url( '/' ) ) );
$tt_full20 = esc_url( add_query_arg( array( 'add-to-cart' => $tt_oneoff_id, 'quantity' => 20, 'variation_id' => $tt_oneoff_std, 'attribute_size' => $tt_std_attr ), home_url( '/' ) ) );
$tt_full50 = esc_url( add_query_arg( array( 'add-to-cart' => $tt_oneoff_id, 'quantity' => 50, 'variation_id' => $tt_oneoff_std, 'attribute_size' => $tt_std_attr ), home_url( '/' ) ) );
$tt_mini1 = esc_url( add_query_arg( array( 'add-to-cart' => $tt_oneoff_id, 'quantity' => 1, 'variation_id' => $tt_oneoff_mini, 'attribute_size' => $tt_mini_attr ), home_url( '/' ) ) );

$tt_faqs = array(
	array( 'Can we pay for office snacks by invoice?', 'Yes. Any one-off order or subscription can be invoiced to your company. Tell us in the quote form or the order note, include your purchase order number if you have one, and we will invoice rather than charge a card. Our standard terms are payment on invoice, then dispatch.' ),
	array( 'Is there a minimum order for office snack boxes?', 'No. One Letterbox Box is a real order and so is 800. Volume pricing on the Letterbox Box and Full Treat Trunk starts at 20 and applies automatically in the basket.' ),
	array( 'Can you deliver to remote or hybrid staff at their home addresses?', 'Yes. The Letterbox Box fits through a standard letterbox so nobody has to wait in. Send us a spreadsheet of home addresses and we handle every label, dispatch tracked, and report delivery status across all recipients. One order can go to the office and to homes at the same time.' ),
	array( 'What dietary options do you offer?', 'Every box is vegetarian throughout and mostly vegan. Gluten-free and nut-aware versions are available at no extra cost, per person if needed within a single order. Tell us who needs what in the order note or the address sheet. Every box includes a contents and allergen card.' ),
	array( 'How quickly can you deliver office snacks?', 'One-off orders are hand-packed and dispatched tracked, usually within two working days, on a tracked two working day service with a first class upgrade available. Subscriptions run to a fixed schedule. If you need a box for a specific day, tell us and we will confirm before you order.' ),
	array( 'Can we pause or cancel an office snack subscription?', 'Yes, at any time, from your account, with no notice period and no phone call. Pause for August, skip a week, or cancel outright. You will never be charged for a box you have paused.' ),
	array( 'Can we add our company branding?', 'Yes, for one-off orders: branded stickers, gift cards and ribbon. Branding takes a little extra time to proof and print, so allow around two weeks and ask for it in the quote form.' ),
	array( 'Can we try one box before committing the whole office?', 'Yes. Order a single Full Treat Trunk online, or ask us for a sample in the quote form. If you are in a WeWork building, your first team box is free.' ),
);

$tt_products = array(
	array(
		'name'        => 'Treat Trunk Letterbox Office Snack Box',
		'description' => 'Letterbox-sized box of 7 to 8 healthier-for-you snacks from small UK makers, vegetarian and mostly vegan, posted through any UK letterbox. No minimum order, volume pricing from 20 boxes, pay by invoice.',
		'image'       => wp_get_attachment_image_url( $tt_lbx_img, 'large' ),
		'category'    => 'Office Snack Boxes',
		'offers'      => array( 'lowPrice' => '13.00', 'highPrice' => '15.99', 'offerCount' => 3, 'url' => get_permalink() ),
	),
	array(
		'name'        => 'Full Treat Trunk Office Snack Box (20 to 25 snacks)',
		'description' => '20 to 25 full-size healthier-for-you snacks from small UK makers for the office kitchen, vegetarian throughout and mostly vegan. One-off, or a weekly or monthly office snack subscription, pause or cancel anytime, pay by invoice.',
		'image'       => wp_get_attachment_image_url( $tt_full_img, 'large' ),
		'category'    => 'Office Snack Boxes',
		'offers'      => array( 'lowPrice' => '35.00', 'highPrice' => '44.99', 'offerCount' => 4, 'url' => get_permalink() ),
	),
	array(
		'name'        => 'Treat Trunk Deluxe Office Snack Box (60+ snacks)',
		'description' => '60+ healthier-for-you snacks from small UK makers with favourites in multiples, for office kitchens, team days and client visits. One-off £125 or £100 a week on subscription.',
		'image'       => get_the_post_thumbnail_url( 54610, 'large' ),
		'category'    => 'Office Snack Boxes',
		'offers'      => array( 'lowPrice' => '100.00', 'highPrice' => '125.00', 'offerCount' => 2, 'url' => get_permalink() ),
	),
);

$s_card  = 'background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px;';
$s_h2    = 'font-weight: 700; font-size: 34px; line-height: 1.15; margin: 0 0 12px; color: #1B2420;';
$s_h3    = 'font-weight: 700; font-size: 20px; margin: 0; color: #1B2420;';
$s_p     = 'font-size: 16px; line-height: 1.65; color: #1B2420; margin: 0;';
$s_btn   = 'background: #12786C; color: #FAFAF8; font-weight: 700; font-size: 15px; padding: 12px 22px; border-radius: 999px; text-decoration: none; display: inline-block; text-align: center;';
$s_btn2  = 'background: #FFFFFF; color: #12786C; border: 2px solid #12786C; font-weight: 700; font-size: 14px; padding: 10px 18px; border-radius: 999px; text-decoration: none; display: inline-block; text-align: center;';
$s_link  = 'color: #12786C; font-weight: 700; text-decoration: underline; text-underline-offset: 4px;';
$s_pill  = 'background: #DCEFEC; color: #0B5951; font-size: 12.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;';
$s_tick  = 'font-size: 14px; color: #5B6B68; font-weight: 600;';
$s_td    = 'padding: 14px 16px; vertical-align: top;';
?>

<div class="tt-corp-page" style="font-family: 'DM Sans', -apple-system, sans-serif; color: #1B2420; background: #FAFAF8;">

	<!-- WeWork bar, kept from the corporate page: same offer, same claim form there -->
	<div style="background: #12786C; color: #FAFAF8; text-align: center; padding: 10px 16px; font-size: 14px; font-weight: 600;">
		In a WeWork office? Your first team box is free &nbsp;&middot;&nbsp;
		<a href="<?php echo esc_url( home_url( '/corporate-orders/#wework' ) ); ?>" style="color: #FAFAF8; text-decoration: underline; text-underline-offset: 3px;">Claim yours</a>
	</div>
	<?php if ( time() < strtotime( '2026-12-18 00:00:00 Europe/London' ) ) : ?>
	<div style="background: #0B5951; color: #FAFAF8; text-align: center; padding: 9px 16px; font-size: 13.5px; font-weight: 600;">
		Christmas 2026: branded orders close Fri 20 Nov &nbsp;&middot;&nbsp;
		<a href="<?php echo esc_url( home_url( '/corporate-christmas-hampers/' ) ); ?>" style="color: #FAFAF8; text-decoration: underline; text-underline-offset: 3px;">See Christmas hampers for staff</a>
	</div>
	<?php endif; ?>

	<!-- Hero -->
	<section class="tt-corp-hero" style="display: grid; grid-template-columns: 1.05fr 1fr; gap: 48px; align-items: center; padding: 64px 48px 56px; max-width: 1240px; margin: 0 auto;">
		<div style="display: flex; flex-direction: column; gap: 22px;">
			<div style="display: flex; gap: 10px; flex-wrap: wrap;">
				<span style="<?php echo esc_attr( $s_pill ); ?>">For office kitchens &amp; remote teams</span>
				<span style="<?php echo esc_attr( $s_pill ); ?> color: #12786C;">Vegetarian, mostly vegan</span>
			</div>
			<h1 style="font-weight: 700; font-size: 44px; line-height: 1.1; margin: 0; color: #1B2420;">Office snack boxes that make snack time easier, adventurous and healthy</h1>
			<p style="font-size: 18px; line-height: 1.6; margin: 0; max-width: 54ch; color: #1B2420;">A box of healthier-for-you snacks from small, ethical UK makers, delivered to your office kitchen every week or month, or posted through the letterbox of every remote colleague. No minimum order, so a team of four is as welcome as a floor of four hundred. Pay by invoice. Pause or cancel whenever you like.</p>
			<div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
				<a href="#boxes" style="<?php echo esc_attr( $s_btn ); ?> font-size: 17px; padding: 15px 30px;">Order an office snack box</a>
				<a href="#quote" style="<?php echo esc_attr( $s_link ); ?> font-size: 16px;">Get a quote for my team &rarr;</a>
			</div>
			<div style="display: flex; gap: 10px 26px; margin-top: 6px; flex-wrap: wrap;">
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; No minimum order</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Vegetarian throughout, mostly vegan</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Gluten-free &amp; nut-aware options</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Pay by invoice</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; UK-wide, to the office or through the letterbox</span>
			</div>
		</div>
		<div style="position: relative;">
			<img src="<?php echo esc_url( wp_get_attachment_image_url( $tt_full_img, 'medium_large' ) ); ?>" width="768" height="576" alt="Two open Full Treat Trunk office snack boxes packed with full-size healthy snacks from small UK makers" style="width: 100%; height: 460px; object-fit: cover; border-radius: 24px; box-shadow: 0 24px 48px -20px rgba(31, 61, 44, 0.35);" data-skip-lazy="1" fetchpriority="high">
		</div>
	</section>

	<!-- Pricing table -->
	<section id="boxes" class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="text-align: center; margin-bottom: 28px;">
			<h2 style="<?php echo esc_attr( $s_h2 ); ?>">Pick your office snack box</h2>
			<p style="font-size: 17px; color: #1B2420; margin: 0;">Order online in minutes and volume pricing applies automatically in the basket. Want branding, a mixed order or invoicing? <a href="#quote" style="<?php echo esc_attr( $s_link ); ?>">Use the quote form</a> and we will sort it.</p>
		</div>
		<div style="overflow-x: auto;">
			<table style="width: 100%; border-collapse: collapse; background: #FFFFFF; border-radius: 20px; overflow: hidden; border: 1px solid #DCEBE9; font-size: 14.5px;">
				<thead>
					<tr style="background: #12786C; color: #FAFAF8;">
						<th style="text-align: left; padding: 14px 16px;">Box</th>
						<th style="text-align: left; padding: 14px 16px;">What&rsquo;s inside</th>
						<th style="text-align: left; padding: 14px 16px;">One-off</th>
						<th style="text-align: left; padding: 14px 16px;">20+ boxes</th>
						<th style="text-align: left; padding: 14px 16px;">50+ boxes</th>
						<th style="text-align: left; padding: 14px 16px;">Subscription</th>
						<th style="text-align: left; padding: 14px 16px;">Order</th>
					</tr>
				</thead>
				<tbody style="color: #1B2420;">
					<tr style="border-top: 1px solid #DCEBE9;">
						<td style="<?php echo esc_attr( $s_td ); ?> font-weight: 700;">Letterbox Box</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">7 to 8 healthier-for-you snacks, posts through any letterbox</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;15.99</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;13.75 each</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;13.00 each</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">quoted for teams</td>
						<td style="<?php echo esc_attr( $s_td ); ?>"><div style="display: flex; flex-direction: column; gap: 6px;"><a href="<?php echo $tt_lb20; ?>" style="<?php echo esc_attr( $s_btn ); ?> padding: 9px 14px; font-size: 13.5px;">20 boxes &pound;275</a><a href="<?php echo $tt_lb50; ?>" style="<?php echo esc_attr( $s_btn2 ); ?> padding: 7px 14px; font-size: 13.5px;">50 boxes &pound;650</a></div></td>
					</tr>
					<tr style="border-top: 1px solid #DCEBE9; background: #FAFAF8;">
						<td style="<?php echo esc_attr( $s_td ); ?> font-weight: 700;">Mini Treat Trunk</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">10 to 15 snacks</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;28.99</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">quoted</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">quoted</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;24.99 a month</td>
						<td style="<?php echo esc_attr( $s_td ); ?>"><div style="display: flex; flex-direction: column; gap: 6px;"><a href="<?php echo $tt_mini1; ?>" style="<?php echo esc_attr( $s_btn ); ?> padding: 9px 14px; font-size: 13.5px;">Order one</a><a href="<?php echo esc_url( home_url( '/product/mini-treat-trunk-monthly-subscription/' ) ); ?>" style="<?php echo esc_attr( $s_btn2 ); ?> padding: 7px 14px; font-size: 13.5px;">Monthly</a></div></td>
					</tr>
					<tr style="border-top: 1px solid #DCEBE9;">
						<td style="<?php echo esc_attr( $s_td ); ?> font-weight: 700;">Full Treat Trunk</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">20 to 25 full-size snacks</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;44.99</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;37.50 each</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;35.00 each</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;39.99 a month, or &pound;39.99 a week</td>
						<td style="<?php echo esc_attr( $s_td ); ?>"><div style="display: flex; flex-direction: column; gap: 6px;"><a href="<?php echo $tt_full20; ?>" style="<?php echo esc_attr( $s_btn ); ?> padding: 9px 14px; font-size: 13.5px;">20 boxes &pound;750</a><a href="<?php echo $tt_full50; ?>" style="<?php echo esc_attr( $s_btn2 ); ?> padding: 7px 14px; font-size: 13.5px;">50 boxes &pound;1,750</a><a href="<?php echo esc_url( home_url( '/product/treat-trunk-weekly-subscription/' ) ); ?>" style="<?php echo esc_attr( $s_btn2 ); ?> padding: 7px 14px; font-size: 13.5px;">Weekly</a><a href="<?php echo esc_url( home_url( '/product/treat-trunk-monthly-subscription/' ) ); ?>" style="<?php echo esc_attr( $s_btn2 ); ?> padding: 7px 14px; font-size: 13.5px;">Monthly</a></div></td>
					</tr>
					<tr style="border-top: 1px solid #DCEBE9; background: #FAFAF8;">
						<td style="<?php echo esc_attr( $s_td ); ?> font-weight: 700;">Deluxe Office Box</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">60+ snacks, favourites in multiples</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;125</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">quoted</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">quoted</td>
						<td style="<?php echo esc_attr( $s_td ); ?>">&pound;100 a week</td>
						<td style="<?php echo esc_attr( $s_td ); ?>"><div style="display: flex; flex-direction: column; gap: 6px;"><a href="<?php echo esc_url( home_url( '/product/corporate-snack-box/' ) ); ?>" style="<?php echo esc_attr( $s_btn ); ?> padding: 9px 14px; font-size: 13.5px;">Order one</a><a href="<?php echo esc_url( home_url( '/product/deluxe-corporate-snack-box-weekly-subscription/' ) ); ?>" style="<?php echo esc_attr( $s_btn2 ); ?> padding: 7px 14px; font-size: 13.5px;">Weekly</a></div></td>
					</tr>
				</tbody>
			</table>
		</div>
		<p style="font-size: 15px; line-height: 1.65; color: #1B2420; margin: 20px auto 0; max-width: 860px;"><strong>How to read the numbers.</strong> Every snack in a Treat Trunk is full-size, the pack you would buy in a good deli, not a bite-size sample. The Letterbox Box works out from &pound;13 a head for a whole remote team. The Full Treat Trunk at 50+ is &pound;35 for 20 to 25 full-size snacks, about &pound;1.50 a snack. The Deluxe box is about &pound;2 a snack and feeds a busy kitchen for a week. If a per-snack price elsewhere looks cheaper, check the pack size. We are not VAT registered, so there is no VAT to add.</p>

		<h3 style="font-weight: 700; font-size: 24px; margin: 40px 0 18px; color: #1B2420; text-align: center;">Which office snack box is right for your team?</h3>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
			<?php
			$tt_which = array(
				array( 'A small or hybrid team', 'The Letterbox Box, one per person, posted to home and office addresses alike. No minimum, so ten boxes is a perfectly good order.', '#boxes', 'Letterbox boxes' ),
				array( 'One office kitchen', 'The Full Treat Trunk on a weekly or monthly subscription. Twenty-plus full-size snacks, refreshed so the kitchen never gets boring.', home_url( '/product/treat-trunk-weekly-subscription/' ), 'Weekly subscription' ),
				array( 'A big office, team days or client visits', 'The Deluxe Office Box, 60+ snacks with the favourites in multiples so nobody misses out, weekly if you like.', home_url( '/product/corporate-snack-box/' ), 'Deluxe box' ),
				array( 'Not sure', 'Tell us the headcount and how many are remote and we will suggest a mix.', '#quote', 'Ask us' ),
			);
			foreach ( $tt_which as $tt_w ) : ?>
				<div style="<?php echo esc_attr( $s_card ); ?> padding: 24px 24px 26px; display: flex; flex-direction: column; gap: 10px;">
					<h4 style="font-weight: 700; font-size: 17px; margin: 0; color: #1B2420;"><?php echo esc_html( $tt_w[0] ); ?></h4>
					<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1;"><?php echo esc_html( $tt_w[1] ); ?></p>
					<a href="<?php echo esc_url( $tt_w[2] ); ?>" style="<?php echo esc_attr( $s_link ); ?> font-size: 14.5px;"><?php echo esc_html( $tt_w[3] ); ?> &rarr;</a>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Subscription + what's in the box -->
	<section class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: start;">
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 32px 32px 34px;">
				<h2 style="font-weight: 700; font-size: 26px; line-height: 1.2; margin: 0 0 12px; color: #1B2420;">Office snack subscription: weekly or monthly, pause whenever you like</h2>
				<p style="<?php echo esc_attr( $s_p ); ?> font-size: 15.5px;">The weekly office subscription sends the same Full Treat Trunk four weeks running, then we refresh the whole selection with a new set of snacks, so the kitchen gets favourites and discoveries in the right balance. The monthly subscription brings a different selection every month, posted around the 10th. Both can be paused, skipped or cancelled from your account in a couple of clicks, no phone call needed, and both can be invoiced to your company rather than charged to a card.</p>
				<div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px;">
					<a href="<?php echo esc_url( home_url( '/product/treat-trunk-weekly-subscription/' ) ); ?>" style="<?php echo esc_attr( $s_btn ); ?>">Weekly &pound;39.99</a>
					<a href="<?php echo esc_url( home_url( '/product/treat-trunk-monthly-subscription/' ) ); ?>" style="<?php echo esc_attr( $s_btn2 ); ?>">Monthly &pound;39.99</a>
					<a href="<?php echo esc_url( home_url( '/product/deluxe-corporate-snack-box-weekly-subscription/' ) ); ?>" style="<?php echo esc_attr( $s_btn2 ); ?>">Deluxe weekly &pound;100</a>
				</div>
			</div>
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 32px 32px 34px;">
				<h2 style="font-weight: 700; font-size: 26px; line-height: 1.2; margin: 0 0 12px; color: #1B2420;">Healthy office snacks, the Treat Trunk way</h2>
				<p style="<?php echo esc_attr( $s_p ); ?> font-size: 15.5px; margin-bottom: 12px;">We choose every snack for two things: it has to taste properly good, and it has to be made with real ingredients by people we would happily introduce you to. That means protein-packed brownies from Vive, creamy Caroboo carob bars, Boundless activated nuts and seeds, and a rotating cast of small British makers you will not find in the meeting-room vending machine. Vegetarian throughout and mostly vegan, sugar-sensible by default, with gluten-free and nut-aware boxes available when you tell us who needs them.</p>
				<p style="<?php echo esc_attr( $s_p ); ?> font-size: 15.5px;">Our whole idea is making snack time easier, adventurous and healthy. For an office that means fewer 3pm crashes, a kitchen people actually gather in, and the small daily signal that somebody thought about the team.</p>
				<figure style="margin: 18px 0 0;">
					<video muted autoplay loop playsinline preload="metadata" poster="<?php echo esc_url( wp_get_attachment_image_url( $tt_full_img, 'medium_large' ) ); ?>" style="width: 100%; max-height: 420px; object-fit: cover; border-radius: 16px; display: block; background: #DCEFEC;" aria-label="A Full Treat Trunk office snack box being packed">
						<source src="<?php echo esc_url( wp_get_attachment_url( $tt_clip ) ); ?>" type="video/mp4">
					</video>
					<figcaption style="font-size: 13px; color: #5B6B68; margin: 8px 4px 0; line-height: 1.5;">A Full Treat Trunk being packed: 20 to 25 full-size snacks, every one chosen by hand.</figcaption>
				</figure>
			</div>
		</div>
	</section>

	<!-- Delivery + invoice -->
	<section class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; align-items: start;">
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 0 0 34px; overflow: hidden;">
				<img src="<?php echo esc_url( wp_get_attachment_image_url( $tt_team_img, 'medium_large' ) ); ?>" width="768" height="1024" alt="A row of open Treat Trunk Letterbox snack boxes being packed for a remote team" style="width: 100%; height: 240px; object-fit: cover; display: block; margin-bottom: 24px;">
				<div style="padding: 0 32px;">
				<h2 style="font-weight: 700; font-size: 26px; line-height: 1.2; margin: 0 0 14px; color: #1B2420;">Office snacks delivery across the UK</h2>
				<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: 15px; line-height: 1.6; color: #1B2420;">
					<li><strong>To your office:</strong> hand-packed and dispatched tracked, usually within two working days of the order, on a two working day service with a first class upgrade available. Subscriptions run to a fixed schedule so the kitchen is never empty.</li>
					<li><strong>To remote colleagues:</strong> the Letterbox Box fits through a standard letterbox, so nobody waits in. Send us a spreadsheet of addresses and we handle every label.</li>
					<li><strong>Both at once:</strong> one order can go to the office and to home addresses. Tell us the split in the quote form.</li>
					<li><strong>Delivery reporting:</strong> for multi-address orders we report delivery status across all recipients.</li>
				</ul>
				</div>
			</div>
			<div style="background: #12786C; color: #FAFAF8; border-radius: 20px; padding: 32px 32px 34px;">
				<h2 style="font-weight: 700; font-size: 26px; line-height: 1.2; margin: 0 0 14px; color: #FAFAF8;">Pay by invoice</h2>
				<p style="font-size: 15.5px; line-height: 1.65; color: #FAFAF8; margin: 0 0 12px;">Any office order can be invoiced to your company instead of paid by card. Ask for it in the quote form or the order note and we will send a proper invoice with your purchase order number on it. Our standard terms are simple: payment on invoice, then we dispatch. Subscriptions can be invoiced monthly.</p>
				<p style="font-size: 15.5px; line-height: 1.65; color: #B9DBD6; margin: 0;">No card on file, no expense-claim chasing, and no VAT to add: Treat Trunk is not VAT registered.</p>
				<a href="#quote" style="<?php echo esc_attr( $s_btn ); ?> background: #FAFAF8; color: #0B5951; margin-top: 18px;">Set up invoicing for my team</a>
			</div>
		</div>
	</section>

	<!-- Comparison -->
	<section class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 960px; margin: 0 auto;">
		<div style="text-align: center; margin-bottom: 24px;">
			<h2 style="<?php echo esc_attr( $s_h2 ); ?> font-size: 30px;">Why teams choose Treat Trunk over a bigger office snack company</h2>
			<p style="font-size: 16px; color: #1B2420; margin: 0;">It is not just price; it is what is actually in the box, and how you are treated when you are not a 500-seat office.</p>
		</div>
		<div style="overflow-x: auto;">
			<table style="width: 100%; border-collapse: collapse; background: #FFFFFF; border-radius: 20px; overflow: hidden; border: 1px solid #DCEBE9; font-size: 14.5px;">
				<thead>
					<tr style="background: #12786C;">
						<th style="text-align: left; padding: 14px 18px; color: #B9DBD6;"></th>
						<th style="text-align: left; padding: 14px 18px; color: #FAFAF8;">Treat Trunk</th>
						<th style="text-align: left; padding: 14px 18px; color: #B9DBD6;">A typical office snack supplier</th>
					</tr>
				</thead>
				<tbody style="color: #1B2420;">
					<?php
					$tt_cmp = array(
						array( 'Minimum order', 'None', 'Often 50 snacks or more' ),
						array( 'What &ldquo;healthy&rdquo; means', 'The whole point: real ingredients, sugar-sensible, mostly vegan', 'A &ldquo;healthier&rdquo; range within a standard mix' ),
						array( 'Where the snacks come from', 'Small, ethical UK makers we know', 'Mostly big-brand multipacks' ),
						array( 'Remote colleagues', 'Letterbox boxes to every home address', 'Office delivery only' ),
						array( 'Dietary needs', 'Vegan, vegetarian, gluten-free and nut-aware per person, within one order', 'A single mixed selection' ),
						array( 'Who packs it', 'A family-run business, by hand', 'A warehouse line' ),
					);
					foreach ( $tt_cmp as $tt_k => $tt_row ) : ?>
						<tr style="border-top: 1px solid #DCEBE9;<?php echo $tt_k % 2 ? ' background: #FAFAF8;' : ''; ?>">
							<td style="padding: 14px 18px; font-weight: 700;"><?php echo $tt_row[0]; ?></td>
							<td style="padding: 14px 18px;">&#10003; <?php echo $tt_row[1]; ?></td>
							<td style="padding: 14px 18px; color: #5B6B68;"><?php echo $tt_row[2]; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>

	<?php
	$tt_quotes = array( 'hst', 'home' );
	include TT_CORP_UI_DIR . 'templates/parts/trust.php';

	$tt_form = array(
		'id'      => 'office',
		'source'  => 'office-snack-boxes',
		'heading' => 'Get a quote for your team',
		'intro'   => 'Tell us the headcount, how many are remote, and whether you want a one-off or a subscription. We reply personally, usually the same day.',
		'bullets' => array( 'hello@treattrunk.co.uk', 'Volume pricing and company invoicing', 'Branded stickers and cards on one-off orders', 'Free first box for WeWork members' ),
		'fields'  => array(
			array( 'name' => 'company', 'label' => 'Company', 'type' => 'text' ),
			array( 'name' => 'headcount', 'label' => 'Rough headcount', 'type' => 'text', 'placeholder' => 'e.g. 25 in the office, 15 remote' ),
			array( 'name' => 'box', 'label' => 'Which box?', 'type' => 'select', 'options' => array( 'Letterbox Box', 'Mini Treat Trunk', 'Full Treat Trunk', 'Deluxe Office Box', 'A mix', 'Not sure yet' ) ),
			array( 'name' => 'cadence', 'label' => 'One-off or subscription?', 'type' => 'select', 'options' => array( 'One-off', 'Weekly subscription', 'Monthly subscription', 'Not sure yet' ) ),
			array( 'name' => 'addresses', 'label' => 'One address or many?', 'type' => 'select', 'options' => array( 'One office address', 'Individual home addresses', 'Both' ) ),
		),
		'message' => array( 'label' => 'Anything else', 'hint' => '(dietary needs, budget, invoicing, branding)', 'placeholder' => 'e.g. 2 gluten-free, invoice to our finance team with a PO number' ),
		'button'  => 'Get my office snack quote',
		'success' => 'Thanks, we have your enquiry. We reply personally, usually the same day and always within 24 hours.',
	);
	include TT_CORP_UI_DIR . 'templates/parts/quote-form.php';

	$tt_faq_heading = 'Office snack box FAQs';
	include TT_CORP_UI_DIR . 'templates/parts/faqs.php';
	?>

	<!-- Cross-links (one intent per page) -->
	<section class="tt-corp-section" style="padding: 24px 48px 72px; max-width: 860px; margin: 0 auto;">
		<p style="font-size: 15.5px; line-height: 1.7; color: #1B2420; margin: 0;">Sending a gift rather than stocking the kitchen? See our <a href="<?php echo esc_url( home_url( '/corporate-orders/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">corporate snack gifts and employee wellbeing boxes</a>. Planning December? Our <a href="<?php echo esc_url( home_url( '/corporate-christmas-hampers/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">Christmas hampers for staff</a> and clients are alcohol-free as standard. For ideas on what to stock, read our guide to <a href="<?php echo esc_url( home_url( '/healthy-office-snack-ideas-2026/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">healthy office snack ideas</a>.</p>
	</section>

	<?php include TT_CORP_UI_DIR . 'templates/parts/schema.php'; ?>

</div>

<?php
get_footer();
