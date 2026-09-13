<?php
/**
 * Template Name: Corporate Orders (Custom Redesign)
 *
 * /corporate-orders/ (page 36634). Refocused 2026-09-13 as the corporate
 * GIFTING page: "corporate snack gifts", employee gift boxes, staff
 * wellbeing gifts, wellness gift box for employees, client gifts, new
 * starter boxes. The office-kitchen products (subscriptions, bulk one-off
 * boxes, Deluxe) moved to /office-snack-boxes/ and Christmas lives on
 * /corporate-christmas-hampers/, so this page no longer competes with
 * either - see docs/corporate-seo-pages-2026-09.md section 0.
 *
 * The WeWork welcome-box offer and its claim form are kept verbatim (the
 * tt_wework_claim endpoint in corporate-ui.php). The "Click Here To Claim
 * Corporate Discount" CTA of the original Elementor page is replaced by the
 * shared quote form (parts/quote-form.php -> tt_corp_enquiry).
 *
 * Same server-rendered pattern as the other corporate-ui templates: theme
 * header/footer, inline styles, shared .tt-corp-* classes.
 */

get_header();

$tt_newmum_img    = get_the_post_thumbnail_url( 8122, 'large' );
$tt_menopause_img = get_the_post_thumbnail_url( 50327, 'large' );
$tt_hamper_img    = wp_get_attachment_image_url( 54964, 'large' );

$s_card  = 'background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px;';
$s_h2    = 'font-weight: 700; font-size: 34px; line-height: 1.15; margin: 0 0 12px; color: #1B2420;';
$s_h3    = 'font-weight: 700; font-size: 20px; margin: 0; color: #1B2420;';
$s_p     = 'font-size: 16px; line-height: 1.65; color: #1B2420; margin: 0;';
$s_btn   = 'background: #12786C; color: #FAFAF8; font-weight: 700; font-size: 15px; padding: 12px 22px; border-radius: 999px; text-decoration: none; display: inline-block; text-align: center;';
$s_btn2  = 'background: #FFFFFF; color: #12786C; border: 2px solid #12786C; font-weight: 700; font-size: 14px; padding: 10px 18px; border-radius: 999px; text-decoration: none; display: inline-block; text-align: center;';
$s_link  = 'color: #12786C; font-weight: 700; text-decoration: underline; text-underline-offset: 4px;';
$s_pill  = 'background: #DCEFEC; color: #0B5951; font-size: 12.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;';
$s_tick  = 'font-size: 14px; color: #5B6B68; font-weight: 600;';
$s_price = 'font-weight: 700; font-size: 16px; color: #0B5951; margin: 0;';
$s_td    = 'padding: 14px 16px; vertical-align: top;';
?>

<div class="tt-corp-page" style="font-family: 'DM Sans', -apple-system, sans-serif; color: #1B2420; background: #FAFAF8;">

	<!-- Announcement bar (WeWork offer - confirmed active 2026-07-04) -->
	<div style="background: #12786C; color: #FAFAF8; text-align: center; padding: 10px 16px; font-size: 14px; font-weight: 600;">
		In a WeWork office? Claim a FREE welcome snack box for your team &nbsp;&middot;&nbsp;
		<a href="#wework" style="color: #FAFAF8; text-decoration: underline; text-underline-offset: 3px;">Claim yours</a>
	</div>

	<?php if ( time() < strtotime( '2026-12-18 00:00:00 Europe/London' ) ) : ?>
	<!-- Seasonal cross-link, auto-hides after the last Christmas deadline -->
	<div style="background: #0B5951; color: #FAFAF8; text-align: center; padding: 9px 16px; font-size: 13.5px; font-weight: 600;">
		Christmas 2026: branded orders close Fri 20 Nov &nbsp;&middot;&nbsp;
		<a href="<?php echo esc_url( home_url( '/corporate-christmas-hampers/' ) ); ?>" style="color: #FAFAF8; text-decoration: underline; text-underline-offset: 3px;">See our corporate Christmas hampers</a>
	</div>
	<?php endif; ?>

	<!-- Hero -->
	<section class="tt-corp-hero" style="display: grid; grid-template-columns: 1.05fr 1fr; gap: 48px; align-items: center; padding: 64px 48px 48px; max-width: 1240px; margin: 0 auto;">
		<div style="display: flex; flex-direction: column; gap: 22px;">
			<div style="display: flex; gap: 10px; flex-wrap: wrap;">
				<span style="<?php echo esc_attr( $s_pill ); ?>">Employees &middot; clients &middot; new starters</span>
				<span style="<?php echo esc_attr( $s_pill ); ?> color: #12786C;">Wellbeing range</span>
			</div>
			<h1 style="font-weight: 700; font-size: 42px; line-height: 1.1; margin: 0; color: #1B2420;">Corporate snack gifts and employee wellbeing boxes, packed by hand by a family that cares about the person opening them</h1>
			<p style="font-size: 18px; line-height: 1.6; margin: 0; max-width: 54ch; color: #1B2420;">Say thank you, welcome, get well or well done with a box of healthier-for-you snacks from small, ethical UK makers. Employee gift boxes from &pound;13.99, wellbeing boxes for new parents and for menopause, client gifts with your branding on the card. No minimum order. Pay by invoice. Posted to one office or through every front door.</p>
			<div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
				<a href="#quote" style="<?php echo esc_attr( $s_btn ); ?> font-size: 17px; padding: 15px 30px;">Tell us about your gift</a>
				<a href="#wellbeing" style="<?php echo esc_attr( $s_link ); ?> font-size: 16px;">See the wellbeing range &rarr;</a>
			</div>
			<div style="display: flex; gap: 10px 26px; margin-top: 6px; flex-wrap: wrap;">
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; No minimum order</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; A wellbeing gift range nobody else offers</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Branded cards, stickers &amp; ribbon</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Pay by invoice</span>
				<span style="<?php echo esc_attr( $s_tick ); ?>">&#10003; Letterbox delivery to home addresses</span>
			</div>
		</div>
		<div style="position: relative;">
			<img src="https://treattrunk.co.uk/wp-content/uploads/2022/02/IMG_0413-1-768x1024.jpeg" data-skip-lazy="1" fetchpriority="high" alt="A corporate snack gift box of healthy snacks being hand-packed at Treat Trunk" style="width: 100%; height: 460px; object-fit: cover; border-radius: 24px; box-shadow: 0 24px 48px -20px rgba(31, 61, 44, 0.35);">
		</div>
	</section>

	<!-- From-pricing strip -->
	<div style="padding: 0 48px 8px;">
		<div style="max-width: 1240px; margin: 0 auto; background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; padding: 18px 24px; display: flex; justify-content: center; gap: 12px 36px; flex-wrap: wrap; font-size: 14.5px; color: #0B5951; font-weight: 700;">
			<span>Letterbox Gift from &pound;13.00</span>
			<span>Full Treat Trunk gift from &pound;35.00</span>
			<span>New Mum &amp; Menopause boxes &pound;39.99</span>
			<span>Signature Hamper &pound;149</span>
			<a href="#pricing" style="<?php echo esc_attr( $s_link ); ?> font-size: 14.5px;">Full pricing &rarr;</a>
		</div>
	</div>

	<!-- Employee gift boxes -->
	<section id="employee-gifts" class="tt-corp-section" style="padding: 40px 48px; max-width: 1240px; margin: 0 auto;">
		<div style="max-width: 760px; margin: 0 auto 32px; text-align: center;">
			<h2 style="<?php echo esc_attr( $s_h2 ); ?>">Employee gift boxes for the moments that matter</h2>
			<p style="font-size: 17px; line-height: 1.6; color: #1B2420; margin: 0;">A snack box is a small thing, and that is rather the point. It arrives on a Tuesday, it is opened at the desk or the kitchen table, and for a minute somebody feels noticed. Here is where our clients tend to use them.</p>
		</div>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
			<?php
			$tt_moments = array(
				array( 'Thank-you and recognition gifts', 'The Full Treat Trunk, twenty to twenty-five full-size healthier-for-you snacks, with a printed card carrying your message. For the person who carried the project, the team that hit the number, or the whole company after a hard quarter.', 'From &pound;35.00 each at 50+' ),
				array( 'New starter boxes', 'First-week nerves are real. A Letterbox Gift or Full Treat Trunk posted to a new starter&rsquo;s home before day one, with a welcome card from the team, says more than a lanyard ever will. Works just as well for remote hires, who so often get nothing physical at all. Give us a rolling list and we send one out every time someone joins.', 'From &pound;13.99' ),
				array( 'Work anniversaries, birthdays and milestones', 'Set it up once: tell us the dates, we send the box. Branded card, their name on it, no admin for you.', 'From &pound;13.99' ),
				array( 'Remote team boosts', 'The Letterbox Gift fits through any door, so a hybrid team of forty in twelve towns gets the same gift on the same day.', 'From &pound;13.00 each at 50+' ),
			);
			foreach ( $tt_moments as $tt_m ) : ?>
				<div style="<?php echo esc_attr( $s_card ); ?> padding: 26px 26px 28px; display: flex; flex-direction: column; gap: 10px;">
					<h3 style="<?php echo esc_attr( $s_h3 ); ?> font-size: 19px;"><?php echo $tt_m[0]; ?></h3>
					<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1;"><?php echo $tt_m[1]; ?></p>
					<p style="<?php echo esc_attr( $s_price ); ?> font-size: 14.5px;"><?php echo $tt_m[2]; ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- Wellbeing range -->
	<section id="wellbeing" class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="max-width: 760px; margin: 0 auto 32px; text-align: center;">
			<h2 style="<?php echo esc_attr( $s_h2 ); ?>">Staff wellbeing gifts with real thought in them</h2>
			<p style="font-size: 17px; line-height: 1.6; color: #1B2420; margin: 0;">This is the part we are proudest of, and the part no other snack company does. Most &ldquo;wellness gift boxes for employees&rdquo; are a scented candle and a tea bag. Ours were built for real moments in people&rsquo;s lives, and they come from the same healthier-for-you thinking as everything else we pack.</p>
		</div>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
			<div class="tt-corp-card-wrap" style="<?php echo esc_attr( $s_card ); ?> overflow: hidden; display: flex; flex-direction: column;">
				<?php if ( $tt_newmum_img ) : ?><img src="<?php echo esc_url( $tt_newmum_img ); ?>" alt="The Treat Trunk New Mum Box: a returning-from-parental-leave wellbeing gift of healthy snacks" style="width: 100%; height: 210px; object-fit: cover; display: block;"><?php endif; ?>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="<?php echo esc_attr( $s_h3 ); ?>">The New Mum Box: the returning-from-leave gift</h3>
					<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1;">Twenty-plus healthier-for-you snacks chosen for new parents: one-handed, energy-steady, genuinely nice. Send it when the baby arrives, or as the &ldquo;welcome back, we&rsquo;re glad you&rsquo;re here&rdquo; gift on someone&rsquo;s first week back from parental leave. It is the kind of thing people mention years later, in a good way.</p>
					<p style="<?php echo esc_attr( $s_price ); ?>">&pound;39.99 &middot; branded card available</p>
					<a href="<?php echo esc_url( home_url( '/product/new-mum/' ) ); ?>" style="<?php echo esc_attr( $s_btn ); ?>">See the New Mum Box</a>
				</div>
			</div>
			<div class="tt-corp-card-wrap" style="<?php echo esc_attr( $s_card ); ?> overflow: hidden; display: flex; flex-direction: column;">
				<?php if ( $tt_menopause_img ) : ?><img src="<?php echo esc_url( $tt_menopause_img ); ?>" alt="The Treat Trunk Menopause Box: a staff wellbeing gift of plant-based snacks for energy, sleep and steadiness" style="width: 100%; height: 210px; object-fit: cover; display: block;"><?php endif; ?>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="<?php echo esc_attr( $s_h3 ); ?>">The Menopause Box</h3>
					<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1;">Snack smarter, feel good: a box built around snacks that support energy, sleep and steadiness, from makers who take the science seriously. We recommend offering it as a choice rather than sending it unasked: many of our clients list it in a wellbeing menu, alongside the Full Treat Trunk, and let people pick. It quietly tells your team that menopause is something the company is comfortable talking about.</p>
					<p style="<?php echo esc_attr( $s_price ); ?>">&pound;39.99</p>
					<a href="<?php echo esc_url( home_url( '/product/menopause-snacks-healthy-snack-box/' ) ); ?>" style="<?php echo esc_attr( $s_btn ); ?>">See the Menopause Box</a>
				</div>
			</div>
			<div class="tt-corp-card-wrap" style="<?php echo esc_attr( $s_card ); ?> overflow: hidden; display: flex; flex-direction: column;">
				<img src="https://treattrunk.co.uk/wp-content/uploads/2020/08/Treat-Trunk-August-2020-1200.jpg" alt="The Full Treat Trunk: an everyday wellness gift box for employees with 20+ healthy snacks" style="width: 100%; height: 210px; object-fit: cover; display: block;">
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="<?php echo esc_attr( $s_h3 ); ?>">The everyday wellness gift box for employees</h3>
					<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1;">The Full Treat Trunk is the all-rounder for wellbeing budgets: real ingredients, sugar-sensible, mostly vegan, full-size packs. Send it for mental health awareness week, at the end of a heavy month, or as the box everyone gets when a wellbeing programme launches.</p>
					<p style="<?php echo esc_attr( $s_price ); ?>">&pound;44.99 &middot; &pound;37.50 at 20+ &middot; &pound;35.00 at 50+</p>
					<a href="<?php echo esc_url( home_url( '/product/one-off-treat-trunk/' ) ); ?>" style="<?php echo esc_attr( $s_btn ); ?>">See the Full Treat Trunk</a>
				</div>
			</div>
		</div>
		<div style="<?php echo esc_attr( $s_card ); ?> padding: 22px 26px; margin-top: 20px; display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap;">
			<span style="<?php echo esc_attr( $s_pill ); ?>">A note on the budget</span>
			<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 0; flex: 1; min-width: 260px;">Most of our wellbeing gifts are bought from an HR or wellbeing budget rather than the kitchen budget, and they are invoiced as such. If you need a line description for finance, tell us and we will word the invoice to match.</p>
		</div>
	</section>

	<!-- Client gifts -->
	<section id="client-gifts" class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: center;">
			<?php if ( $tt_hamper_img ) : ?>
				<img src="<?php echo esc_url( $tt_hamper_img ); ?>" alt="The Treat Trunk Signature Hamper, a black wicker luxury client gift with leather straps" style="width: 100%; height: 400px; object-fit: cover; border-radius: 24px;">
			<?php endif; ?>
			<div style="display: flex; flex-direction: column; gap: 14px;">
				<h2 style="<?php echo esc_attr( $s_h2 ); ?> font-size: 30px;">Client gifts that get talked about</h2>
				<p style="<?php echo esc_attr( $s_p ); ?>">A client gift has one job: to be opened, enjoyed and remembered. Bottles get regifted and biscuits get left in reception. A box of small-maker snacks with your card on top gets photographed and passed round the office.</p>
				<p style="<?php echo esc_attr( $s_p ); ?>">For a handful of key clients, the Signature Hamper is the luxury tier: black wicker, twenty gift-grade products, alcohol-free as standard, &pound;149. See our <a href="<?php echo esc_url( home_url( '/corporate-christmas-hampers/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">corporate Christmas hampers</a> page for the full spec; it is available all year round on request.</p>
				<a href="#quote" style="<?php echo esc_attr( $s_btn ); ?> align-self: flex-start;">Ask about client gifts</a>
			</div>
		</div>
	</section>

	<!-- Pricing -->
	<section id="pricing" class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<h2 style="<?php echo esc_attr( $s_h2 ); ?> text-align: center; margin-bottom: 24px;">Corporate snack gift pricing</h2>
		<div style="overflow-x: auto;">
			<table style="width: 100%; border-collapse: collapse; background: #FFFFFF; border-radius: 20px; overflow: hidden; border: 1px solid #DCEBE9; font-size: 14.5px;">
				<thead>
					<tr style="background: #12786C; color: #FAFAF8;">
						<th style="text-align: left; padding: 14px 16px;">Gift</th>
						<th style="text-align: left; padding: 14px 16px;">What arrives</th>
						<th style="text-align: left; padding: 14px 16px;">Single</th>
						<th style="text-align: left; padding: 14px 16px;">20+</th>
						<th style="text-align: left; padding: 14px 16px;">50+</th>
						<th style="text-align: left; padding: 14px 16px;">Order</th>
					</tr>
				</thead>
				<tbody style="color: #1B2420;">
					<?php
					$tt_rows = array(
						array( 'Letterbox Gift', '7 to 8 healthy snacks, posts through any door', '&pound;13.99', '&pound;13.75', '&pound;13.00', home_url( '/product/letterbox/' ), 'Order' ),
						array( 'Mini gift box', '10 to 15 snacks', '&pound;28.99', 'quoted', 'quoted', home_url( '/product/one-off-treat-trunk/' ), 'Order' ),
						array( 'Full Treat Trunk gift', '20 to 25 full-size snacks', '&pound;44.99', '&pound;37.50', '&pound;35.00', home_url( '/product/one-off-treat-trunk/' ), 'Order' ),
						array( 'New Mum Box', '20+ snacks for new parents', '&pound;39.99', 'quoted', 'quoted', home_url( '/product/new-mum/' ), 'Order' ),
						array( 'Menopause Box', '20+ snacks for energy, sleep and steadiness', '&pound;39.99', 'quoted', 'quoted', home_url( '/product/menopause-snacks-healthy-snack-box/' ), 'Order' ),
						array( 'Signature Hamper', 'Black wicker, 20 gift-grade products, alcohol-free', '&pound;149 <span style="color:#5B6B68;">(&pound;125 returning customers)</span>', '&pound;149', '&pound;149', '#quote', 'Quote' ),
					);
					foreach ( $tt_rows as $tt_k => $tt_r ) : ?>
						<tr style="border-top: 1px solid #DCEBE9;<?php echo $tt_k % 2 ? ' background: #FAFAF8;' : ''; ?>">
							<td style="<?php echo esc_attr( $s_td ); ?> font-weight: 700;"><?php echo $tt_r[0]; ?></td>
							<td style="<?php echo esc_attr( $s_td ); ?>"><?php echo $tt_r[1]; ?></td>
							<td style="<?php echo esc_attr( $s_td ); ?>"><?php echo $tt_r[2]; ?></td>
							<td style="<?php echo esc_attr( $s_td ); ?>"><?php echo $tt_r[3]; ?></td>
							<td style="<?php echo esc_attr( $s_td ); ?>"><?php echo $tt_r[4]; ?></td>
							<td style="<?php echo esc_attr( $s_td ); ?>"><a href="<?php echo esc_url( $tt_r[5] ); ?>" style="<?php echo esc_attr( $s_btn2 ); ?> padding: 7px 14px; font-size: 13.5px;"><?php echo esc_html( $tt_r[6] ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<p style="text-align: center; font-size: 13.5px; color: #5B6B68; margin: 14px 0 0;">Dietary versions at no extra cost on every gift. Branding quoted separately. Volume pricing on the Letterbox Gift and Full Treat Trunk applies automatically in the basket. No VAT to add: we are not VAT registered. Looking for the kitchen, not a gift? See our <a href="<?php echo esc_url( home_url( '/office-snack-boxes/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">office snack box subscription</a>.</p>
	</section>

	<!-- Branding + how it works + dietary -->
	<section class="tt-corp-section" style="padding: 24px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; align-items: start;">
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 30px 30px 32px;">
				<h2 style="font-weight: 700; font-size: 24px; margin: 0 0 12px; color: #1B2420;">Your branding, on the bits people keep</h2>
				<ul style="margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: 15px; line-height: 1.55; color: #1B2420;">
					<li><strong>Gift cards.</strong> Your logo and a personal message, printed and placed in every box.</li>
					<li><strong>Stickers.</strong> Branded stickers on the box, so the unboxing photo has your name in it.</li>
					<li><strong>Ribbon.</strong> For the gift-wrapped tiers and the hamper.</li>
					<li><strong>Proofed first.</strong> We send mock-ups for sign-off before anything is printed or packed. Allow around two weeks for branded orders.</li>
				</ul>
			</div>
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 30px 30px 32px;">
				<h2 style="font-weight: 700; font-size: 24px; margin: 0 0 12px; color: #1B2420;">How corporate gifting works</h2>
				<ol style="margin: 0; padding: 0 0 0 20px; display: flex; flex-direction: column; gap: 10px; font-size: 15px; line-height: 1.55; color: #1B2420;">
					<li><strong>Choose.</strong> Pick a gift, or a mix. Tell us the occasion and who it is for.</li>
					<li><strong>Personalise.</strong> Send your message and artwork; we proof the card, stickers or ribbon.</li>
					<li><strong>One invoice.</strong> Your company is invoiced directly, worded for the right budget. Payment on invoice, then we dispatch.</li>
					<li><strong>We deliver.</strong> One office address, or a spreadsheet of homes. Every parcel goes tracked and we report delivery for every recipient.</li>
				</ol>
			</div>
			<div style="<?php echo esc_attr( $s_card ); ?> padding: 30px 30px 32px;">
				<h2 style="font-weight: 700; font-size: 24px; margin: 0 0 12px; color: #1B2420;">Dietary needs, handled honestly</h2>
				<p style="font-size: 15px; line-height: 1.65; color: #1B2420; margin: 0;">Every gift is vegetarian throughout and mostly vegan. We build vegan, gluten-free and nut-aware versions at no extra cost; tag each recipient in the address sheet. Kosher is handled case by case. We cannot offer bespoke allergy builds beyond those, so every box carries a contents and allergen card.</p>
			</div>
		</div>
	</section>

	<?php
	$tt_quotes = array( '800', 'home' );
	include TT_CORP_UI_DIR . 'templates/parts/trust.php';
	?>

	<!-- WeWork offer -->
	<section id="wework" class="tt-corp-section" style="padding: 48px; max-width: 1240px; margin: 0 auto;">
		<div style="background: #12786C; border-radius: 28px; padding: 48px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: start;">
			<div style="display: flex; flex-direction: column; gap: 18px;">
				<span style="align-self: flex-start; background: #12786C; color: #FAFAF8; font-size: 12px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;">Free for WeWork members</span>
				<h2 style="font-weight: 700; font-size: 32px; line-height: 1.15; margin: 0; color: #FAFAF8;">Based in a WeWork? Your first box is on us.</h2>
				<p style="font-size: 17px; line-height: 1.6; color: #B9DBD6; margin: 0; max-width: 56ch;">Any business working from a WeWork office space gets a free welcome snack box - a full-size Treat Trunk with 20+ healthy snacks for the team to try. No card details, no commitment. Pick your WeWork building and we&rsquo;ll take it from there.</p>
			</div>

			<?php
			// Real WeWork UK buildings with full addresses, provided by the user
			// 2026-07-05 (authoritative source, supersedes the earlier partial
			// list sourced from web search). "Not listed" keeps the offer open
			// to anyone at a WeWork not in this list.
			$wework_locations = array(
				'London'    => array(
					'Medius House - 2 Sheraton St, London W1F 8BH',
					'16 Great Chapel St - 16 Great Chapel St, London W1F 8FL',
					'123 Buckingham Palace Rd - 123 Buckingham Palace Rd, London SW1W 9SH',
					'Aldwych House - 71-91 Aldwych, London WC2B 4HN',
					'Aviation House - 125 Kingsway, London WC2B 6NH',
					'1 Waterhouse Square - 1 Waterhouse Square, London EC1N 2ST',
					'3 Waterhouse Square - 3 Waterhouse Square, London EC1N 2SW',
					'26 Hatton Garden - 26 Hatton Garden, London EC1N 8BN',
					'33 Queen St - 33 Queen St, London EC4R 1AP',
					'North West House - 119 Marylebone Rd, London NW1 5PU',
					'5 Merchant Square - 5 Merchant Square, London W2 1AY',
					'2 Eastbourne Terrace - 2 Eastbourne Terrace, London W2 6LG',
					'184 Shepherds Bush Rd - 184 Shepherds Bush Rd, London W6 7NL',
					"1 St Katharine's Way - 1 St Katharine's Way, London E1W 1UN",
					'10 York Rd - 10 York Rd, London SE1 7ND',
					'Kings Place - 90 York Way, London N1 9AG',
					'1 Mark Sq - 1 Mark Square, London EC2A 4EG',
					'145 City Rd - 145 City Rd, London EC1V 1AZ',
					'8 Devonshire Square - 8 Devonshire Square, London EC2M 4YJ',
					'10 Devonshire Square - 10 Devonshire Square, London EC2M 4YP',
					'30 Churchill Place - 30 Churchill Place, London E14 5RE',
					"17 St Helen's Place - 17 St Helen's Pl, London EC3A 6DG",
					'The Monument - 51 Eastcheap, London EC3M 1DT',
					'77 Leadenhall Street - 77 Leadenhall St, London EC3A 3DE',
					'120 Moorgate - 120 Moorgate, London EC2M 6UR',
					'Moor Place - 1 Fore Street Ave, London EC2Y 9DT',
					'2 Minster Court - 2 Minster Court, London EC3R 7BB',
				),
				'Manchester' => array(
					"One St Peter's Square - One St Peter's Square, Manchester M2 3DE",
					'Dalton Place - 29 John Dalton St, Manchester M2 6FW',
				),
				'Edinburgh' => array(
					'80 George Street - 80 George St, Edinburgh EH2 3BU',
				),
				'Cambridge' => array(
					'50-60 Station Road - 50-60 Station Rd, Cambridge CB1 2JH',
				),
			);
			?>
			<form id="tt-wework-form" style="background: #FAFAF8; border-radius: 22px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
				<input type="text" id="tt-ww-name" placeholder="Your name" required style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box;">
				<input type="text" id="tt-ww-company" placeholder="Company name" required style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box;">
				<select id="tt-ww-location" required style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box;">
					<option value="">Select your WeWork building&hellip;</option>
					<?php foreach ( $wework_locations as $city => $buildings ) : ?>
						<optgroup label="<?php echo esc_attr( $city ); ?>">
							<?php foreach ( $buildings as $loc ) : ?>
								<option value="<?php echo esc_attr( $loc ); ?>"><?php echo esc_html( $loc ); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; ?>
					<option value="Not listed">My WeWork isn&rsquo;t listed</option>
				</select>
				<input type="text" id="tt-ww-address" placeholder="Suite/floor or delivery notes (optional)" style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box;">
				<button type="submit" id="tt-ww-submit" style="background: #12786C; color: #FAFAF8; border: none; font-weight: 700; font-size: 16px; padding: 14px 0; border-radius: 999px; cursor: pointer;">Claim your free welcome box</button>
				<p id="tt-ww-status" role="status" style="font-size: 13.5px; margin: 0; display: none;"></p>
				<span style="font-size: 12.5px; color: #5B6B68;">We'll email you directly to arrange delivery - no card details, no commitment.</span>
			</form>
			<script>
			document.getElementById('tt-wework-form').addEventListener('submit', function (e) {
				e.preventDefault();
				var name = document.getElementById('tt-ww-name').value;
				var company = document.getElementById('tt-ww-company').value;
				var location = document.getElementById('tt-ww-location').value;
				var address = document.getElementById('tt-ww-address').value;
				var submitBtn = document.getElementById('tt-ww-submit');
				var status = document.getElementById('tt-ww-status');
				var subject = 'WeWork free welcome box - ' + company;
				var body = 'Name: ' + name + '\nCompany: ' + company + '\nWeWork building: ' + location
					+ (address ? '\nNotes: ' + address : '') + '\n\nPlease send our free WeWork welcome box!';
				var mailtoFallback = 'mailto:hello@treattrunk.co.uk?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);

				function showStatus(message, isError) {
					status.textContent = message;
					status.style.display = 'block';
					status.style.color = isError ? '#B8532F' : '#12786C';
				}

				submitBtn.disabled = true;
				var formData = new FormData();
				formData.append('action', 'tt_wework_claim');
				formData.append('nonce', '<?php echo esc_js( wp_create_nonce( 'tt_wework_claim' ) ); ?>');
				formData.append('name', name);
				formData.append('company', company);
				formData.append('location', location);
				formData.append('notes', address);

				fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', { method: 'POST', body: formData })
					.then(function (r) { return r.json(); })
					.then(function (data) {
						submitBtn.disabled = false;
						if (data.success) {
							showStatus(data.data.message, false);
							document.getElementById('tt-wework-form').reset();
						} else {
							showStatus((data.data && data.data.message) || 'Something went wrong - opening your email client instead.', true);
							window.location.href = mailtoFallback;
						}
					})
					.catch(function () {
						submitBtn.disabled = false;
						showStatus('Could not reach our server - opening your email client instead.', true);
						window.location.href = mailtoFallback;
					});
			});
			</script>
		</div>
	</section>

	<?php
	$tt_form = array(
		'id'      => 'gift',
		'source'  => 'corporate-orders',
		'heading' => 'Tell us about your gift',
		'intro'   => 'The occasion, roughly how many people, and whether it is one address or many. We reply personally with a quote, usually the same day.',
		'bullets' => array( 'hello@treattrunk.co.uk', 'No minimum order, invoiced to the right budget', 'Branded cards, stickers and ribbon', 'New Mum and Menopause wellbeing boxes' ),
		'fields'  => array(
			array( 'name' => 'company', 'label' => 'Company', 'type' => 'text' ),
			array( 'name' => 'headcount', 'label' => 'Rough headcount', 'type' => 'text', 'placeholder' => 'e.g. 1 colleague, or 60 staff' ),
			array( 'name' => 'gifts', 'label' => 'Which gift?', 'type' => 'select', 'options' => array( 'Letterbox Gift', 'Full Treat Trunk', 'New Mum Box', 'Menopause Box', 'Signature Hamper', 'A mix', 'Not sure yet' ) ),
			array( 'name' => 'occasion', 'label' => 'Occasion and ideal delivery date', 'type' => 'text', 'placeholder' => 'e.g. new starters on 5 October' ),
			array( 'name' => 'addresses', 'label' => 'One address or many?', 'type' => 'select', 'options' => array( 'One office address', 'Individual home addresses', 'Both' ) ),
			array( 'name' => 'branding', 'label' => 'Branding?', 'type' => 'select', 'options' => array( 'Yes', 'No', 'Not sure yet' ) ),
		),
		'message' => array( 'label' => 'Anything else', 'hint' => '(dietary needs, budget, message for the card)', 'placeholder' => 'e.g. budget around £30 a head, 3 vegan' ),
		'button'  => 'Get my gifting quote',
		'success' => 'Thanks, we have your enquiry. We reply personally, usually the same day and always within 24 hours.',
	);
	include TT_CORP_UI_DIR . 'templates/parts/quote-form.php';

	// FAQ accordion. The FAQPage JSON-LD for page 36634 is emitted by
	// site-core.php from an identical array - update both together.
	$tt_faqs = array(
		array( 'Is there a minimum order for corporate snack gifts?', 'No. One New Mum Box for one colleague is a real order. Volume pricing on the Letterbox Gift and Full Treat Trunk starts at 20.' ),
		array( 'Can we send employee gift boxes to home addresses?', 'Yes. Send us a spreadsheet of addresses and we handle every label, dispatch tracked and report delivery status for every recipient. The Letterbox Gift fits through any standard letterbox.' ),
		array( 'Can we pay by invoice, and can it come out of our wellbeing budget?', 'Yes. Your company is invoiced directly and we will word the invoice line to match the budget you are using, wellbeing, HR, marketing or client entertainment. Our standard terms are payment on invoice, then dispatch.' ),
		array( 'Can you add our branding?', 'Yes: gift cards with your artwork and message, branded stickers on the box, and ribbon on the gift-wrapped tiers. We proof everything first. Allow around two weeks.' ),
		array( 'What is in the New Mum and Menopause boxes, and are they suitable as employee gifts?', 'Both are 20+ healthier-for-you snacks chosen for the moment: one-handed and energy-steady for new parents; energy, sleep and steadiness for menopause. Both are widely used as returning-from-leave and wellbeing-programme gifts. We suggest offering the Menopause Box as a choice within a wellbeing menu rather than sending it unrequested.' ),
		array( 'Can you set up recurring gifts for new starters or anniversaries?', 'Yes. Give us a rolling list or the dates and we send a box each time, with a card, and invoice monthly.' ),
		array( 'What dietary options are there?', 'Vegetarian throughout and mostly vegan. Vegan, gluten-free and nut-aware versions at no extra cost, per recipient. Kosher case by case. Every box carries a contents and allergen card.' ),
	);
	$tt_faq_heading = 'Corporate gifting FAQs';
	include TT_CORP_UI_DIR . 'templates/parts/faqs.php';
	?>

	<!-- Cross-links (one intent per page) -->
	<section class="tt-corp-section" style="padding: 24px 48px 72px; max-width: 860px; margin: 0 auto;">
		<p style="font-size: 15.5px; line-height: 1.7; color: #1B2420; margin: 0;">Stocking the office kitchen rather than sending a gift? See our <a href="<?php echo esc_url( home_url( '/office-snack-boxes/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">office snack box subscription</a>, weekly or monthly with no minimum order. For December, our <a href="<?php echo esc_url( home_url( '/corporate-christmas-hampers/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">corporate Christmas hampers</a> are alcohol-free as standard. For ideas, read our guides to <a href="<?php echo esc_url( home_url( '/the-best-corporate-wellbeing-gifts-to-support-your-employees-healthwellness/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">corporate wellbeing gifts</a> and <a href="<?php echo esc_url( home_url( '/9-corporate-letterbox-gifts-to-send-to-your-staff-and-clients/' ) ); ?>" style="<?php echo esc_attr( $s_link ); ?>">corporate letterbox gifts</a>.</p>
	</section>

</div>

<?php
get_footer();
