<?php
/**
 * Template Name: Corporate Christmas Hampers (Custom)
 *
 * /corporate-christmas-hampers/ (page 54774, re-slugged from
 * /corporate-christmas-gifting/ on 2026-09-13 - WordPress' own old-slug
 * redirect 301s the previous URL).
 *
 * One page, one intent: "corporate christmas hampers" and its variants
 * (christmas hampers for employees / staff / clients, corporate christmas
 * gifts uk, luxury christmas hamper for clients, alcohol-free christmas
 * hamper). Office snacks and year-round gifting live on their own pages -
 * see docs/corporate-seo-pages-2026-09.md section 0 for the keyword map.
 *
 * Copy mirrors the Signature Hamper lookbook sent to clients 2026-09-11.
 * Prices are the Christmas 2026 corporate rates. Same server-rendered pattern
 * as the other corporate-ui templates: theme header/footer, inline styles,
 * shared .tt-corp-* classes for the responsive rules in corporate-orders.css.
 */

get_header();

$tt_hero_img = 54964; // Closed black wicker hamper in the garden (lookbook cover).
$tt_lid_img  = 54965; // Lifting the leather-strapped lid.
$tt_card_img = 54966; // Hand-written gift card over the open hamper.
$tt_full_img = 54985; // Two open Full Treat Trunks, real contents (13 Sep 2026).
$tt_lbx_img  = 54987; // Open Letterbox gift box, real contents.

$tt_faqs = array(
	array( 'When do I need to order corporate Christmas hampers by?', 'Three dates matter. Branded and bespoke orders close on Friday 20 November, because gift cards, ribbon and sleeves need proofing and printing. Bulk orders to a single office address need to be confirmed by Friday 11 December. Individual gifts posted to home addresses need to be confirmed by Thursday 17 December. If you order early we can hold dispatch to the week you choose.' ),
	array( 'Can you put our company branding on the hampers?', 'Yes. We can print your artwork and message on the gift card, and carry your branding on the ribbon, a sleeve or the basket tag. We produce mock-ups of every branded element for your sign-off before we pack anything. Branding costs are confirmed in your written quote, and branded orders close on 20 November.' ),
	array( 'Is the Signature Hamper really alcohol-free?', 'Yes, every hamper is alcohol-free as standard. Instead of a bottle it includes a premium alcohol-free pour to toast with, so every recipient receives the very same gift whatever their circumstances. We do not add alcohol to hampers.' ),
	array( 'Can you deliver to our clients\' and employees\' home addresses, not just our office?', 'Both. Send the whole order to one office address for a team celebration, or give us your recipient list in our template and we handle every label. Every hamper is dispatched tracked, and we report delivery status across all recipients. Home addresses need to be confirmed by Thursday 17 December.' ),
	array( 'Is there a minimum order?', 'No. We have sent five hampers to a founder\'s best clients and an 800-employee letterbox campaign, and both got the same care. Volume pricing on the Full Treat Trunk and Letterbox Gift starts at 20. If you would like to judge the quality first, ask us about a sample.' ),
	array( 'Can we pay by invoice, and can a mixed order go on one invoice?', 'Yes to both. Your company is invoiced directly for the whole order, hampers for clients and boxes for the team included, on one invoice. Our standard terms are payment on invoice, then dispatch, so paying promptly is what secures your delivery week.' ),
	array( 'Do you deliver corporate Christmas hampers outside the UK?', 'UK mainland delivery is included in the hamper price. For Northern Ireland, the Highlands and Islands, and international addresses we quote delivery separately, so tell us where the recipients are and we will include it in your written quote.' ),
	array( 'Can you cater for vegan, vegetarian, gluten-free or kosher recipients?', 'We build vegan, vegetarian and gluten-free versions of every tier at no extra cost; just tag each recipient in the address sheet. Kosher is handled case by case once we know how many recipients need it. We cannot offer bespoke allergy builds beyond those, so every hamper carries a full contents and allergen card.' ),
);

$tt_products = array(
	array(
		'name'        => 'Treat Trunk Signature Hamper',
		'description' => 'Luxury corporate Christmas hamper: black wicker basket with leather straps, 20 gift-grade products from small British makers, ribbon, wood wool and personalised gift card. Alcohol-free as standard. Vegan, vegetarian and gluten-free versions at no extra cost.',
		'image'       => wp_get_attachment_image_url( $tt_hero_img, 'large' ),
		'category'    => 'Corporate Christmas Hampers',
		'offers'      => array( 'lowPrice' => '125.00', 'highPrice' => '149.00', 'offerCount' => 2, 'url' => get_permalink() ),
	),
	array(
		'name'        => 'Full Treat Trunk Corporate Christmas Gift Box',
		'description' => '20 to 25 full-size healthier-for-you snacks from small UK makers, vegetarian throughout and mostly vegan. Volume pricing from 20 boxes, delivered to one office or to individual home addresses.',
		'image'       => wp_get_attachment_image_url( $tt_full_img, 'large' ),
		'category'    => 'Corporate Christmas Hampers',
		'offers'      => array( 'lowPrice' => '35.00', 'highPrice' => '37.50', 'offerCount' => 2, 'url' => get_permalink(), 'eligibleQuantity' => array( '@type' => 'QuantitativeValue', 'minValue' => 20 ) ),
	),
	array(
		'name'        => 'Treat Trunk Letterbox Christmas Gift',
		'description' => 'Letterbox-sized selection of 7 to 8 healthier-for-you snacks from small UK makers, posted through any UK letterbox. Volume pricing from 20 gifts.',
		'image'       => wp_get_attachment_image_url( $tt_lbx_img, 'large' ),
		'category'    => 'Corporate Christmas Hampers',
		'offers'      => array( 'lowPrice' => '13.00', 'highPrice' => '13.99', 'offerCount' => 3, 'url' => get_permalink() ),
	),
);

$s_card  = 'background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px;';
$s_h2    = 'font-weight: 700; font-size: 34px; line-height: 1.15; margin: 0 0 12px; color: #1B2420;';
$s_h3    = 'font-weight: 700; font-size: 20px; margin: 0; color: #1B2420;';
$s_p     = 'font-size: 16px; line-height: 1.65; color: #1B2420; margin: 0;';
$s_btn   = 'background: #12786C; color: #FAFAF8; font-weight: 700; font-size: 16px; padding: 14px 28px; border-radius: 999px; text-decoration: none; display: inline-block; text-align: center;';
$s_link  = 'color: #12786C; font-weight: 700; text-decoration: underline; text-underline-offset: 4px;';
$s_pill  = 'background: #DCEFEC; color: #0B5951; font-size: 12.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;';
$s_tick  = 'font-size: 14px; color: #5B6B68; font-weight: 600;';
$s_price = 'font-weight: 700; font-size: 17px; color: #0B5951; margin: 0;';
?>

<div class="tt-corp-page" style="font-family: 'DM Sans', -apple-system, sans-serif; color: #1B2420; background: #FAFAF8;">

	<!-- Deadline ribbon: the page's urgency device, repeated in full lower down -->
	<div style="background: #0B5951; color: #FAFAF8; text-align: center; padding: 10px 16px; font-size: 14px; font-weight: 600; line-height: 1.5;">
		Christmas 2026 deadlines: branded &amp; bespoke orders close <strong>Fri 20 Nov</strong> &nbsp;&middot;&nbsp; bulk to one office by <strong>Fri 11 Dec</strong> &nbsp;&middot;&nbsp; home addresses by <strong>Thu 17 Dec</strong>
	</div>

	<!-- Hero -->
	<section class="tt-corp-hero" style="display: grid; grid-template-columns: 1.05fr 1fr; gap: 48px; align-items: center; padding: 64px 48px 56px; max-width: 1240px; margin: 0 auto;">
		<div style="display: flex; flex-direction: column; gap: 22px;">
			<div style="display: flex; gap: 10px; flex-wrap: wrap;">
				<span style="<?php echo esc_attr( $s_pill ); ?>">Christmas 2026</span>
				<span style="<?php echo esc_attr( $s_pill ); ?> color: #12786C;">Alcohol-free as standard</span>
			</div>
			<h1 style="font-weight: 700; font-size: 44px; line-height: 1.1; margin: 0; color: #1B2420;">Corporate Christmas hampers, made a little healthier</h1>
			<p style="font-size: 18px; line-height: 1.6; margin: 0; max-width: 54ch; color: #1B2420;">Every December the same tin of biscuits and bottle of red do the rounds. This year, send your clients and your team something they will actually finish: a black wicker hamper of twenty gift-grade discoveries from small, ethical British makers, healthier-for-you and alcohol-free as standard, hand-packed by our family and delivered tracked to one office or a hundred front doors. One order, one invoice.</p>
			<div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
				<a href="#quote" style="<?php echo esc_attr( $s_btn ); ?>">Get a written quote within 24 hours</a>
				<a href="#tiers" style="<?php echo esc_attr( $s_link ); ?> font-size: 16px;">See the three gift tiers &rarr;</a>
			</div>
			<div style="display: flex; gap: 10px 26px; margin-top: 6px; flex-wrap: wrap;">
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; No minimum order</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Alcohol-free as standard</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Vegan, vegetarian &amp; gluten-free at no extra cost</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Pay by invoice</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Trusted by Nissan, SA Law &amp; 800+ box orders</span>
			</div>
		</div>
		<figure style="margin: 0; position: relative;">
			<?php echo wp_get_attachment_image( $tt_hero_img, 'large', false, array(
				'alt'           => 'The Treat Trunk Signature Hamper: a black wicker corporate Christmas hamper with leather straps and buckles, on a wooden garden table',
				'style'         => 'width: 100%; height: 520px; object-fit: cover; border-radius: 24px; box-shadow: 0 24px 48px -20px rgba(31, 61, 44, 0.35);',
				'data-skip-lazy' => '1',
				'fetchpriority' => 'high',
			) ); ?>
			<figcaption style="font-size: 13px; color: #5B6B68; margin: 10px 4px 0; line-height: 1.5;">The Signature Hamper, ready to send. Black wicker, hand-stitched leather straps, and twenty gift-grade discoveries inside.</figcaption>
		</figure>
	</section>

	<!-- Signature Hamper -->
	<section id="signature" class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; align-items: start;">
			<div style="display: flex; flex-direction: column; gap: 18px;">
				<span style="<?php echo esc_attr( $s_pill ); ?> align-self: flex-start; background: #1B2420; color: #F2A93B;">Clients &middot; directors &middot; VIPs</span>
				<h2 style="<?php echo esc_attr( $s_h2 ); ?>">The Signature Hamper: a luxury Christmas hamper for clients, directors and the people who made your year</h2>
				<p style="font-weight: 700; font-size: 22px; line-height: 1.4; color: #0B5951; margin: 0;">&pound;149 per hamper <span style="font-weight: 600; font-size: 15.5px; color: #5B6B68;">&middot; &pound;125 for companies who have ordered from Treat Trunk before &middot; UK mainland delivery included</span></p>
				<p style="<?php echo esc_attr( $s_p ); ?>">Each Signature Hamper arrives in our black wicker basket with hand-stitched leather straps: a modern take on the classic English hamper, the kind that sits on an executive&rsquo;s desk rather than under it. Inside, a generous spread of exceptional food arranged in tiers on wood wool, finished with ribbon and a personalised gift card.</p>
				<p style="<?php echo esc_attr( $s_p ); ?>">Everything is chosen the Treat Trunk way: a little healthier than the traditional hamper, and sourced almost entirely from small, independent, ethically minded British makers. Award-winning truffle houses, bean-to-bar chocolate makers and family nut roasters rather than the big factory names. It is the kind of spread your clients will finish, photograph, and ask you about.</p>
				<p style="<?php echo esc_attr( $s_p ); ?>">Every hamper is hand-packed by our team, a family-run business rather than a warehouse line. Your clients receive something made with the same care you put into the relationship.</p>
				<a href="#quote" style="<?php echo esc_attr( $s_btn ); ?> align-self: flex-start;">Quote me for Signature Hampers</a>
			</div>
			<figure style="margin: 0;">
				<?php echo wp_get_attachment_image( $tt_lid_img, 'large', false, array(
					'alt'   => 'Lifting the leather-strapped lid of the Treat Trunk Signature Hamper in a sunny English garden',
					'style' => 'width: 100%; height: 560px; object-fit: cover; object-position: center 40%; border-radius: 24px;',
				) ); ?>
				<figcaption style="font-size: 13px; color: #5B6B68; margin: 10px 4px 0; line-height: 1.5;">Every hamper is packed and closed by hand, by us, before it goes out tracked to your client&rsquo;s desk.</figcaption>
			</figure>
		</div>

		<!-- What's inside -->
		<div style="margin-top: 48px; background: #1B2420; color: #FAF4E6; border-radius: 28px; padding: 44px 40px;">
			<h3 style="font-weight: 700; font-size: 26px; margin: 0 0 8px; color: #FAF4E6;">What&rsquo;s inside: twenty gift-grade discoveries</h3>
			<p style="font-size: 15.5px; line-height: 1.6; color: rgba(250, 244, 230, 0.8); margin: 0 0 24px; max-width: 70ch;">A taste of the categories inside. The final twenty products are curated fresh for each order, confirmed with you before packing where you would like a say, and listed on the contents card in every hamper.</p>
			<ul style="list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 10px;">
				<?php
				$tt_inside = array(
					array( 'Hand-rolled truffles', 'dusted in cocoa and made in Britain' ),
					array( 'Caramelised nuts', 'roasted in small batches' ),
					array( 'Spanish olives', 'the deli counter classic' ),
					array( 'Gourmet popcorn', 'air-popped and hand-finished' ),
					array( 'Medjool dates', 'nature&rsquo;s caramel' ),
					array( 'Truffle crisps', 'the luxury hamper essential' ),
					array( 'Alcohol-free pours', 'something special to toast with' ),
					array( 'Single-origin chocolate', 'bean-to-bar and ethically traded' ),
					array( 'Gourmet vegan sweets', 'all the joy and none of the gelatine' ),
				);
				foreach ( $tt_inside as $tt_i ) : ?>
					<li style="background: rgba(250, 244, 230, 0.07); border: 1px solid rgba(250, 244, 230, 0.22); border-radius: 12px; padding: 14px 16px;">
						<span style="display: block; font-weight: 700; font-size: 15.5px; color: #FAF4E6;"><?php echo $tt_i[0]; ?></span>
						<span style="display: block; font-size: 13px; color: rgba(250, 244, 230, 0.7); margin-top: 3px;"><?php echo $tt_i[1]; ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 20px;">
				<?php foreach ( array( 'Healthier-for-you', 'Small independent makers', 'Ethically sourced', 'Alcohol-free by design' ) as $tt_pl ) : ?>
					<span style="padding: 6px 13px; border-radius: 999px; font-size: 12px; font-weight: 700; background: rgba(242, 169, 59, 0.16); color: #F2A93B; border: 1px solid rgba(242, 169, 59, 0.4);"><?php echo esc_html( $tt_pl ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-top: 32px;">
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 30px 30px 32px;">
				<h3 style="<?php echo esc_attr( $s_h3 ); ?> margin-bottom: 12px;">Why our Christmas hampers are alcohol-free as standard</h3>
				<p style="font-size: 15.5px; line-height: 1.65; color: #1B2420; margin: 0;">Most luxury hampers are built around a bottle. Ours are built around exceptional food and a premium alcohol-free pour, and that changes more than you might think. Every recipient opens the very same hamper, whether they are pregnant, driving home, observant, in recovery or simply not a drinker, and every thank-you note describes the same gift. Nobody in HR has to keep a list. If you have been searching for an alcohol-free Christmas hamper that still feels properly luxurious, this is the one we would send to our own best clients.</p>
			</div>
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 30px 30px 32px;">
				<h3 style="<?php echo esc_attr( $s_h3 ); ?> margin-bottom: 12px;">What the price includes</h3>
				<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 15px; line-height: 1.5; color: #1B2420;">
					<li>&#10003; 20 gift-grade products from independent makers</li>
					<li>&#10003; Black wicker basket, leather straps, ribbon and wood wool</li>
					<li>&#10003; Personalised gift card and tag</li>
					<li>&#10003; Tracked UK mainland delivery, with delivery reporting across all recipients</li>
					<li>&#10003; Branded elements produced and proofed (branding costs confirmed in your written quote)</li>
					<li>&#10003; One invoice for the whole order</li>
				</ul>
				<p style="font-size: 13.5px; line-height: 1.6; color: #5B6B68; margin: 16px 0 0;">&pound;125 per hamper is our rate for companies that have ordered from Treat Trunk before, a saving of &pound;24 on the standard &pound;149. Both include UK mainland delivery. All prices inclusive of VAT.</p>
			</div>
		</div>
	</section>

	<!-- Volume tiers -->
	<section id="tiers" class="tt-corp-section" style="padding: 40px 48px; max-width: 1240px; margin: 0 auto;">
		<div style="text-align: center; max-width: 760px; margin: 0 auto 36px;">
			<h2 style="<?php echo esc_attr( $s_h2 ); ?>">Christmas hampers for employees and whole teams: the volume tiers</h2>
			<p style="font-size: 17px; line-height: 1.6; color: #1B2420; margin: 0;">Not everyone on the list needs wicker. Send the Signature Hamper to clients and directors, and one of these to the wider team, all on the same order and the same invoice.</p>
		</div>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
			<div class="tt-corp-card-wrap" style="<?php echo esc_attr( $s_card ); ?> overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="<?php echo esc_url( wp_get_attachment_image_url( $tt_full_img, 'medium_large' ) ); ?>" width="768" height="576" alt="The Full Treat Trunk: two open boxes of full-size healthy snacks from small UK makers, the Christmas gift box for employees" style="width: 100%; height: 240px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #12786C; color: #FAFAF8; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Team thank-yous</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="<?php echo esc_attr( $s_h3 ); ?>">The Full Treat Trunk: 20 to 25 full-size healthy snacks</h3>
					<p style="<?php echo esc_attr( $s_price ); ?>">&pound;37.50 each at 20+ &middot; &pound;35.00 each at 50+</p>
					<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1;">Our original box, and still the one we are proudest of. Twenty to twenty-five full-size, healthier-for-you snacks from small UK makers, vegetarian throughout and mostly vegan, in a box that makes an office kitchen or a home doorstep feel like Christmas morning. The generous all-rounder for team thank-yous and end-of-year celebration days.</p>
					<a href="#quote" style="<?php echo esc_attr( $s_btn ); ?> padding: 12px 0;">Quote me for Full Treat Trunks</a>
				</div>
			</div>
			<div class="tt-corp-card-wrap" style="<?php echo esc_attr( $s_card ); ?> overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="<?php echo esc_url( wp_get_attachment_image_url( $tt_lbx_img, 'medium_large' ) ); ?>" width="768" height="576" alt="The Letterbox Gift: an open slim box of seven healthy snacks with yellow tissue, posts through any letterbox" style="width: 100%; height: 240px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #12786C; color: #FAFAF8; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Whole company</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="<?php echo esc_attr( $s_h3 ); ?>">The Letterbox Gift: posts through any door</h3>
					<p style="<?php echo esc_attr( $s_price ); ?>">&pound;13.99 each &middot; &pound;13.75 at 20+ &middot; &pound;13.00 at 50+</p>
					<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1;">Seven or eight of the same healthier-for-you snacks, in a slim box that fits through a standard letterbox, so nobody on a hybrid or remote team has to wait in for a courier. Give us a spreadsheet of home addresses and we handle every label. The classic pick when the whole company is on the list.</p>
					<a href="#quote" style="<?php echo esc_attr( $s_btn ); ?> padding: 12px 0;">Quote me for Letterbox Gifts</a>
				</div>
			</div>
		</div>

		<div style="overflow-x: auto; margin-top: 28px;">
			<table style="width: 100%; border-collapse: collapse; background: #FFFFFF; border-radius: 20px; overflow: hidden; border: 1px solid #DCEBE9; font-size: 14.5px;">
				<thead>
					<tr style="background: #12786C; color: #FAFAF8;">
						<th style="text-align: left; padding: 14px 18px;">Gift</th>
						<th style="text-align: left; padding: 14px 18px;">What arrives</th>
						<th style="text-align: left; padding: 14px 18px;">1 to 19</th>
						<th style="text-align: left; padding: 14px 18px;">20 to 49</th>
						<th style="text-align: left; padding: 14px 18px;">50+</th>
						<th style="text-align: left; padding: 14px 18px;">Delivery</th>
					</tr>
				</thead>
				<tbody style="color: #1B2420;">
					<tr style="border-top: 1px solid #DCEBE9;">
						<td style="padding: 14px 18px; font-weight: 700;">Signature Hamper</td>
						<td style="padding: 14px 18px;">20 gift-grade products in black wicker, leather straps, ribbon, wood wool, personalised card</td>
						<td style="padding: 14px 18px;">&pound;149 <span style="color: #5B6B68;">(&pound;125 returning customers)</span></td>
						<td style="padding: 14px 18px;">&pound;149 <span style="color: #5B6B68;">(&pound;125)</span></td>
						<td style="padding: 14px 18px;">&pound;149 <span style="color: #5B6B68;">(&pound;125)</span></td>
						<td style="padding: 14px 18px;">UK mainland included, tracked</td>
					</tr>
					<tr style="border-top: 1px solid #DCEBE9; background: #FAFAF8;">
						<td style="padding: 14px 18px; font-weight: 700;">Full Treat Trunk</td>
						<td style="padding: 14px 18px;">20 to 25 full-size healthy snacks</td>
						<td style="padding: 14px 18px;">quoted</td>
						<td style="padding: 14px 18px;">&pound;37.50</td>
						<td style="padding: 14px 18px;">&pound;35.00</td>
						<td style="padding: 14px 18px;">One office, or individual homes</td>
					</tr>
					<tr style="border-top: 1px solid #DCEBE9;">
						<td style="padding: 14px 18px; font-weight: 700;">Letterbox Gift</td>
						<td style="padding: 14px 18px;">7 to 8 healthy snacks, letterbox-sized</td>
						<td style="padding: 14px 18px;">&pound;13.99</td>
						<td style="padding: 14px 18px;">&pound;13.75</td>
						<td style="padding: 14px 18px;">&pound;13.00</td>
						<td style="padding: 14px 18px;">Through any UK letterbox</td>
					</tr>
				</tbody>
			</table>
		</div>
		<p style="text-align: center; font-size: 13.5px; color: #5B6B68; margin: 14px 0 0;">Dietary versions at no extra cost on every tier. Mixed orders welcome. All prices inclusive of VAT.</p>
	</section>

	<!-- Deadlines -->
	<section id="deadlines" class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="<?php echo esc_attr( $s_card ); ?> padding: 36px 40px;">
			<h2 style="font-weight: 700; font-size: 28px; margin: 0 0 20px; color: #1B2420;">Christmas 2026 order deadlines</h2>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
				<div style="border-left: 3px solid #12786C; padding-left: 16px;">
					<p style="font-weight: 700; font-size: 17px; margin: 0; color: #0B5951;">Friday 20 November</p>
					<p style="font-size: 14.5px; line-height: 1.5; margin: 4px 0 0; color: #1B2420;">Branded and bespoke orders close: custom gift cards, ribbon, sleeves, mixed builds</p>
				</div>
				<div style="border-left: 3px solid #12786C; padding-left: 16px;">
					<p style="font-weight: 700; font-size: 17px; margin: 0; color: #0B5951;">Friday 11 December</p>
					<p style="font-size: 14.5px; line-height: 1.5; margin: 4px 0 0; color: #1B2420;">Bulk orders to one office address</p>
				</div>
				<div style="border-left: 3px solid #12786C; padding-left: 16px;">
					<p style="font-weight: 700; font-size: 17px; margin: 0; color: #0B5951;">Thursday 17 December</p>
					<p style="font-size: 14.5px; line-height: 1.5; margin: 4px 0 0; color: #1B2420;">Individual gifts posted to home addresses</p>
				</div>
			</div>
			<p style="font-size: 14px; line-height: 1.6; color: #5B6B68; margin: 20px 0 0;">Ordering early? We can hold dispatch to your chosen week, so hampers land when your clients are at their desks rather than in the last-minute rush. Just tell us the week in your enquiry.</p>
		</div>
	</section>

	<!-- How it works -->
	<section class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<h2 style="<?php echo esc_attr( $s_h2 ); ?> text-align: center; margin-bottom: 28px;">How corporate Christmas gifting works</h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
			<?php
			$tt_steps = array(
				array( 'Choose', 'Pick a tier, or mix them: hampers for clients, boxes for the team. Tell us any dietary needs and we build vegan, vegetarian and gluten-free versions at no extra cost.' ),
				array( 'Brand it', 'Send us your artwork. We produce a mock-up of every branded element, gift card, ribbon, sleeve or tag, for your sign-off before a single hamper is packed.' ),
				array( 'One invoice', 'Your company is invoiced directly for the whole order. We do the admin; you get the thank-you messages.' ),
				array( 'We deliver', 'One office address, or a recipient list in our template. Every parcel goes tracked, and we report delivery status across all recipients.' ),
			);
			foreach ( $tt_steps as $tt_n => $tt_st ) : ?>
				<div style="<?php echo esc_attr( $s_card ); ?> padding: 26px 28px;">
					<p style="font-weight: 800; font-size: 13px; letter-spacing: 0.06em; text-transform: uppercase; color: #12786C; margin: 0 0 8px;"><?php echo (int) $tt_n + 1; ?> &middot; <?php echo esc_html( $tt_st[0] ); ?></p>
					<p style="font-size: 15px; line-height: 1.6; margin: 0; color: #1B2420;"><?php echo esc_html( $tt_st[1] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Dietary + branding -->
	<section class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; align-items: start;">
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 30px 30px 32px;">
				<h2 style="font-weight: 700; font-size: 24px; margin: 0 0 12px; color: #1B2420;">Dietary needs, handled honestly</h2>
				<p style="font-size: 15.5px; line-height: 1.65; color: #1B2420; margin: 0;">Every hamper is alcohol-free as standard, and we happily build vegan, vegetarian and gluten-free versions at no extra cost; simply tag each recipient in the address sheet. For kosher requirements, we would love to explore what is possible once we know how many recipients need it. Beyond these, we are not able to offer bespoke allergy builds, so every hamper includes a full contents and allergen card to keep recipients informed.</p>
			</div>
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 30px 30px 32px;">
				<h2 style="font-weight: 700; font-size: 24px; margin: 0 0 12px; color: #1B2420;">Your name on the moment</h2>
				<p style="font-size: 15.5px; line-height: 1.65; color: #1B2420; margin: 0 0 14px;">Work with us to carry your company through the whole unboxing. Artwork comes from your marketing team, and every element is produced and approved before packing begins.</p>
				<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; line-height: 1.55; color: #1B2420;">
					<li><strong>Branded gift card.</strong> Your artwork and a personal message from your company, printed and placed in every hamper.</li>
					<li><strong>Branded ribbon and finish.</strong> Ribbon, sleeve or basket tag carrying your branding across the presentation.</li>
					<li><strong>Approval before production.</strong> Mock-ups of every branded element for your sign-off before we pack.</li>
				</ul>
				<p style="font-size: 13.5px; line-height: 1.6; color: #5B6B68; margin: 14px 0 0;">Branded orders close Friday 20 November because proofing and printing take real time, and we would rather get it right than rush it.</p>
			</div>
			<figure style="margin: 0;">
				<?php echo wp_get_attachment_image( $tt_card_img, 'large', false, array(
					'alt'   => 'A hand-held Treat Trunk gift card above an open Signature Hamper lined with yellow tissue, ready for a personalised Christmas message',
					'style' => 'width: 100%; height: 420px; object-fit: cover; border-radius: 24px;',
				) ); ?>
				<figcaption style="font-size: 13px; color: #5B6B68; margin: 10px 4px 0; line-height: 1.5;">A personalised gift card in every hamper. Your artwork and your message, proofed before we pack.</figcaption>
			</figure>
		</div>
	</section>

	<!-- Family business -->
	<section class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 860px; margin: 0 auto;">
		<h2 style="font-weight: 700; font-size: 28px; margin: 0 0 14px; color: #1B2420;">A small family business, packing hampers by hand</h2>
		<p style="font-size: 16px; line-height: 1.7; color: #1B2420; margin: 0;">Treat Trunk has been hand-packing healthier-for-you snack boxes for over seven years. My wife and I took the business over in 2024, and we still pack every corporate order ourselves, here in Britain, choosing from small, ethical UK makers we have got to know personally. Our whole idea is making snack time easier, adventurous and healthy, and a Christmas hamper is that idea at its most generous. If you would like to judge the quality before you commit, <a href="#quote" style="<?php echo esc_attr( $s_link ); ?>">ask us about a sample</a>.</p>
	</section>

	<?php
	$tt_quotes = array( '800', 'hst' );
	include TT_CORP_UI_DIR . 'templates/parts/trust.php';

	$tt_form = array(
		'id'      => 'xmas',
		'source'  => 'corporate-christmas-hampers',
		'heading' => 'Get your Christmas quote within 24 hours',
		'intro'   => 'Tell us roughly how many people, which tier or mix, and whether it is one address or many. We reply personally with a written quote, usually the same day.',
		'bullets' => array( 'hello@treattrunk.co.uk', 'Volume pricing and company invoicing', 'Branded cards, ribbon and sleeves', 'Samples available on request' ),
		'fields'  => array(
			array( 'name' => 'company', 'label' => 'Company', 'type' => 'text' ),
			array( 'name' => 'headcount', 'label' => 'Rough headcount', 'type' => 'text', 'placeholder' => 'e.g. 12 clients and 40 staff' ),
			array( 'name' => 'gifts', 'label' => 'Which gifts?', 'type' => 'select', 'options' => array( 'Signature Hamper', 'Full Treat Trunk', 'Letterbox Gift', 'A mix', 'Not sure yet' ) ),
			array( 'name' => 'addresses', 'label' => 'One address or many?', 'type' => 'select', 'options' => array( 'One office address', 'Individual home addresses', 'Both' ) ),
			array( 'name' => 'branding', 'label' => 'Branding?', 'type' => 'select', 'options' => array( 'Yes', 'No', 'Not sure yet' ) ),
			array( 'name' => 'week', 'label' => 'Ideal delivery week', 'type' => 'text', 'placeholder' => 'e.g. week of 7 December' ),
		),
		'message' => array( 'label' => 'Anything else', 'hint' => '(dietary needs, budget, questions)', 'placeholder' => 'e.g. 6 vegan, 2 gluten-free; budget around £40 a head for the team' ),
		'button'  => 'Get my Christmas quote',
		'success' => 'Thanks, we have your enquiry. We reply personally, usually the same day and always within 24 hours.',
	);
	include TT_CORP_UI_DIR . 'templates/parts/quote-form.php';

	$tt_faq_heading = 'Corporate Christmas hamper FAQs';
	include TT_CORP_UI_DIR . 'templates/parts/faqs.php';
	?>

	<!-- Cross-links to the other corporate pages (one intent per page) -->
	<section class="tt-corp-section" style="padding: 24px 48px 72px; max-width: 860px; margin: 0 auto;">
		<h2 style="font-weight: 700; font-size: 24px; margin: 0 0 14px; color: #1B2420;">Healthy corporate Christmas gifts, from a small UK business</h2>
		<p style="font-size: 15.5px; line-height: 1.7; color: #1B2420; margin: 0 0 12px;">Instead of another bottle of wine or a tin of biscuits, send your clients and employees a Christmas gift that feels personal, ships tracked, and works for almost every dietary requirement in the building. We have handled everything from five hampers for a founder&rsquo;s best clients to an 800-employee letterbox campaign.</p>
		<p style="font-size: 15.5px; line-height: 1.7; color: #1B2420; margin: 0;">Gifting outside December? See our <a href="<?php echo esc_url( home_url( '/corporate-orders/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">year-round corporate snack gifts</a>, including the New Mum and Menopause wellbeing boxes. Stocking the kitchen instead? Our <a href="<?php echo esc_url( home_url( '/office-snack-boxes/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">healthy office snacks</a> come weekly or monthly with no minimum order.</p>
	</section>

	<?php include TT_CORP_UI_DIR . 'templates/parts/schema.php'; ?>

</div>

<?php
get_footer();
