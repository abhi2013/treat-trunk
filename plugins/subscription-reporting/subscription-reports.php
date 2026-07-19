<?php
/**
 *	Plugin Name: 	Subscription Custom Reports
 *	Plugin URI:		https://treattrunk.co.uk/
 *	Description:	Add Custom Reporting for all subscription products to see current active subscriptions
 *	Version:		0.1.0
 *	Author:			Treat Trunk
 *	Author URI:		https://treattrunk.co.uk/
 *	Text Domain:	subscription-reporting
 */
 
function subscription_reports_setup_menu() {
	add_menu_page('Subscription Reports', 'Subscription Reports', 'manage_options', 'subscription-reports', 'subscription_reports', 'dashicons-chart-pie');
}
add_action('admin_menu', 'subscription_reports_setup_menu');

function subscription_reports() {
	echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">';
	echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.css" integrity="sha512-C7hOmCgGzihKXzyPU/z4nv97W0d9bv4ALuuEbSf6hm93myico9qa0hv4dODThvCsqQUmKmLcJmlpRmCaApr83g==" crossorigin="anonymous" />';
	echo '<style>body {background-color:#e1e1e1;} .card {border-color:#b9b9b9;}</style>';
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div class="col text-center">';
	echo '<h1>Subscription Reports</h1>';
	echo '</div>';
	echo '</div>';
	echo '<div class="row justify-content-center">';
	echo '<div class="col-12 col-md-5">';
	echo '<div class="card text-center pt-0 px-0">';
	echo '<div class="card-header">Total Active Subscriptions</div>';
	echo '<div class="card-body">';
	echo '<h2 class="card-title">'.get_total_active_subscriptions().'</h2>';
	echo '<canvas id="myChart" width="600" height"800"></canvas>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	$product_ids = get_subscription_product_ids('snack-box-subscription');
	$arrTitles = array();
	$arrTotals = array();
	
	
	echo '<div class="row row-cols-1 row-cols-md-3 g-4">';
	foreach($product_ids as $pid) {
		array_push($arrTitles, get_the_title($pid));
		$total = get_orders_ids_from_product_ids(array($pid));
		array_push($arrTotals, count($total));
		echo '<div class="col">';
		echo '<div class="card text-center pt-0 px-0">';
		echo '<div class="card-header">'.get_the_title($pid).'</div>';
		echo '<div class="card-body">';
		echo '<h2 class="card-title">'.count($total).'</h2>';
		$variation_ids = get_variations_for_product($pid);
		foreach($variation_ids as $index=>$v) {
			echo '<div class="row d-flex align-items-center py-2 mx-2';
			echo $index%2==0?' bg-light':'';
			echo '">';
			echo '<div class="col text-start"><strong>'.get_the_excerpt($v).'</strong></div>';
			echo '<div class="col text-end">'.count(get_orders_for_variation($v)).'</div>';
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';
	echo '</div>';
	echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>';
	echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>';
	echo '<script>
	var ctx = document.getElementById("myChart");
	data = {
	    datasets: [{
		    data: ['.implode(',',$arrTotals).'],
		    backgroundColor: ["rgba(15, 98, 119, 1)","rgba(15, 98, 119, .6)","rgba(23, 162, 153, 1)","rgba(23, 162, 153, .7)","rgba(23, 162, 153, .4)"],
	    }],
	    labels: ["'.implode('","',$arrTitles).'"],
    };
    options = {
	    cutoutPercentage: 50,
	    legend: {
	    	position: "bottom"
    	}
    };
	var myDoughnutChart = new Chart(ctx, {
	    type: "doughnut",
	    data: data,
	    options: options
	});
	</script>';
}

function get_subscription_product_ids($slug) {
	$args = array(
		'post_type'			=> 'product',
		'posts_per_page'	=> -1,
		'tax_query'	=> array(
			array(
				'taxonomy'	=> 'product_cat',
				'field'		=> 'slug',
				'terms'		=> $slug
			)
		),
		'orderby'			=> 'title'
	);
	$the_query = new WP_Query($args);
	$product_ids = array();
	if($the_query->have_posts()) {
		while($the_query->have_posts()) {
			$the_query->the_post();
			array_push($product_ids, get_the_id());
		}
	}
	wp_reset_postdata();
	return $product_ids;
}

function get_total_active_subscriptions() {
	$customer_subscriptions = get_posts(array(
		'numberposts'	=> -1,
		'post_type'		=> 'shop_subscription',
		'post_status'	=> 'wc-active'
	));
	return count($customer_subscriptions);
}

function get_orders_ids_from_product_ids( $product_ids )
{
    global $wpdb;

    $product_ids = implode( ',', $product_ids );

    $orders_statuses = "'wc-active'";

    # Requesting All defined statuses Orders IDs for a defined product ID
    $orders_ids = $wpdb->get_col( "
        SELECT DISTINCT woi.order_id
        FROM {$wpdb->prefix}woocommerce_order_items as woi
        INNER JOIN {$wpdb->prefix}posts AS p ON woi.order_id = p.ID
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id
        WHERE p.post_status IN ( $orders_statuses )
        AND p.post_type = 'shop_subscription'
        AND woim.meta_key LIKE '_product_id'
        AND woi.order_item_type = 'line_item'
        AND woim.meta_value IN ($product_ids)
        ORDER BY woi.order_item_id DESC
    ");

    // Return an array of Orders IDs for the given Product IDs
    return $orders_ids;
}

function get_orders_for_variation($variation_id) {
	global $wpdb;
	
	$order_ids = $wpdb->get_col("
		SELECT DISTINCT woi.order_id
        FROM {$wpdb->prefix}woocommerce_order_items as woi
        INNER JOIN {$wpdb->prefix}posts AS p ON woi.order_id = p.ID
        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id
        WHERE p.post_status = 'wc-active'
        AND p.post_type = 'shop_subscription'
        AND woim.meta_key LIKE '_variation_id'
        AND woi.order_item_type = 'line_item'
        AND woim.meta_value = ($variation_id)
        ORDER BY woi.order_item_id DESC
	");
	return $order_ids;
}

function get_variations_for_product($product_id) {
	$args = array(
		'post_type' 	=> 'product_variation',
		'post_status'	=> 'publish',
		'numberposts'	=> -1,
		'post_parent'	=> $product_id
	);
	$variations = get_posts($args);
	$variation_ids = array();
	foreach($variations as $v) {
		array_push($variation_ids, $v->ID);
	}
	return $variation_ids;
}

