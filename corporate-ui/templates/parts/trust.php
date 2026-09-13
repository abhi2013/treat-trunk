<?php
/**
 * Shared trust strip + testimonials (added 2026-09-13).
 *
 * Client names published with the owner's approval on 2026-09-13: Nissan and
 * SA Law (real bulk buyers from the order history), High Speed Training (the
 * original testimonial), the anonymised 800-employee letterbox campaign, and
 * the WeWork welcome-box partnership. "Sally" (previous owner, pre-2024) is
 * replaced with "the team" in the older quotes, also approved.
 *
 * $tt_quotes: which two of the three quotes to show, e.g. array( 'hst', '800' ).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tt_all_quotes = array(
	'800'  => array(
		'We ordered over 800 letterbox gifts to be sent to our staff with a mixture of tea and treats. The team were great and provided lots of options and costs to help us decide. It was a pleasure to deal with Treat Trunk and we would definitely use them again.',
		'An 800-employee letterbox campaign',
	),
	'hst'  => array(
		'An extremely good value range of high-quality snacks, catering to the many allergen and dietary requirements we had, sent with tracking information and amazing customer service. We couldn\'t have asked for more.',
		'Andy, High Speed Training Ltd',
	),
	'home' => array(
		'Everyone was delighted to receive their boxes and we\'ve had lots of positive feedback on the range of snacks included. The ordering process was really easy and the boxes were dispatched promptly. I would thoroughly recommend.',
		'Staff gift order, sent to home addresses',
	),
);
if ( empty( $tt_quotes ) ) {
	$tt_quotes = array( '800', 'hst' );
}
?>
<section class="tt-corp-section" style="background: #FAFAF8; padding: 48px 48px 56px;">
	<div style="max-width: 1240px; margin: 0 auto;">
		<h2 style="font-weight: 700; font-size: 30px; text-align: center; margin: 0 0 18px; color: #1B2420;">Trusted by teams big and small</h2>
		<p style="text-align: center; font-size: 15px; color: #0B5951; font-weight: 600; margin: 0 0 32px; line-height: 1.8;">Nissan &nbsp;&middot;&nbsp; SA Law &nbsp;&middot;&nbsp; High Speed Training &nbsp;&middot;&nbsp; WeWork member businesses &nbsp;&middot;&nbsp; 800+ box orders handled</p>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; max-width: 1000px; margin: 0 auto;">
			<?php foreach ( $tt_quotes as $tt_qk ) :
				if ( ! isset( $tt_all_quotes[ $tt_qk ] ) ) { continue; }
				$tt_q = $tt_all_quotes[ $tt_qk ]; ?>
				<figure style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 20px; padding: 26px; margin: 0; display: flex; flex-direction: column; gap: 14px;">
					<div style="color: #12786C; font-size: 16px;" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
					<blockquote style="margin: 0; font-size: 15px; line-height: 1.6; color: #1B2420; font-style: italic;">&ldquo;<?php echo esc_html( $tt_q[0] ); ?>&rdquo;</blockquote>
					<figcaption style="font-size: 14px; font-weight: 700; color: #12786C;"><?php echo esc_html( $tt_q[1] ); ?></figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
