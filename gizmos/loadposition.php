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
class GantryGizmoLoadPosition extends GantryGizmo {

    var $_name = 'loadposition';
    
    function query_parsed_init() {
    	add_shortcode('loadposition', array('GantryGizmoLoadPosition', 'rok_loadposition'));
	}
	
	function rok_loadposition($atts, $content = null) {
		global $gantry;	
			
		extract(shortcode_atts(array('id' => ''), $atts));
		
		if ($gantry->countModules($id)) :
			return $gantry->displayModules($id,'basic','basic');
		endif;
		
	}
	
}