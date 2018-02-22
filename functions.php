<?php

// DEQUEUE StoreFront Core CSS
//add_action('wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999);
function sf_child_theme_dequeue_style() {
	wp_dequeue_style('storefront-style');
	wp_dequeue_style('storefront-woocommerce-style');
}

/************************************************
********************* NAV MENUS ****************
*************************************************/

function softwareone_setup() {
	register_nav_menus(array(
		'social_menu'		=> __('Social Menu', 'softwareone'),
	));
}
add_action('after_setup_theme', 'softwareone_setup');


/************************************************
************* SCRIPTS AND STYLES ****************
*************************************************/
function so_scripts() {
	// FONTAWESOME
	wp_enqueue_script('sfchild-fontawesome', 'https://use.fontawesome.com/b1403a6995.js', array(), '20180111', true);
	// CUSTOM JAVASCRIPT (app.js)
	wp_enqueue_script('softwareone-app', get_theme_file_uri('/assets/js/app.js'), array('jquery'), '20180109', false);
}
add_action('wp_enqueue_scripts', 'so_scripts');


/******************************************************
****** HOMEPAGE WC HOOKS - ACTIONS AND FILTERS ********
*******************************************************/

// remove product display in order to add the correct order @ HOMEPAGE
add_action('init', 'so_homepage_lists_swap');
function so_homepage_lists_swap() {
	remove_action('homepage', 'storefront_product_categories', 20);
	remove_action('homepage', 'storefront_featured_products', 40);
	remove_action('homepage', 'storefront_popular_products', 50);
	remove_action('homepage', 'storefront_on_sale_products', 60);
	remove_action('homepage', 'storefront_best_selling_products', 70);
	add_action('homepage', 'storefront_homepage_htmlsection', 40);
	add_action('homepage', 'storefront_product_categories', 50);
	add_action('homepage', 'storefront_best_selling_products', 60);
}

function storefront_product_categories($args) {
	if (storefront_is_woocommerce_activated()) {
		$args = apply_filters('storefront_product_categories_args', array(
			// 'limit' 			=> -1,
			// 'columns' 			=> 5,
			'child_categories' 	=> 0,
			// 'order'					=> 'ASC',
			'orderby' 			=> 'rand',
			'title'				=> __('BUSCAR POR CATEGORÍA', 'softwareone'),
		));
		$shortcode_content = storefront_do_shortcode('product_categories', apply_filters('storefront_product_categories_shortcode_args', array(
			// 'number'  => intval($args['limit']),
			// 'columns' => intval($args['columns']),
			// 'order'		=> esc_attr($args['order']),
			'orderby' => esc_attr($args['orderby']),
			'parent'  => esc_attr($args['child_categories']),
		)));
		// Only display the section if the shortcode returns product categories
		if (false !== strpos($shortcode_content, 'product-category')) { ?>
			<section class="<?php if (is_page_template('template-homepage.php')) { echo 'col-full'; } ?> storefront-product-section storefront-product-categories" aria-label="<?php esc_attr__('Product Categories', 'storefront'); ?>">
			<?php
			do_action('storefront_homepage_before_product_categories');
			echo '<h2 class="section-title">' . wp_kses_post($args['title']) . '</h2>';
			do_action('storefront_homepage_after_product_categories_title');
			//echo $shortcode_content;
			echo do_shortcode('[wpb-woo-category-slider content_type="with_icon" items="4" autoplay="false" exclude="27" need_description="on" pagination="false" loop="false"]');
			do_action('storefront_homepage_after_product_categories');
			echo '</section>';
		}
	}
}

function storefront_recent_products($args) {
	if (storefront_is_woocommerce_activated()) {
		$args = apply_filters( 'storefront_recent_products_args', array(
			'limit' 			=> 4,
			'columns' 			=> 4,
			'title'				=> __('CURSOS NUEVOS', 'storefront'),
		));
		$shortcode_content = storefront_do_shortcode( 'recent_products', apply_filters( 'storefront_recent_products_shortcode_args', array(
			'per_page' => intval( $args['limit'] ),
			'columns'  => intval( $args['columns'] ),
		)));
		// Only display the section if the shortcode returns products
		if (false !== strpos($shortcode_content, 'product')) { ?>
			<section class="<?php if (is_page_template('template-homepage.php')) { echo 'col-full'; } ?> storefront-product-section storefront-product-categories" aria-label="<?php esc_attr__('New Courses', 'storefront'); ?>">
			<?php
			do_action('storefront_homepage_before_recent_products');
			echo '<h2 class="section-title">' . wp_kses_post($args['title']) . '</h2>';
			do_action('storefront_homepage_after_recent_products_title');
			echo $shortcode_content;
			do_action('storefront_homepage_after_recent_products');
			echo '</section>';
		}
	}
}

function storefront_best_selling_products($args) {
	if (storefront_is_woocommerce_activated()) {
		$args = apply_filters('storefront_best_selling_products_args', array(
			'limit'   => 4,
			'columns' => 4,
			'title'	  => esc_attr__('LOS MÁS VENDIDOS', 'storefront'),
		));
		$shortcode_content = storefront_do_shortcode('best_selling_products', apply_filters( 'storefront_best_selling_products_shortcode_args', array(
			'per_page' => intval( $args['limit']),
			'columns'  => intval( $args['columns']),
		)));

		/**
		 * Only display the section if the shortcode returns products
		 */
		if (false !== strpos($shortcode_content, 'product')) { ?>

			<section class="<?php if (is_page_template('template-homepage.php')) { echo 'col-full'; } ?> storefront-product-section storefront-product-categories" aria-label="<?php esc_attr__('Product Categories', 'storefront'); ?>">

			<?php
			do_action('storefront_homepage_before_best_selling_products');

			echo '<h2 class="section-title">' . wp_kses_post( $args['title']) . '</h2>';

			do_action('storefront_homepage_after_best_selling_products_title');

			echo $shortcode_content;

			do_action('storefront_homepage_after_best_selling_products');

			echo '</section>';

		}
	}
}

// MODIFY FRONT PAGE HEADER CONTENT
add_action('init', 'so_homepage_header_content');
function so_homepage_header_content() {
	remove_action('storefront_homepage', 'storefront_homepage_header', 10);
	//remove_action('storefront_homepage', 'storefront_page_content', 20);
	//add_action('storefront_homepage', 'custom_storefront_page_content', 10)
}

// ADD NEW HTML CONTENT TO THE HEADER

add_action('init', 'so_header_blocks');
function so_header_blocks() {
	add_action('storefront_header', 'so_header_login_logout', 35);
}

// ADD USERNAME LOGGED IN TO HEADER
if (!function_exists('so_header_login_logout')) {
	function so_header_login_logout() {
		if (is_user_logged_in()) {
			$user = wp_get_current_user();
			echo "<div class='header-login-logout'>";
			echo "<span class='logged-in'>Hola: <b>" . $user->display_name . "</b>!</span>";
			echo "<span class='logout-link'><a href='" . get_permalink(wc_get_page_id('myaccount')) . "'>Salir</a></span>";
			echo "</div>";
		} elseif (!is_user_logged_in()) {
			echo "<div class='header-login-logout logout'>";
			echo "<span class='login-link'><a href='" . get_permalink(wc_get_page_id('myaccount')) . "'>Ingresar</a></span>";
			echo "</div>";
		}
	}
}
// REDIRECT TO HOME
add_filter('woocommerce_login_redirect', 'login_redirect');
function login_redirect() {
	return home_url();
}

add_action('wp_logout', 'logout_redirect');
function logout_redirect() {
	wp_redirect(home_url());
	exit;
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
	echo '<h2>Información Adicional</h2>';
	//echo get_post_meta(get_the_ID(), 'prerequisitos', false);
}

// REMOVE FOOTER CREDIT INFO
add_action('init', 'custom_remove_footer_credit', 10);
function custom_remove_footer_credit() {
	remove_action('storefront_footer', 'storefront_footer_widgets', 10);
	add_action('storefront_footer', 'storefront_footer_widgets', 30);
	remove_action('storefront_footer', 'storefront_credit', 20);
	add_action('storefront_footer', 'custom_store_front_credit', 20);
}
function custom_store_front_credit() {
	?>
	<div class="site-info">
		<div class="footer-logo"><img src="http://localhost/~ronyortiz/sites2017/softwareone/wp-content/themes/softwareone/assets/images/logo-softwareone-transparent-white.png"></div>
		<div class="footer-moto"><h4>Siga a SoftwareONE para conocer las últimas tecnologías, eventos y promociones</h4></div>
			<?php
				wp_nav_menu(array(
					'theme_location'		=> 'social_menu',
					'container'					=> 'nav',
					'container_id'			=> 'footer-social',
					'container_class'		=> 'footer-social',
					'menu_id'						=> 'menu-social-items',
					'menu_class'				=> 'menu-items',
					'depth'							=> 1,
					'link_before'				=> '<span class="screen-reader-text">',
					'link_after'				=> '</span>',
					'fallback_cb'				=> '',
				));
			?>


	</div><!-- class="site-info" -->
	<?php
}

// callback function for SOCIAL MENU


/* HEADER */
// Remove search form from Header
add_action('init', 'so_remove_storefront_header_search');
function so_remove_storefront_header_search() {
	remove_action('storefront_header', 'storefront_product_search', 40);
}


/* MODIFY CHECKOUT FIELDS */

// remove phone field
add_filter('woocommerce_checkout_fields', 'storepd_checkout_fields', 20);
function storepd_checkout_fields($fields) {
	unset($fields['billing']['billing_phone']);
	// make email field full width
	$fields['billing']['billing_email']['class'] = array('form-row-wide');
	return $fields;
}

// add 'how did you hear about us' field
add_filter('woocommerce_checkout_fields', 'softwareone_feedback', 30);
function softwareone_feedback($fields) {
	$fields['order']['hear_about_us'] = array(
		'type' 					=> 'select',
		'class'					=> array('form-row-wide'),
		'label'					=> '¿Cómo escuchaste de nosotros?',
		'options'				=> array(
			'default'			=> '-- selecciona una opción --',
			'tv'					=> 'TV',
			'radio'				=> 'Radio',
			'internet'		=> 'Internet',
			'billboard'		=> 'Billboard'
		),
	);
	return $fields;
}

/******************************************************
********** MODIFY TABS INFO WITH FILTER  **************
*******************************************************/

//add_filter('woocommerce_product_tabs', 'softwareone_product_tabs', 100);

function softwareone_product_tabs() {
	/*$tabs['reviews']['title'] = __("Success Stories");
	return $tabs;*/
}


/******************************************************
********** MODIFY DROP DOWN FILTERS  **************
*******************************************************/
add_filter('woocommerce_catalog_orderby', 'softwareone_catalog_orderby', 20);
function softwareone_catalog_orderby($orderby) {
	unset($orderby['rating']);
	// $orderby['date'] = __('Ordenar por fecha: Nuevos primero', 'woocommerce');
	// $orderby['oldest_to_recent'] = __('Ordenar por fecha: Antigüos primero', 'woocommerce');
	return $orderby;
}

// create query to add oldest-to-newest list
//add_filter('woocommerce_get_catalog_ordering_args', 'softwareone_get_catalog_ordering_args', 15);
function softwareone_get_catalog_ordering_args () {
/*	$orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));
	if ('oldest_to_recent' == $orderby_value) {
		$args['orderby'] = 'date';
		$args['order'] = 'ASC';
	}
	return $args;*/
}

// ADD FILTER DROWDOWN TO _before_shop_loop
add_action('init', 'so_filter_list');
function so_filter_list() {
	add_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 30);
	add_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 30);

}



