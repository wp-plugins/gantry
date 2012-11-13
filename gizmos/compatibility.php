<?php
/**
 * @version   1.28 November 13, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrygizmo');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoCompatibility extends GantryGizmo {

	var $_name = 'compatibility';

	function isEnabled() {
		return true;
	}

	/**
		* 	Copyright (C) 2012 Hassan Derakhshandeh & Jakub Baran
		*  	Contains parts of code from the WooCommerce plugin by WooThemes
	 */

	function init() {
		global $gantry;

		/**
		 * 	WooCommerce Compatibility
		 */

		if(defined('WOOCOMMERCE_VERSION')) {
			// Set the number of the items on the WooCommerce page to the Blog post count
			if($gantry->get('blog-count') != '') {
				$shop_items_count = $gantry->get('blog-count');
			} else {
				$shop_items_count = get_option('posts_per_page');
			}

			add_filter('loop_shop_per_page', create_function('$cols', "return $shop_items_count;"));
			add_action('wp_enqueue_scripts', array(&$this, 'wc_cart_variation_script'));
			remove_filter('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
		}

		/**
		 *	WP E-Commerce Compatibility
		 */
		
		if(defined('WPSC_VERSION')) {
			add_action('init', array(&$this, 'wpse_filter_template_parts'), 20);
		}
	}

	function query_parsed_init() {
		global $gantry;
	}

	function wpse_filter_template_parts() {
		/**
		 *	WP E-Commerce Compatibility
		 */
		
		foreach(wpsc_get_theme_files() as $template) {
			add_filter(WPEC_TRANSIENT_THEME_PATH_PREFIX . $template, array(&$this, 'wpsc_template_part'));
		}
	}

	function wpsc_template_part($tmpl) {
		/**
		 *	WP E-Commerce Compatibility
		 */
		
		$file = basename($tmpl);
		if(file_exists(trailingslashit(get_template_directory()) . $file)) {
			return trailingslashit(get_template_directory()) . $file;
		}
		return $tmpl;
	}

	function wc_cart_variation_script() {
		global $gantry, $woocommerce;

		/**
		 * 	WooCommerce Compatibility
		 */
		
		if(defined('WOOCOMMERCE_VERSION') && is_woocommerce()) {
			if(is_single() && get_post_type() == 'product') {
				wp_enqueue_script('wc-add-to-cart-variation', $woocommerce->plugin_url() . '/assets/js/frontend/add-to-cart-variation.js', array( 'jquery' ), '1.6', true);
			}
		}
	}
}