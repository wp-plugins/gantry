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
class GantryGizmoChildCSS extends GantryGizmo {

    var $_name = 'childcss';
    
    function isEnabled(){
        return true;
    }

    function finalize() {
		global $gantry;
		
		$gantry->addStyles(array(get_bloginfo('stylesheet_url')));

    }
}