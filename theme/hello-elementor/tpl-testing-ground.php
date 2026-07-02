<?php
/**
 * Template Name: Testing Ground
 * Template Post Type: post, page
 */

?>
Welcome to the testing ground.

<?php
$args = array(
	'post_type' 	=> 'shop_subscription',
);
$the_query = new WP_Query($args);
echo $the_query->found_posts;
wp_reset_postdata();
?>