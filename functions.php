<?php

// DEQUEUE StoreFront Core CSS
//add_action('wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999);
function sf_child_theme_dequeue_style() {
	wp_dequeue_style('storefront-style');
	wp_dequeue_style('storefront-woocommerce-style');
}


/************************************************
************* SCRIPTS AND STYLES ****************
*************************************************/
function so_scripts() {
	// FONTAWESOME
		wp_enqueue_script('sfchild-fontawesome', 'https://use.fontawesome.com/b1403a6995.js', array(), '20180111', true);
}
add_action('wp_enqueue_scripts', 'so_scripts');


/******************************************************
****** HOMEPAGE WC HOOKS - ACTIONS AND FILTERS ********
*******************************************************/

// remove product display in order to add the correct order @ HOMEPAGE
add_action('init', 'so_hompage_lists_swap');
function so_hompage_lists_swap() {
	remove_action('homepage', 'storefront_product_categories', 20);
	remove_action('homepage', 'storefront_featured_products', 40);
	remove_action('homepage', 'storefront_popular_products', 50);
	remove_action('homepage', 'storefront_on_sale_products', 60);
	remove_action('homepage', 'storefront_best_selling_products', 70);
	add_action('homepage', 'storefront_homepage_htmlsection', 40);
	add_action('homepage', 'storefront_product_categories', 50);
	add_action('homepage', 'storefront_best_selling_products', 60);
}

// ADD NEW HTML CONTENT TO THE FRONTEND
if (!function_exists('storefront_homepage_htmlsection')) {
	function storefront_homepage_htmlsection() {
		$args = array(
			'post_type'				=> 'post',
			'name'						=> 'como-funciona',
			'posts_per_page'	=> 1,
		);
		$query = new WP_Query($args);
		while ($query->have_posts()) {
			$query->the_post();
			get_template_part('content', 'fpsection');
		}
		wp_reset_postdata();
	}
}

/*while (have_posts()) {
	the_post();
	get_template_part('content', 'homepage');
}*/


/******************************************************
****** GENERAL WC HOOKS - ACTIONS AND FILTERS *********
*******************************************************/

// disable woocommerce 3.+ default lightbox
function remove_wc_gallery_lightbox() {
	remove_theme_support('wc-product-gallery-lightbox');
	remove_theme_support('wc-product-gallery-zoom');
}
add_action('after_setup_theme', 'remove_wc_gallery_lightbox', 100);

// disable single product featured image link
function custom_single_product_image_html($html, $post_id) {
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
  return get_the_post_thumbnail($post_thumbnail_id, apply_filters('single_product_large_thumbnail_size', 'shop_single'));
}
add_filter('woocommerce_single_product_image_thumbnail_html', 'custom_single_product_image_html', 10, 2);

// Reorder tabs

add_filter('woocommerce_product_tabs', 'so_reorder_tabs', 98);
function so_reorder_tabs($tabs) {
	$tabs['description']['priority'] = 10;
	$tabs['test_tab']['priority'] = 15;
	$tabs['reviews']['priority'] = 30;

	return $tabs;
}

// Add new tabs

add_filter('woocommerce_product_tabs', 'so_new_product_tab');
function so_new_product_tab( $tabs ) {
	// Adds the new tab
	$tabs['test_tab'] = array(
		'title' 	=> __('Información Adicional', 'woocommerce'),
		'priority' 	=> 50,
		'callback' 	=> 'so_new_product_tab_content'
	);
	return $tabs;
}
function so_new_product_tab_content() {
	// The new tab content
	echo '<h2>' . esc_html_e('Información Adicional', 'softwareone') . '</h2>';
	echo '<p>Here\'s your new product tab.</p>';
}

// REMOVE FOOTER CREDIT INFO
add_action('init', 'custom_remove_footer_credit', 10);
function custom_remove_footer_credit() {
	remove_action('storefront_footer', 'storefront_credit', 20);
	add_action('storefront_footer', 'custom_store_front_credit', 20);
}
function custom_store_front_credit() {
	?>
	<div class="site-info">
		<?php echo esc_html(apply_filters('storefront_copyright_text', $content = '&copy; ' . get_bloginfo('name') . ' ' . date( 'Y'))); ?>
			<?php if (apply_filters('storefront_credit_link', true)) { ?>
			<br /><span class="footer-credit-pd"><?php printf(esc_attr__('%1$s developed by %2$s', 'storefront'), '', '<a href="http://www.pleiadesdigital.com" class="footer-credit-link" target="_blank" title="Pleiades Digital - Website Development and Ecommerce Agency">Pleiades Digital</a>' ); ?></span>
			<?php } ?>
	</div><!-- class="site-info" -->
	<?php
}

/* HEADER */
// Remove search form from Header
add_action('init', 'so_remove_storefront_header_search');
function so_remove_storefront_header_search() {
	remove_action('storefront_header', 'storefront_product_search', 40);
}
