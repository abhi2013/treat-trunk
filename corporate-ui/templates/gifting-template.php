<?php
/**
 * Template Name: Corporate Christmas Gifting (Custom)
 *
 * Q4 corporate gifting campaign page (value-build playbook WS3). Same
 * server-rendered pattern as corporate-orders-template.php: real theme
 * header/footer, inline styles + the shared .tt-corp-* classes so
 * assets/corporate-orders.css responsive rules apply to both pages.
 *
 * The enquiry form posts to the existing tt_corp_enquiry endpoint in
 * site-core.php (honeypot + rate limit + email to hello@) - no new backend.
 *
 * NOTE: the three order-deadline dates below are provisional and need
 * confirming against Royal Mail's final 2026 Christmas posting dates.
 */

get_header();
?>

<div class="tt-corp-page" style="font-family: 'DM Sans', -apple-system, sans-serif; color: #1B2420; background: #FAFAF8;">

	<!-- Urgency bar -->
	<div style="background: #12786C; color: #FAFAF8; text-align: center; padding: 10px 16px; font-size: 14px; font-weight: 600;">
		Christmas corporate orders are open &nbsp;&middot;&nbsp; branded &amp; bespoke orders close Fri 20 November
	</div>

	<!-- Hero -->
	<section class="tt-corp-hero" style="display: grid; grid-template-columns: 1.05fr 1fr; gap: 48px; align-items: center; padding: 72px 48px 80px; max-width: 1240px; margin: 0 auto;">
		<div style="display: flex; flex-direction: column; gap: 22px;">
			<div style="display: flex; gap: 10px; flex-wrap: wrap;">
				<span style="background: #DCEFEC; color: #0B5951; font-size: 12.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;">Christmas 2026</span>
				<span style="background: #DCEFEC; color: #12786C; font-size: 12.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;">Clients &middot; employees &middot; remote teams</span>
			</div>
			<h1 style="font-weight: 700; font-size: 44px; line-height: 1.1; margin: 0; color: #1B2420;">Corporate Christmas gifts people actually eat</h1>
			<p style="font-size: 18px; line-height: 1.6; margin: 0; max-width: 52ch; color: #1B2420;">Healthy snack hampers and letterbox gift boxes from small UK brands - hand-packed, posted to your office or direct to every employee&rsquo;s door. Branded notes, volume pricing, one invoice.</p>
			<div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
				<a href="#gifts" style="background: #12786C; color: #FAFAF8; font-weight: 700; font-size: 17px; padding: 15px 30px; border-radius: 999px; text-decoration: none;">See the gift options</a>
				<a href="#quote" style="color: #12786C; font-weight: 700; font-size: 16px; text-decoration: underline; text-underline-offset: 4px;">Get a quote in 24 hours &rarr;</a>
			</div>
			<div style="display: flex; gap: 26px; margin-top: 6px; font-size: 14px; color: #5B6B68; font-weight: 600; flex-wrap: wrap;">
				<span>&#10003; Posts through any letterbox</span>
				<span>&#10003; Vegetarian, mostly vegan</span>
				<span>&#10003; Pay by invoice</span>
				<span>&#10003; 800+ box orders handled</span>
			</div>
		</div>
		<div style="position: relative;">
			<img src="https://treattrunk.co.uk/wp-content/uploads/2022/02/IMG_0413-1-768x1024.jpeg" data-skip-lazy="1" alt="Corporate Christmas snack gift boxes being hand-packed" style="width: 100%; height: 460px; object-fit: cover; border-radius: 24px; box-shadow: 0 24px 48px -20px rgba(31, 61, 44, 0.35);">
		</div>
	</section>

	<!-- Gift tiers -->
	<section id="gifts" class="tt-corp-section" style="padding: 40px 48px; max-width: 1240px; margin: 0 auto;">
		<div style="text-align: center; margin-bottom: 44px;">
			<h2 style="font-weight: 700; font-size: 34px; margin: 0 0 12px; color: #1B2420;">Three ways to say thank you</h2>
			<p style="font-size: 17px; color: #1B2420; margin: 0;">Order online in minutes - volume pricing applies automatically at checkout. Branding or mixed orders? <a href="#quote" style="color: #12786C; font-weight: 700;">Talk to us</a>.</p>
		</div>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">

			<!-- Letterbox gift -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2021/05/Treat-Trunk-Mini-Healthy-Snack-Box-March-1200.jpg" alt="Letterbox Christmas gift snack box" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #12786C; color: #FAFAF8; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">For whole teams</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1B2420;">The Letterbox Gift</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #1B2420; margin: 0; flex: 1;">Fits through any letterbox - no one waiting in for a courier. The classic pick for remote and hybrid teams: one order, every employee&rsquo;s doorstep.</p>
					<p style="font-size: 15px; margin: 0; color: #0B5951; font-weight: 700;">&pound;13.99 each &middot; &pound;13.75 at 20+ &middot; &pound;13.00 at 50+</p>
					<a href="<?php echo esc_url( home_url( '/product/letterbox/' ) ); ?>" style="background: #12786C; color: #FAFAF8; font-weight: 700; font-size: 15px; padding: 12px 0; border-radius: 999px; text-decoration: none; text-align: center;">Order letterbox gifts</a>
				</div>
			</div>

			<!-- Christmas hamper -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<?php // Real featured image of the Christmas Hamper product (attachment 53195). ?>
				<img src="https://treattrunk.co.uk/wp-content/uploads/2024/11/IMG_5475-1024x768.jpeg" alt="Vegan Christmas hamper corporate gift" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #B0492B; color: #FAFAF8; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Clients &amp; VIPs</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1B2420;">The Christmas Hamper</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #1B2420; margin: 0; flex: 1;">Our festive vegan hamper - the &ldquo;impress a client&rdquo; option. A generous, beautifully packed spread of the best festive snacks from small UK makers.</p>
					<p style="font-size: 15px; margin: 0; color: #0B5951; font-weight: 700;">&pound;39.99 &middot; volume pricing on request</p>
					<a href="<?php echo esc_url( home_url( '/product/christmas-hamper-vegan-snack-box-christmas-gift/' ) ); ?>" style="background: #12786C; color: #FAFAF8; font-weight: 700; font-size: 15px; padding: 12px 0; border-radius: 999px; text-decoration: none; text-align: center;">Order hampers</a>
				</div>
			</div>

			<!-- Full-size box -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2022/02/IMG_0413-1-768x1024.jpeg" alt="Full size healthy snack box Christmas gift" style="width: 100%; height: 180px; object-fit: cover; display: block;">
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1B2420;">The Full Treat Trunk</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #1B2420; margin: 0; flex: 1;">20&ndash;25 full-size healthy snacks - the generous all-rounder for office deliveries, end-of-year thank-yous and team celebration days.</p>
					<p style="font-size: 15px; margin: 0; color: #0B5951; font-weight: 700;">&pound;37.50 at 20+ &middot; &pound;35.00 at 50+</p>
					<a href="<?php echo esc_url( home_url( '/product/one-off-treat-trunk/' ) ); ?>" style="background: #12786C; color: #FAFAF8; font-weight: 700; font-size: 15px; padding: 12px 0; border-radius: 999px; text-decoration: none; text-align: center;">Order full boxes</a>
				</div>
			</div>

		</div>
	</section>

	<!-- Deadlines -->
	<section class="tt-corp-section" style="padding: 40px 48px 72px; max-width: 1240px; margin: 0 auto;">
		<div style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; padding: 36px 40px;">
			<h2 style="font-weight: 700; font-size: 26px; margin: 0 0 20px; color: #1B2420;">Christmas order deadlines</h2>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
				<div style="border-left: 3px solid #12786C; padding-left: 16px;">
					<p style="font-weight: 700; font-size: 15px; margin: 0; color: #0B5951;">Fri 20 November</p>
					<p style="font-size: 14.5px; margin: 4px 0 0; color: #1B2420;">Branded &amp; bespoke orders (custom stickers, gift cards, mixed boxes)</p>
				</div>
				<div style="border-left: 3px solid #12786C; padding-left: 16px;">
					<p style="font-weight: 700; font-size: 15px; margin: 0; color: #0B5951;">Fri 11 December</p>
					<p style="font-size: 14.5px; margin: 4px 0 0; color: #1B2420;">Bulk orders to one office address</p>
				</div>
				<div style="border-left: 3px solid #12786C; padding-left: 16px;">
					<p style="font-weight: 700; font-size: 15px; margin: 0; color: #0B5951;">Thu 17 December</p>
					<p style="font-size: 14.5px; margin: 4px 0 0; color: #1B2420;">Individual gifts posted to home addresses</p>
				</div>
			</div>
			<p style="font-size: 13px; color: #5B6B68; margin: 18px 0 0;">Ordering early? We can hold dispatch to your chosen week - just tell us in the enquiry.</p>
		</div>
	</section>

	<!-- How it works -->
	<section class="tt-corp-section" style="padding: 0 48px 72px; max-width: 1240px; margin: 0 auto;">
		<h2 style="font-weight: 700; font-size: 30px; margin: 0 0 28px; color: #1B2420; text-align: center;">How corporate gifting works</h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
			<div style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; padding: 26px 28px;">
				<p style="font-weight: 800; font-size: 13px; letter-spacing: 0.06em; text-transform: uppercase; color: #12786C; margin: 0 0 8px;">1 &middot; Choose</p>
				<p style="font-size: 15px; line-height: 1.6; margin: 0; color: #1B2420;">Pick a gift tier - or mix them (hampers for clients, letterbox for the team). Tell us dietary needs; we cater for them at no extra cost.</p>
			</div>
			<div style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; padding: 26px 28px;">
				<p style="font-weight: 800; font-size: 13px; letter-spacing: 0.06em; text-transform: uppercase; color: #12786C; margin: 0 0 8px;">2 &middot; Send us the list</p>
				<p style="font-size: 15px; line-height: 1.6; margin: 0; color: #1B2420;">One office address, or a spreadsheet of home addresses - we handle every label. Add a personal note or your branding on the box.</p>
			</div>
			<div style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; padding: 26px 28px;">
				<p style="font-weight: 800; font-size: 13px; letter-spacing: 0.06em; text-transform: uppercase; color: #12786C; margin: 0 0 8px;">3 &middot; One invoice</p>
				<p style="font-size: 15px; line-height: 1.6; margin: 0; color: #1B2420;">We hand-pack, dispatch tracked, and invoice your company directly. You get the thank-you messages; we do the admin.</p>
			</div>
		</div>
	</section>

	<!-- Quote form (same endpoint as corporate-orders) -->
	<section id="quote" class="tt-corp-section" style="background: #12786C; padding: 64px 48px;">
		<div style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 48px; align-items: start;">
			<div style="display: flex; flex-direction: column; gap: 16px;">
				<h2 style="font-weight: 700; font-size: 32px; margin: 0; color: #FAFAF8;">Get your Christmas quote within 24 hours</h2>
				<p style="font-size: 16px; line-height: 1.6; color: #B9DBD6; margin: 0;">Tell us roughly how many people, gift tier (or mix), and whether it&rsquo;s one address or many. We&rsquo;ll come back with a full quote - usually the same day.</p>
				<ul style="list-style: none; margin: 8px 0 0; padding: 0; display: flex; flex-direction: column; gap: 8px; font-size: 14.5px; color: #B9DBD6; font-weight: 600; border-left: 2px solid rgba(250, 250, 248, 0.3);">
					<li style="padding-left: 14px;">hello@treattrunk.co.uk</li>
					<li style="padding-left: 14px;">Volume discounts &amp; company invoicing</li>
					<li style="padding-left: 14px;">Branded boxes, stickers &amp; gift notes</li>
				</ul>
			</div>
			<div style="background: #FAFAF8; border-radius: 22px; padding: 28px;">
				<form id="tt-gift-quote-form" novalidate style="display: flex; flex-direction: column; gap: 12px;">
					<label for="tt-gq-firstname" style="font-size: 13px; font-weight: 700; color: #1B2420; margin: 0;">First name *</label>
					<input type="text" id="tt-gq-firstname" name="firstname" required autocomplete="given-name" style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box;">
					<label for="tt-gq-lastname" style="font-size: 13px; font-weight: 700; color: #1B2420; margin: 0;">Last name</label>
					<input type="text" id="tt-gq-lastname" name="lastname" autocomplete="family-name" style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box;">
					<label for="tt-gq-email" style="font-size: 13px; font-weight: 700; color: #1B2420; margin: 0;">Work email *</label>
					<input type="email" id="tt-gq-email" name="email" required autocomplete="email" style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box;">
					<label for="tt-gq-message" style="font-size: 13px; font-weight: 700; color: #1B2420; margin: 0;">Your Christmas order <span style="font-weight: 400; color: #5B6B68;">(how many people, which gifts, one address or many, any branding)</span></label>
					<textarea id="tt-gq-message" name="message" rows="3" placeholder="e.g. 35 letterbox gifts to home addresses + 5 hampers for clients, week of 14 Dec" style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box; font-family: inherit; resize: vertical;"></textarea>
					<div style="position: absolute; left: -9999px;" aria-hidden="true"><label>Leave this blank<input type="text" name="tt_hp" tabindex="-1" autocomplete="off"></label></div>
					<label style="display: flex; gap: 8px; align-items: flex-start; font-size: 12.5px; line-height: 1.5; color: #5B6B68;">
						<input type="checkbox" name="consent" value="yes" required style="margin-top: 2px;">
						<span>I&rsquo;m happy for Treat Trunk to contact me about this enquiry. We won&rsquo;t add you to our newsletter - see our <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color: #12786C;">privacy policy</a>.</span>
					</label>
					<button type="submit" id="tt-gq-submit" style="background: #12786C; color: #FAFAF8; border: none; font-weight: 700; font-size: 16px; padding: 14px 0; border-radius: 999px; cursor: pointer;">Get my Christmas quote</button>
					<p id="tt-gq-status" role="status" style="font-size: 13.5px; line-height: 1.5; margin: 0; display: none;"></p>
				</form>
				<script id="tt-gift-quote">
				(function () {
					var form = document.getElementById('tt-gift-quote-form');
					var btn = document.getElementById('tt-gq-submit');
					var status = document.getElementById('tt-gq-status');
					function done( ok, msg ) {
						btn.disabled = false;
						btn.textContent = 'Get my Christmas quote';
						status.style.display = 'block';
						status.style.color = ok ? '#0B5951' : '#A03D3A';
						status.textContent = msg;
						if ( ok ) { form.reset(); }
					}
					form.addEventListener('submit', function ( e ) {
						e.preventDefault();
						if ( form.reportValidity && ! form.reportValidity() ) { return; }
						btn.disabled = true;
						btn.textContent = 'Sending…';
						var data = new FormData( form );
						fetch( '/wp-admin/admin-ajax.php?action=tt_corp_enquiry', { method: 'POST', body: data } )
							.then( function ( r ) { return r.json(); } )
							.then( function ( res ) {
								if ( res && res.success ) {
									done( true, 'Thanks, we have your enquiry. We reply personally, usually the same day and always within 24 hours.' );
								} else {
									done( false, ( res && res.data && res.data.message ) || 'Something went wrong. Please email hello@treattrunk.co.uk.' );
								}
							} )
							.catch( function () {
								done( false, 'Something went wrong. Please email hello@treattrunk.co.uk directly.' );
							} );
					});
				})();
				</script>
			</div>
		</div>
	</section>

	<!-- SEO / reassurance text -->
	<section class="tt-corp-section" style="padding: 56px 48px 72px; max-width: 860px; margin: 0 auto;">
		<h2 style="font-weight: 700; font-size: 24px; margin: 0 0 14px; color: #1B2420;">Healthy corporate Christmas gifts, from a small UK business</h2>
		<p style="font-size: 15.5px; line-height: 1.7; color: #1B2420; margin: 0 0 12px;">Treat Trunk has hand-packed healthy snack boxes for over seven years - every box vegetarian, mostly vegan, and sourced from small, independent UK brands. Instead of another bottle of wine or a tin of biscuits, send your clients and employees a Christmas gift that feels personal, ships tracked, and works for almost every dietary requirement in the office - vegan, gluten-free and nut-aware options included.</p>
		<p style="font-size: 15.5px; line-height: 1.7; color: #1B2420; margin: 0;">We&rsquo;ve handled everything from five hampers for a founder&rsquo;s best clients to an 800-employee letterbox campaign. Looking for year-round office snacks instead? See our <a href="<?php echo esc_url( home_url( '/corporate-orders/' ) ); ?>" style="color: #12786C; font-weight: 700;">corporate snack boxes</a>, or read our guide to <a href="<?php echo esc_url( home_url( '/the-best-corporate-wellbeing-gifts-to-support-your-employees-healthwellness/' ) ); ?>" style="color: #12786C; font-weight: 700;">corporate wellbeing gifts</a>.</p>
	</section>

</div>

<?php
get_footer();
