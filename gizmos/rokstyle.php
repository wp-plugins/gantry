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
 
class GantryGizmoRokStyle extends GantryGizmo {

    var $_name = 'rokstyle';

    function query_parsed_init() {
    	add_shortcode('rokstyle', array('GantryGizmoRokStyle', 'rokstyle_init'));
	}
	
	function rokstyle_init($atts, $content = null) {
		global $gantry;
		extract(shortcode_atts(array(
			'type' => '',
		), $atts));

		if($type == 'css') :
			$gantry->addInlineStyle(trim($content));
		elseif ($type == 'js') :
			$gantry->addInlineScript(trim($content));
		endif;
	}
	
}