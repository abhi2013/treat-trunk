<?php
/**
 * Shared FAQ accordion (added 2026-09-13). Renders $tt_faqs (array of
 * array( question, answer )) under $tt_faq_heading. The JSON-LD FAQPage for
 * the same array is emitted by parts/schema.php from the same variable, so
 * visible content and markup can never drift apart.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section id="faqs" class="tt-corp-section" style="padding: 24px 48px 56px; max-width: 800px; margin: 0 auto;">
	<h2 style="font-weight: 700; font-size: 32px; text-align: center; margin: 0 0 28px; color: #1B2420;"><?php echo esc_html( $tt_faq_heading ); ?></h2>
	<div style="display: flex; flex-direction: column; gap: 12px;">
		<?php foreach ( $tt_faqs as $tt_qa ) : ?>
			<details style="background: #FFFFFF; border: 1px solid #DCEBE9; border-radius: 14px; padding: 16px 20px;">
				<summary style="font-weight: 700; font-size: 16px; color: #1B2420; cursor: pointer;"><h3 style="display: inline; font-weight: 700; font-size: 16px; margin: 0; color: #1B2420;"><?php echo esc_html( $tt_qa[0] ); ?></h3></summary>
				<p style="font-size: 14.5px; line-height: 1.6; color: #1B2420; margin: 10px 0 0;"><?php echo esc_html( $tt_qa[1] ); ?></p>
			</details>
		<?php endforeach; ?>
	</div>
</section>
