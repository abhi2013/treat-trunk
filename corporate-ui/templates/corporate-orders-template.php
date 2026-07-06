<?php
/**
 * Template Name: Corporate Orders (Custom Redesign)
 *
 * Server-rendered replacement for the Elementor-built Corporate Orders page.
 * Reuses the theme's real header/footer (Elementor Theme Builder) so nav,
 * mini-cart and site-wide chrome stay untouched. See docs/elementor-removal-plan.md.
 *
 * PLACEHOLDER IMAGES: the two hero/lifestyle photos from the Claude Design
 * mockup (photos-1783026072643.jpeg, photos-1783026082440.jpeg) could not be
 * fetched in full resolution (256KB tool cap). Using existing real product
 * photos as placeholders until the real ones are uploaded to the media
 * library and the URLs below are swapped in - see TODO markers.
 */

get_header();
?>

<div class="tt-corp-page" style="font-family: 'Nunito Sans', -apple-system, sans-serif; color: #1F3B2C; background: #FBF8F2;">

	<!-- Announcement bar (WeWork offer - confirmed active 2026-07-04) -->
	<div style="background: #1F4D38; color: #F6EED9; text-align: center; padding: 10px 16px; font-size: 14px; font-weight: 600;">
		In a WeWork office? Claim a FREE welcome snack box for your team &nbsp;&middot;&nbsp;
		<a href="#wework" style="color: #F2C94C; text-decoration: underline; text-underline-offset: 3px;">Claim yours</a>
	</div>

	<!-- Hero -->
	<section class="tt-corp-hero" style="display: grid; grid-template-columns: 1.05fr 1fr; gap: 48px; align-items: center; padding: 72px 48px 80px; max-width: 1240px; margin: 0 auto;">
		<div style="display: flex; flex-direction: column; gap: 22px;">
			<div style="display: flex; gap: 10px; flex-wrap: wrap;">
				<span style="background: #EADFC8; color: #6B5A2E; font-size: 12.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;">For offices &amp; teams</span>
				<span style="background: #E3EFE2; color: #1F4D38; font-size: 12.5px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;">Vegetarian &amp; low sugar</span>
			</div>
			<h1 style="font-weight: 700; font-size: 44px; line-height: 1.1; margin: 0; color: #1F3B2C;">Corporate snack boxes your team will actually fight over</h1>
			<p style="font-size: 18px; line-height: 1.6; margin: 0; max-width: 52ch; color: #44543F;">Hand-packed office snacks from independent UK brands - delivered to your office, or straight through the letterbox of every remote employee. No vending-machine sadness, no admin.</p>
			<div style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
				<a href="#boxes" style="background: #C75B39; color: #FFF7EE; font-weight: 700; font-size: 17px; padding: 15px 30px; border-radius: 999px; text-decoration: none;">Shop corporate boxes</a>
				<a href="#quote" style="color: #1F4D38; font-weight: 700; font-size: 16px; text-decoration: underline; text-underline-offset: 4px;">Request a quote &rarr;</a>
			</div>
			<div style="display: flex; gap: 26px; margin-top: 6px; font-size: 14px; color: #6A7A64; font-weight: 600; flex-wrap: wrap;">
				<span>&#10003; No minimum order</span>
				<span>&#10003; Dietary needs catered</span>
				<span>&#10003; 800+ box orders handled</span>
			</div>
		</div>
		<div style="position: relative;">
			<?php // TODO: swap for uploads/photos-1783026082440.jpeg once uploaded to the media library ?>
			<img src="https://treattrunk.co.uk/wp-content/uploads/2022/02/IMG_0413-1-768x1024.jpeg" alt="Corporate snack box order being hand-packed at Treat Trunk HQ" style="width: 100%; height: 460px; object-fit: cover; border-radius: 24px; box-shadow: 0 24px 48px -20px rgba(31, 61, 44, 0.35);">
		</div>
	</section>

	<!-- Social proof strip -->
	<div style="background: #F2ECDE; padding: 18px 48px;">
		<div style="max-width: 1240px; margin: 0 auto; display: flex; align-items: center; justify-content: center; gap: 40px; flex-wrap: wrap; font-size: 14px; color: #6B5A2E; font-weight: 600;">
			<span style="font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; font-size: 12px;">Trusted by teams big &amp; small</span>
			<span>High Speed Training Ltd</span><span>&middot;</span>
			<span>An 800-employee letterbox campaign</span><span>&middot;</span>
			<span>Startups, agencies &amp; NHS teams</span>
		</div>
	</div>

	<!-- Product cards -->
	<section id="boxes" class="tt-corp-section" style="padding: 72px 48px 40px; max-width: 1240px; margin: 0 auto;">
		<div style="text-align: center; margin-bottom: 44px;">
			<h2 style="font-weight: 700; font-size: 34px; margin: 0 0 12px; color: #1F3B2C;">Pick your corporate snacking setup</h2>
			<p style="font-size: 17px; color: #44543F; margin: 0;">Buy online in minutes - or <a href="#quote" style="color: #C75B39; font-weight: 700;">talk to our team</a> for bespoke branding, mixed orders and volume pricing.</p>
		</div>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">

			<!-- Card 1: Letterbox, bulk discount applies automatically at checkout -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2021/05/Treat-Trunk-Mini-Healthy-Snack-Box-March-1200.jpg" alt="Corporate letterbox office snack box" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #F2C94C; color: #4A3A08; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Best seller</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1F3B2C;">Bulk Letterbox Boxes</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #44543F; margin: 0; flex: 1;">Letterbox-friendly snack boxes delivered directly to your office address. Order online in one click - volume pricing applies automatically, no code needed. Want your branding on the box? <a href="#quote" style="color: #C75B39; font-weight: 700;">Contact us directly</a>.</p>
					<div style="font-weight: 700; font-size: 22px; color: #1F4D38;">£15.99 <span style="font-size: 13.5px; font-weight: 600; color: #6A7A64;">/box &middot; 20+: £13.75/box &middot; 50+: £13.00/box</span></div>

					<?php
					// One-click bulk ordering: WooCommerce's native add-to-cart URL
					// (?add-to-cart={id}&quantity={n}) adds the item and redirects
					// straight to the basket at the correct discounted price - no
					// product-page visit, no manually typing a quantity, no "email us."
					$letterbox_id = 40245;
					$qty20_url    = esc_url( add_query_arg( array( 'add-to-cart' => $letterbox_id, 'quantity' => 20 ), home_url( '/' ) ) );
					$qty50_url    = esc_url( add_query_arg( array( 'add-to-cart' => $letterbox_id, 'quantity' => 50 ), home_url( '/' ) ) );
					?>
					<div style="display: flex; flex-direction: column; gap: 8px;">
						<a href="<?php echo $qty20_url; ?>" style="background: #1F4D38; color: #F6EED9; text-align: center; font-weight: 700; font-size: 15px; padding: 12px 0; border-radius: 999px; text-decoration: none;">Order 20 boxes (£275) &rarr;</a>
						<a href="<?php echo $qty50_url; ?>" style="background: #1F4D38; color: #F6EED9; text-align: center; font-weight: 700; font-size: 15px; padding: 12px 0; border-radius: 999px; text-decoration: none;">Order 50 boxes (£650) &rarr;</a>
						<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; gap: 6px; margin-top: 2px;">
							<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $letterbox_id ); ?>">
							<input type="number" name="quantity" min="1" value="1" aria-label="Custom quantity" style="width: 70px; padding: 10px 8px; border: 1.5px solid #DDD3BE; border-radius: 10px; font-size: 14px;">
							<button type="submit" style="flex: 1; background: #FFFFFF; color: #1F4D38; border: 2px solid #1F4D38; font-weight: 700; font-size: 14px; padding: 10px 0; border-radius: 999px; cursor: pointer;">Add custom quantity</button>
						</form>
					</div>
				</div>
			</div>

			<!-- Card 2: Monthly Office Subscription -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2020/08/Treat-Trunk-August-2020-1200.jpg" alt="Monthly office snack box" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #E3EFE2; color: #1F4D38; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Subscription</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1F3B2C;">Monthly Office Subscription</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #44543F; margin: 0; flex: 1;">A big box of 20+ healthy snacks for the office kitchen, refreshed every month. Pause or cancel anytime.</p>
					<div style="font-weight: 700; font-size: 22px; color: #1F4D38;">£39.99<span style="font-size: 14px; font-weight: 600; color: #6A7A64;">/month</span></div>
					<a href="<?php echo esc_url( home_url( '/product/treat-trunk-monthly-subscription/' ) ); ?>" style="background: #1F4D38; color: #F6EED9; text-align: center; font-weight: 700; font-size: 15.5px; padding: 12px 0; border-radius: 999px; text-decoration: none;">Start a subscription</a>
				</div>
			</div>

			<!-- Card 3: Remote Team Boxes - quote/manual for now, no product page yet -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2021/05/Treat-Trunk-Healthy-Vegan-Snack-Box-1200-2.jpg" alt="Snack boxes for remote staff" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #EADFC8; color: #6B5A2E; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Remote teams</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1F3B2C;">Remote Team Boxes</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #44543F; margin: 0; flex: 1;">Healthy office snacks delivered individually to each remote employee&rsquo;s home address. Send us a spreadsheet of addresses and we&rsquo;ll sort the rest - get a quote tailored to your team size.</p>
					<div style="font-weight: 700; font-size: 22px; color: #1F4D38;">Custom pricing</div>
					<a href="#quote" style="background: #FFFFFF; color: #1F4D38; border: 2px solid #1F4D38; text-align: center; font-weight: 700; font-size: 15.5px; padding: 10px 0; border-radius: 999px; text-decoration: none;">Set up for my team</a>
				</div>
			</div>

			<!-- Card 4: Client & Staff Gifting -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2019/10/Treat-Trunk-Healthy-Snack-Box-1200-scaled.jpg" alt="Client gift snack box" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #F7DFD6; color: #A94729; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Gifting</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1F3B2C;">Client &amp; Staff Gifting</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #44543F; margin: 0; flex: 1;">Corporate gifting for one-off thank-yous, onboarding gifts and Christmas orders. Add your branding to boxes, stickers and gift cards.</p>
					<div style="font-weight: 700; font-size: 22px; color: #1F4D38;">£28.99<span style="font-size: 14px; font-weight: 600; color: #6A7A64;">/gift</span></div>
					<a href="<?php echo esc_url( home_url( '/product/one-off-treat-trunk/' ) ); ?>" style="background: #1F4D38; color: #F6EED9; text-align: center; font-weight: 700; font-size: 15.5px; padding: 12px 0; border-radius: 999px; text-decoration: none;">Send a gift box</a>
				</div>
			</div>

			<!-- Card 5: Deluxe Corporate Snack Box -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 2px solid #1F4D38; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2021/05/one-off-trunk-1024x1024.jpg" alt="Deluxe corporate snack box with 60+ office snacks" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #1F4D38; color: #F6EED9; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Biggest box</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1F3B2C;">Deluxe Corporate Snack Box</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #44543F; margin: 0; flex: 1;">Our biggest one-off box: 60+ sugar sensible, predominantly vegan snacks (some fan favourites included in multiples) - built for office kitchens, team days and client visits.</p>
					<div style="font-weight: 700; font-size: 22px; color: #1F4D38;">£125<span style="font-size: 14px; font-weight: 600; color: #6A7A64;">/box</span></div>
					<a href="<?php echo esc_url( home_url( '/product/corporate-snack-box/' ) ); ?>" style="background: #1F4D38; color: #F6EED9; text-align: center; font-weight: 700; font-size: 15.5px; padding: 12px 0; border-radius: 999px; text-decoration: none;">Shop the deluxe box</a>
				</div>
			</div>

			<!-- Card 6: Weekly Office Subscription (big box, weekly cadence) -->
			<div class="tt-corp-card-wrap" style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column;">
				<div style="position: relative;">
					<img src="https://treattrunk.co.uk/wp-content/uploads/2020/08/Treat-Trunk-August-2020-1200.jpg" alt="Weekly office snack box" style="width: 100%; height: 180px; object-fit: cover; display: block;">
					<span style="position: absolute; top: 12px; left: 12px; background: #E3EFE2; color: #1F4D38; font-size: 11.5px; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Subscription</span>
				</div>
				<div class="tt-corp-card" style="display: flex; flex-direction: column; gap: 10px; flex: 1;">
					<h3 style="font-weight: 700; font-size: 20px; margin: 0; color: #1F3B2C;">Weekly Office Subscription</h3>
					<p style="font-size: 14.5px; line-height: 1.55; color: #44543F; margin: 0; flex: 1;">Our full-size 20+ snack box, refreshed every week instead of once. Pause or cancel anytime.</p>
					<div style="font-weight: 700; font-size: 22px; color: #1F4D38;">£39.99<span style="font-size: 14px; font-weight: 600; color: #6A7A64;">/week</span></div>
					<a href="<?php echo esc_url( home_url( '/product/treat-trunk-weekly-subscription/' ) ); ?>" style="background: #1F4D38; color: #F6EED9; text-align: center; font-weight: 700; font-size: 15.5px; padding: 12px 0; border-radius: 999px; text-decoration: none;">Start a weekly subscription</a>
				</div>
			</div>
		</div>
		</div>
		<p style="text-align: center; font-size: 14px; color: #6A7A64; margin: 26px 0 0;">Bigger team? Mixed dietary needs? <a href="#quote" style="color: #C75B39; font-weight: 700;">Get volume pricing</a> - we&rsquo;ve shipped orders from 5 boxes to 800+.</p>
	</section>

	<!-- Comparison: value/attributes, not just price (rebased on Letterbox bulk pricing 2026-07-04) -->
	<section class="tt-corp-section" style="padding: 24px 48px 56px; max-width: 900px; margin: 0 auto;">
		<div style="text-align: center; margin-bottom: 32px;">
			<h2 style="font-weight: 700; font-size: 32px; margin: 0 0 10px; color: #1F3B2C;">Why Treat Trunk over a standard office box</h2>
			<p style="font-size: 16px; color: #44543F; margin: 0;">It&rsquo;s not just price - it&rsquo;s what&rsquo;s actually in the box.</p>
		</div>
		<div style="overflow-x: auto;">
		<table style="width: 100%; border-collapse: collapse; background: #FFFFFF; border-radius: 20px; overflow: hidden; border: 1px solid #E8E0D0;">
			<thead>
				<tr style="background: #1F4D38;">
					<th style="text-align: left; padding: 16px 20px; font-weight: 700; font-size: 14.5px; color: #CFE0CC;"></th>
					<th style="text-align: left; padding: 16px 20px; font-weight: 700; font-size: 15.5px; color: #F6EED9;">Treat Trunk</th>
					<th style="text-align: left; padding: 16px 20px; font-weight: 700; font-size: 15.5px; color: #CFE0CC;">Competitor</th>
				</tr>
			</thead>
			<tbody style="font-size: 14.5px; color: #33422F;">
				<tr style="border-top: 1px solid #E8E0D0;">
					<td style="padding: 14px 20px; font-weight: 700;">Bulk price</td>
					<td style="padding: 14px 20px;">from <strong>£13.00/box</strong> on 50+ box orders</td>
					<td style="padding: 14px 20px; color: #6A7A64;">£87.50 per 50 snacks (£1.75/snack), no bulk tiers</td>
				</tr>
				<tr style="border-top: 1px solid #E8E0D0; background: #FBF8F2;">
					<td style="padding: 14px 20px; font-weight: 700;">Sugar-conscious</td>
					<td style="padding: 14px 20px;">&#10003; Every snack is low-sugar by default</td>
					<td style="padding: 14px 20px; color: #6A7A64;">&#10005; Mixed selection, not sugar-focused</td>
				</tr>
				<tr style="border-top: 1px solid #E8E0D0;">
					<td style="padding: 14px 20px; font-weight: 700;">Good-for-you snacks</td>
					<td style="padding: 14px 20px;">&#10003; Handpicked for nutritional value, mostly vegan</td>
					<td style="padding: 14px 20px; color: #6A7A64;">&#10005; Standard snack mix, not health-curated</td>
				</tr>
				<tr style="border-top: 1px solid #E8E0D0; background: #FBF8F2;">
					<td style="padding: 14px 20px; font-weight: 700;">Allergy &amp; dietary catering</td>
					<td style="padding: 14px 20px;">&#10003; Gluten-free/nut-free tailored per person, even within one bulk order</td>
					<td style="padding: 14px 20px; color: #6A7A64;">&#10005; No per-person tailoring mentioned</td>
				</tr>
				<tr style="border-top: 1px solid #E8E0D0;">
					<td style="padding: 14px 20px; font-weight: 700;">Minimum order</td>
					<td style="padding: 14px 20px;">&#10003; None</td>
					<td style="padding: 14px 20px; color: #6A7A64;">&#10005; 50-snack minimum</td>
				</tr>
				<tr style="border-top: 1px solid #E8E0D0; background: #FBF8F2;">
					<td style="padding: 14px 20px; font-weight: 700;">Delivery</td>
					<td style="padding: 14px 20px;">&#10003; Letterbox to home addresses, or bulk to one office</td>
					<td style="padding: 14px 20px; color: #6A7A64;">&#10005; Office-only</td>
				</tr>
			</tbody>
		</table>
		</div>
		<p style="text-align: center; font-size: 12.5px; color: #8A937F; margin: 18px 0 0;">Competitor pricing checked July 2026 from a comparable UK office snack box provider's published pricing (ex VAT, 20% VAT added for a like-for-like comparison).</p>
	</section>

	<!-- WeWork offer -->
	<section id="wework" class="tt-corp-section" style="padding: 48px; max-width: 1240px; margin: 0 auto;">
		<div style="background: #1F4D38; border-radius: 28px; padding: 48px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: start;">
			<div style="display: flex; flex-direction: column; gap: 18px;">
				<span style="align-self: flex-start; background: #F2C94C; color: #4A3A08; font-size: 12px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 999px;">Free for WeWork members</span>
				<h2 style="font-weight: 700; font-size: 32px; line-height: 1.15; margin: 0; color: #F6EED9;">Based in a WeWork? Your first box is on us.</h2>
				<p style="font-size: 17px; line-height: 1.6; color: #CFE0CC; margin: 0; max-width: 56ch;">Any business working from a WeWork office space gets a free welcome snack box - a full-size Treat Trunk with 20+ healthy snacks for the team to try. No card details, no commitment. Pick your WeWork building and we&rsquo;ll take it from there.</p>
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
			<form id="tt-wework-form" style="background: #FBF8F2; border-radius: 22px; padding: 26px; display: flex; flex-direction: column; gap: 12px;">
				<input type="text" id="tt-ww-name" placeholder="Your name" required style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #DDD3BE; border-radius: 12px; width: 100%; box-sizing: border-box;">
				<input type="text" id="tt-ww-company" placeholder="Company name" required style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #DDD3BE; border-radius: 12px; width: 100%; box-sizing: border-box;">
				<select id="tt-ww-location" required style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #DDD3BE; border-radius: 12px; width: 100%; box-sizing: border-box;">
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
				<input type="text" id="tt-ww-address" placeholder="Suite/floor or delivery notes (optional)" style="font-size: 15px; padding: 12px 14px; border: 1.5px solid #DDD3BE; border-radius: 12px; width: 100%; box-sizing: border-box;">
				<button type="submit" style="background: #F2C94C; color: #4A3A08; border: none; font-weight: 700; font-size: 16px; padding: 14px 0; border-radius: 999px; cursor: pointer;">Claim your free welcome box</button>
				<span style="font-size: 12.5px; color: #6A7A64;">Opens a pre-filled email to hello@treattrunk.co.uk - nothing sends without you pressing send.</span>
			</form>
			<script>
			document.getElementById('tt-wework-form').addEventListener('submit', function (e) {
				e.preventDefault();
				var name = document.getElementById('tt-ww-name').value;
				var company = document.getElementById('tt-ww-company').value;
				var location = document.getElementById('tt-ww-location').value;
				var address = document.getElementById('tt-ww-address').value;
				var subject = 'WeWork free welcome box - ' + company;
				var body = 'Name: ' + name + '\nCompany: ' + company + '\nWeWork building: ' + location
					+ (address ? '\nNotes: ' + address : '') + '\n\nPlease send our free WeWork welcome box!';
				window.location.href = 'mailto:hello@treattrunk.co.uk?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(body);
			});
			</script>
		</div>
	</section>

	<!-- How it works -->
	<section class="tt-corp-section" style="padding: 56px 48px; max-width: 1240px; margin: 0 auto;">
		<h2 style="font-weight: 700; font-size: 32px; text-align: center; margin: 0 0 40px; color: #1F3B2C;">Corporate orders, without the admin</h2>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
			<div style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; padding: 28px; display: flex; flex-direction: column; gap: 12px;">
				<div style="width: 40px; height: 40px; border-radius: 999px; background: #E3EFE2; color: #1F4D38; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">1</div>
				<h3 style="font-weight: 700; font-size: 19px; margin: 0; color: #1F3B2C;">Tell us about your team</h3>
				<p style="font-size: 15px; line-height: 1.6; color: #44543F; margin: 0;">Headcount, budget, dietary needs, one office or fifty home addresses - order online or drop us a message.</p>
			</div>
			<div style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; padding: 28px; display: flex; flex-direction: column; gap: 12px;">
				<div style="width: 40px; height: 40px; border-radius: 999px; background: #E3EFE2; color: #1F4D38; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">2</div>
				<h3 style="font-weight: 700; font-size: 19px; margin: 0; color: #1F3B2C;">We hand-pack every box</h3>
				<p style="font-size: 15px; line-height: 1.6; color: #44543F; margin: 0;">Snacks are handpicked from small, ethical UK brands - vegetarian, mostly vegan, low sugar, with your branding on request.</p>
			</div>
			<div style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 20px; padding: 28px; display: flex; flex-direction: column; gap: 12px;">
				<div style="width: 40px; height: 40px; border-radius: 999px; background: #E3EFE2; color: #1F4D38; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">3</div>
				<h3 style="font-weight: 700; font-size: 19px; margin: 0; color: #1F3B2C;">Delivered &amp; tracked</h3>
				<p style="font-size: 15px; line-height: 1.6; color: #44543F; margin: 0;">Bulk to one location or individually to every doorstep - all tracked, letterbox-friendly.</p>
			</div>
		</div>
	</section>

	<!-- Testimonials (verified against real page content 2026-07-04) -->
	<section class="tt-corp-section" style="background: #F2ECDE; padding: 56px 48px;">
		<div style="max-width: 1240px; margin: 0 auto;">
			<h2 style="font-weight: 700; font-size: 32px; text-align: center; margin: 0 0 36px; color: #1F3B2C;">What corporate clients say</h2>
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
				<figure style="background: #FFFFFF; border-radius: 20px; padding: 26px; margin: 0; display: flex; flex-direction: column; gap: 14px;">
					<div style="color: #F2C94C; font-size: 16px;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
					<blockquote style="margin: 0; font-size: 15px; line-height: 1.6; color: #33422F; font-style: italic;">&ldquo;An extremely good value range of high-quality snacks, catering to the many allergen and dietary requirements we had, sent with tracking information and amazing customer service from Sally.&rdquo;</blockquote>
					<figcaption style="font-size: 14px; font-weight: 700; color: #1F4D38;">Andy &middot; High Speed Training Ltd</figcaption>
				</figure>
				<figure style="background: #FFFFFF; border-radius: 20px; padding: 26px; margin: 0; display: flex; flex-direction: column; gap: 14px;">
					<div style="color: #F2C94C; font-size: 16px;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
					<blockquote style="margin: 0; font-size: 15px; line-height: 1.6; color: #33422F; font-style: italic;">&ldquo;We ordered over 800 letterbox gifts to be sent to our staff - Sally was great and provided lots of options and costs to help us decide. A pleasure to deal with Treat Trunk.&rdquo;</blockquote>
					<figcaption style="font-size: 14px; font-weight: 700; color: #1F4D38;">800-employee letterbox order</figcaption>
				</figure>
				<figure style="background: #FFFFFF; border-radius: 20px; padding: 26px; margin: 0; display: flex; flex-direction: column; gap: 14px;">
					<div style="color: #F2C94C; font-size: 16px;">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
					<blockquote style="margin: 0; font-size: 15px; line-height: 1.6; color: #33422F; font-style: italic;">&ldquo;Everyone was delighted to receive their boxes and we&rsquo;ve had lots of positive feedback. The ordering process was really easy and boxes were dispatched promptly.&rdquo;</blockquote>
					<figcaption style="font-size: 14px; font-weight: 700; color: #1F4D38;">Staff wellbeing order</figcaption>
				</figure>
			</div>
		</div>
	</section>

	<!-- Why Treat Trunk -->
	<section class="tt-corp-section" style="padding: 56px 48px; max-width: 1240px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: center;">
		<img src="https://treattrunk.co.uk/wp-content/uploads/2022/02/IMG_0413-1-768x1024.jpeg" alt="Corporate order packed at Treat Trunk" style="width: 100%; height: 420px; object-fit: cover; border-radius: 24px;">
		<div style="display: flex; flex-direction: column; gap: 16px;">
			<h2 style="font-weight: 700; font-size: 30px; margin: 0; color: #1F3B2C;">Why offices choose Treat Trunk</h2>
			<div style="display: flex; flex-direction: column; gap: 12px; font-size: 15px; line-height: 1.55; color: #44543F;">
				<div>&#10003; <strong>No minimum order</strong> - we work with big brands and five-person startups alike.</div>
				<div>&#10003; <strong>Dietary needs sorted</strong> - vegetarian, mostly vegan, low sugar, allergen requirements handled per person.</div>
				<div>&#10003; <strong>Your branding, our boxes</strong> - logos on stickers, wrapping and gift cards.</div>
				<div>&#10003; <strong>Independent UK brands</strong> - every box champions small, ethical snack makers.</div>
				<div>&#10003; <strong>One human, start to finish</strong> - you&rsquo;ll deal with a real person on our team, not a ticketing system.</div>
			</div>
		</div>
	</section>

	<!-- FAQ -->
	<section class="tt-corp-section" style="padding: 24px 48px 56px; max-width: 800px; margin: 0 auto;">
		<h2 style="font-weight: 700; font-size: 32px; text-align: center; margin: 0 0 28px; color: #1F3B2C;">Corporate FAQs</h2>
		<div style="display: flex; flex-direction: column; gap: 12px;">
			<details style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1F3B2C; cursor: pointer;">What is a corporate snack box?</summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #44543F; margin: 10px 0 0;">A corporate snack box is a curated selection of office snacks delivered to your workplace or straight to staff at home, ideal for office kitchens, client visits, staff wellbeing and team perks. Ours are predominantly vegan, sugar sensible and sourced from independent UK brands.</p>
			</details>
			<details style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1F3B2C; cursor: pointer;">How much do office snacks cost for a team?</summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #44543F; margin: 10px 0 0;">Our Letterbox snack box is £15.99/box, dropping automatically to £13.75/box on 20+ box orders and £13.00/box on 50+ box orders to one address. A one-off Deluxe Corporate Snack Box (60+ snacks) is £125. No minimum order and no discount code needed.</p>
			</details>
			<details style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1F3B2C; cursor: pointer;">Can you deliver to lots of individual home addresses?</summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #44543F; margin: 10px 0 0;">Yes - this is our speciality. Send us a spreadsheet of names and addresses and we&rsquo;ll post a tracked, letterbox-friendly box to every one. We&rsquo;ve handled orders of 800+.</p>
			</details>
			<details style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1F3B2C; cursor: pointer;">Do the bulk discounts need a code?</summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #44543F; margin: 10px 0 0;">No - order 20 or more Letterbox boxes to one address and the discount applies automatically in your cart. No code needed.</p>
			</details>
			<details style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1F3B2C; cursor: pointer;">Can you handle dietary requirements and allergies?</summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #44543F; margin: 10px 0 0;">All boxes are vegetarian (mostly vegan), low sugar and health-conscious throughout. We can tailor individual boxes for gluten-free, nut-free and other requirements, even within a single bulk order.</p>
			</details>
			<details style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1F3B2C; cursor: pointer;">Can we add our company branding?</summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #44543F; margin: 10px 0 0;">Yes - we can incorporate your branding on boxes, stickers, wrapping and gift cards. Mention it in your enquiry and we&rsquo;ll quote for it.</p>
			</details>
			<details style="background: #FFFFFF; border: 1px solid #E8E0D0; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1F3B2C; cursor: pointer;">Can we pay by invoice?</summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #44543F; margin: 10px 0 0;">Yes - for corporate orders we can invoice your company directly. Mention it in the enquiry form and we&rsquo;ll set it up.</p>
			</details>
		</div>
	</section>

	<!-- Quote / enquiry - REAL ActiveCampaign integration, not a mock form -->
	<section id="quote" class="tt-corp-section" style="background: #1F4D38; padding: 64px 48px;">
		<div style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 48px; align-items: start;">
			<div style="display: flex; flex-direction: column; gap: 16px;">
				<h2 style="font-weight: 700; font-size: 32px; margin: 0; color: #F6EED9;">Request a quote within 24 hours</h2>
				<p style="font-size: 16px; line-height: 1.6; color: #CFE0CC; margin: 0;">Tell us about your team and what you have in mind - bulk letterbox orders, a monthly subscription, gifting, or your free WeWork welcome box. Our team replies personally, usually the same day.</p>
				<ul style="list-style: none; margin: 8px 0 0; padding: 0; display: flex; flex-direction: column; gap: 8px; font-size: 14.5px; color: #9FBCA0; font-weight: 600; border-left: 2px solid rgba(159, 188, 160, 0.4);">
					<li style="padding-left: 14px;">hello@treattrunk.co.uk</li>
					<li style="padding-left: 14px;">Replies within 24 hours</li>
					<li style="padding-left: 14px;">Volume discounts &amp; invoicing available</li>
				</ul>
			</div>
			<div style="background: #FBF8F2; border-radius: 22px; padding: 28px;">
				<?php
				// Real, working lead-capture integration - preserved exactly as used
				// elsewhere on the site (see docs/frontend-techniques.md). Do not
				// replace with a custom form; ActiveCampaign owns this submission.
				echo do_shortcode( '[activecampaign form=1 css=1]' );
				?>
			</div>
		</div>
	</section>

</div>

<?php
get_footer();
