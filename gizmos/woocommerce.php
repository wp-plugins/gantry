<?php
/**
 * @version   1.26 September 14, 2012
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
class GantryGizmoWooCommerce extends GantryGizmo {

	var $_name = 'woocommerce';

	function isEnabled() {
		if(defined('WOOCOMMERCE_VERSION'))
			return true;

		return false;
	}

	/**
			* 	Copyright (C) 2012 Hassan Derakhshandeh & Jakub Baran
			*   Contains parts of code from the WooCommerce plugin by WooThemes
	 */

	function init() {
		global $gantry;

		if($gantry->get('blog-count') != '') {
			$shop_items_count = $gantry->get('blog-count');
		} else {
			$shop_items_count = get_option('posts_per_page');
		}

		add_filter('loop_shop_per_page', create_function('$cols', "return $shop_items_count;"));
	}

	function query_parsed_init() {
		global $gantry, $woocommerce;
		if(is_woocommerce()) {
			remove_filter('template_include', array($woocommerce, 'template_loader'));
			add_filter('gantry_mainbody_include', array('GantryGizmoWooCommerce', 'include_woocommerce_template'));
		}
	}

	function include_woocommerce_template($tmpl) {
		global $gantry;

		$find = array('woocommerce.php');
		$file = '';

		if (is_single() && get_post_type() == 'product') {

			$file 	= 'single-product.php';
			$find[] = $file;

		} elseif (is_tax('product_cat') || is_tax('product_tag')) {

			$term = get_queried_object();

			$file 		= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] 	= 'taxonomy-' . $term->taxonomy . '.php';
			$find[] 	= $file;

		} elseif (is_post_type_archive('product') || is_page(woocommerce_get_page_id('shop'))) {

			$file 	= 'archive-product.php';
			$find[] = $file;

		}

		$find = array_reverse($find);

		$template = GantryBodyLayout::locate_type($find);
		if ($template && $template != '') {
			return $template;
		} else {
			woocommerce_content();
			return '';
		}

		return $tmpl;
	}
}