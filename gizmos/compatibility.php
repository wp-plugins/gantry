<?php
/**
 * @version   $Id: compatibility.php 58623 2012-12-15 22:01:32Z btowles $
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
class GantryGizmoCompatibility extends GantryGizmo
{

	var $_name = 'compatibility';

	function isEnabled()
	{
		return true;
	}

	/**
	 *     Copyright (C) 2012 Hassan Derakhshandeh & Jakub Baran
	 *      Contains parts of code from the WooCommerce plugin by WooThemes
	 */

	function init()
	{
		global $gantry;

		/**
		 *     WooCommerce Compatibility
		 */

		if (defined('WOOCOMMERCE_VERSION')) {
			// Set the number of the items on the WooCommerce page to the Blog post count
			if ($gantry->get('blog-count') != '') {
				$shop_items_count = $gantry->get('blog-count');
			} else {
				$shop_items_count = get_option('posts_per_page');
			}

			add_filter('loop_shop_per_page', create_function('$cols', "return $shop_items_count;"));
			add_action('wp_enqueue_scripts', array(&$this, 'wc_cart_variation_script'));
			remove_filter('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
		}

		/**
		 *    WP E-Commerce Compatibility
		 */

		if (defined('WPSC_VERSION')) {
			add_action('init', array(&$this, 'wpsc_filter_template_parts'), 20);
		}

		/**
		 * Jigoshop Compatibility
		 */

		remove_action('jigoshop_sidebar', 'jigoshop_get_sidebar', 10);

	}

	function query_parsed_init()
	{
		global $gantry;

		/**
		 * BBPress Compatibility
		 */

		if (function_exists('bbpress') && is_bbpress()) {
			add_filter('gantry_mainbody_include', array(&$this, 'bb_fix_archive_page'));
		}
	}

	/**
	 *    WP E-Commerce  - Ability to override plugin theme files
	 */

	function wpsc_filter_template_parts()
	{
		foreach (wpsc_get_theme_files() as $template) {
			add_filter(WPEC_TRANSIENT_THEME_PATH_PREFIX . $template, array(&$this, 'wpsc_template_part'));
		}
	}

	/**
	 *    WP E-Commerce  - Ability to override plugin theme files
	 */

	function wpsc_template_part($tmpl)
	{
		$file = basename($tmpl);
		if (file_exists(trailingslashit(get_template_directory()) . $file)) {
			return trailingslashit(get_template_directory()) . $file;
		}
		return $tmpl;
	}

	/**
	 *     WooCommerce - Fix for Add-To-Cart Variations
	 */

	function wc_cart_variation_script()
	{
		global $gantry, $woocommerce;

		if (defined('WOOCOMMERCE_VERSION') && is_woocommerce()) {
			if (is_single() && get_post_type() == 'product') {
				wp_enqueue_script('wc-add-to-cart-variation', $woocommerce->plugin_url() . '/assets/js/frontend/add-to-cart-variation.js', array('jquery'), '1.6', true);
			}
		}
	}

	/**
	 * BBPress - Fix for the Forum archive post type
	 */

	function bb_fix_archive_page($tmpl)
	{
		if (is_post_type_archive('forum')) {
			foreach (array('archive-forum.php', 'page.php') as $template) {
				if (file_exists(get_template_directory() . '/html/' . $template)) return get_template_directory() . '/html/' . $template;
			}
		}
		return $tmpl;
	}
}