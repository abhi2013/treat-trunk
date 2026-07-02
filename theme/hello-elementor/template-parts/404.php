<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<main class="site-main" role="main">
	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<header class="page-header 404">
			<h1 class="entry-title"><?php esc_html_e( 'The page can&rsquo;t be found.', 'hello-elementor' ); ?></h1>
		</header>
	<?php endif; ?>
	<div class="elementor-section elementor-section-boxed">
		<div class="elementor-container">
			<?php
			if ( function_exists('yoast_breadcrumb') ) {
			  yoast_breadcrumb( '<p style="margin-top:8px;" id="breadcrumbs">','</p>' );
			}
			?>
		</div>
	</div>
	<div class="page-content">
		<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'hello-elementor' ); ?></p>
	</div>

</main>
