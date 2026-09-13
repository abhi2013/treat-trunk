<?php
/**
 * Shared corporate quote form (added 2026-09-13).
 *
 * Used by all three corporate-ui templates so the markup, honeypot, consent
 * line and AJAX handling live in one place. Posts to the existing
 * tt_corp_enquiry endpoint in site-core.php, which now forwards every extra
 * field below in the email body.
 *
 * Expects $tt_form = array(
 *   'id'        => 'xmas',                 // unique per page, used for element ids
 *   'source'    => 'corporate-christmas-hampers',
 *   'heading'   => '...',
 *   'intro'     => '...',
 *   'bullets'   => array( '...', '...' ),
 *   'fields'    => array( array( 'name' => 'company', 'label' => 'Company', 'type' => 'text' ), ... ),
 *   'message'   => array( 'label' => '...', 'hint' => '...', 'placeholder' => '...' ),
 *   'button'    => 'Get my Christmas quote',
 *   'success'   => 'Thanks ...',
 * );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tt_f_id    = preg_replace( '/[^a-z0-9\-]/', '', strtolower( $tt_form['id'] ) );
$tt_f_input = 'font-size: 15px; padding: 12px 14px; border: 1.5px solid #C4DDDA; border-radius: 12px; width: 100%; box-sizing: border-box; font-family: inherit; background: #FFFFFF; color: #1B2420;';
$tt_f_label = 'font-size: 13px; font-weight: 700; color: #1B2420; margin: 0;';
?>
<section id="quote" class="tt-corp-section" style="background: #12786C; padding: 64px 48px;">
	<div style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 48px; align-items: start;">
		<div style="display: flex; flex-direction: column; gap: 16px;">
			<h2 style="font-weight: 700; font-size: 32px; margin: 0; color: #FAFAF8;"><?php echo esc_html( $tt_form['heading'] ); ?></h2>
			<p style="font-size: 16px; line-height: 1.6; color: #B9DBD6; margin: 0;"><?php echo esc_html( $tt_form['intro'] ); ?></p>
			<ul style="list-style: none; margin: 8px 0 0; padding: 0; display: flex; flex-direction: column; gap: 8px; font-size: 14.5px; color: #B9DBD6; font-weight: 600; border-left: 2px solid rgba(250, 250, 248, 0.3);">
				<?php foreach ( $tt_form['bullets'] as $tt_b ) : ?>
					<li style="padding-left: 14px;"><?php echo esc_html( $tt_b ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p style="font-size: 12.5px; line-height: 1.6; color: #B9DBD6; margin: 12px 0 0;">Treat Trunk Ltd &middot; Company No. 15624707 &middot; 86-90 Paul Street, London EC2A 4NE</p>
		</div>
		<div style="background: #FAFAF8; border-radius: 22px; padding: 28px;">
			<form id="tt-quote-<?php echo esc_attr( $tt_f_id ); ?>" novalidate style="display: flex; flex-direction: column; gap: 12px;">
				<input type="hidden" name="source" value="<?php echo esc_attr( $tt_form['source'] ); ?>">
				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
					<div style="display: flex; flex-direction: column; gap: 6px;">
						<label for="tt-<?php echo esc_attr( $tt_f_id ); ?>-firstname" style="<?php echo esc_attr( $tt_f_label ); ?>">First name *</label>
						<input type="text" id="tt-<?php echo esc_attr( $tt_f_id ); ?>-firstname" name="firstname" required autocomplete="given-name" style="<?php echo esc_attr( $tt_f_input ); ?>">
					</div>
					<div style="display: flex; flex-direction: column; gap: 6px;">
						<label for="tt-<?php echo esc_attr( $tt_f_id ); ?>-lastname" style="<?php echo esc_attr( $tt_f_label ); ?>">Last name</label>
						<input type="text" id="tt-<?php echo esc_attr( $tt_f_id ); ?>-lastname" name="lastname" autocomplete="family-name" style="<?php echo esc_attr( $tt_f_input ); ?>">
					</div>
				</div>
				<label for="tt-<?php echo esc_attr( $tt_f_id ); ?>-email" style="<?php echo esc_attr( $tt_f_label ); ?>">Work email *</label>
				<input type="email" id="tt-<?php echo esc_attr( $tt_f_id ); ?>-email" name="email" required autocomplete="email" style="<?php echo esc_attr( $tt_f_input ); ?>">

				<?php foreach ( $tt_form['fields'] as $tt_fld ) :
					$tt_fid = 'tt-' . $tt_f_id . '-' . preg_replace( '/[^a-z0-9\-]/', '', $tt_fld['name'] ); ?>
					<label for="<?php echo esc_attr( $tt_fid ); ?>" style="<?php echo esc_attr( $tt_f_label ); ?>"><?php echo esc_html( $tt_fld['label'] ); ?></label>
					<?php if ( 'select' === $tt_fld['type'] ) : ?>
						<select id="<?php echo esc_attr( $tt_fid ); ?>" name="<?php echo esc_attr( $tt_fld['name'] ); ?>" style="<?php echo esc_attr( $tt_f_input ); ?>">
							<option value="">Choose&hellip;</option>
							<?php foreach ( $tt_fld['options'] as $tt_opt ) : ?>
								<option value="<?php echo esc_attr( $tt_opt ); ?>"><?php echo esc_html( $tt_opt ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php else : ?>
						<input type="text" id="<?php echo esc_attr( $tt_fid ); ?>" name="<?php echo esc_attr( $tt_fld['name'] ); ?>" <?php echo isset( $tt_fld['placeholder'] ) ? 'placeholder="' . esc_attr( $tt_fld['placeholder'] ) . '"' : ''; ?> style="<?php echo esc_attr( $tt_f_input ); ?>">
					<?php endif; ?>
				<?php endforeach; ?>

				<label for="tt-<?php echo esc_attr( $tt_f_id ); ?>-message" style="<?php echo esc_attr( $tt_f_label ); ?>"><?php echo esc_html( $tt_form['message']['label'] ); ?> <span style="font-weight: 400; color: #5B6B68;"><?php echo esc_html( $tt_form['message']['hint'] ); ?></span></label>
				<textarea id="tt-<?php echo esc_attr( $tt_f_id ); ?>-message" name="message" rows="3" placeholder="<?php echo esc_attr( $tt_form['message']['placeholder'] ); ?>" style="<?php echo esc_attr( $tt_f_input ); ?> resize: vertical;"></textarea>

				<?php // Honeypot: hidden from real users, bots fill it and get silently dropped server-side. ?>
				<div style="position: absolute; left: -9999px;" aria-hidden="true"><label>Leave this blank<input type="text" name="tt_hp" tabindex="-1" autocomplete="off"></label></div>

				<label style="display: flex; gap: 8px; align-items: flex-start; font-size: 12.5px; line-height: 1.5; color: #5B6B68;">
					<input type="checkbox" name="consent" value="yes" required style="margin-top: 2px;">
					<span>I&rsquo;m happy for Treat Trunk to contact me about this enquiry. We won&rsquo;t add you to our newsletter - see our <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color: #12786C;">privacy policy</a>.</span>
				</label>
				<button type="submit" id="tt-<?php echo esc_attr( $tt_f_id ); ?>-submit" style="background: #12786C; color: #FAFAF8; border: none; font-weight: 700; font-size: 16px; padding: 14px 0; border-radius: 999px; cursor: pointer; font-family: inherit;"><?php echo esc_html( $tt_form['button'] ); ?></button>
				<p id="tt-<?php echo esc_attr( $tt_f_id ); ?>-status" role="status" style="font-size: 13.5px; line-height: 1.5; margin: 0; display: none;"></p>
			</form>
			<script id="tt-quote-<?php echo esc_attr( $tt_f_id ); ?>-js">
			(function () {
				var form = document.getElementById('tt-quote-<?php echo esc_js( $tt_f_id ); ?>');
				var btn = document.getElementById('tt-<?php echo esc_js( $tt_f_id ); ?>-submit');
				var status = document.getElementById('tt-<?php echo esc_js( $tt_f_id ); ?>-status');
				var label = <?php echo wp_json_encode( $tt_form['button'] ); ?>;
				function done( ok, msg ) {
					btn.disabled = false;
					btn.textContent = label;
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
								done( true, <?php echo wp_json_encode( $tt_form['success'] ); ?> );
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
