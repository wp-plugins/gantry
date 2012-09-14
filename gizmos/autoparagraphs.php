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
class GantryGizmoAutoParagraphs extends GantryGizmo {

    var $_name = 'autoparagraphs';

    function query_parsed_init() {
    	global $gantry;

		if($gantry->get('autoparagraphs-type') == 'content') :
			remove_filter('the_content', 'wpautop');
		elseif ($gantry->get('autoparagraphs-type') == 'excerpt') :
			remove_filter('the_excerpt', 'wpautop');
		else :
			remove_filter('the_content', 'wpautop');
			remove_filter('the_excerpt', 'wpautop');
		endif;

    }
}