<?php
/**
 * @version		1.26 September 14, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrygizmo');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoStyleDeclaration extends GantryGizmo {
    var $_name = 'styledeclaration';

    function isEnabled() {
        global $gantry;
        $menu_enabled = $this->get('enabled');

        if (1 == (int)$menu_enabled) return true;
        return false;
    }

	function query_parsed_init() {
        global $gantry;

        //inline css for dynamic stuff
        $css  = 'body {background:'.$gantry->get('bgcolor').';}';
        $css .= 'body a {color:'.$gantry->get('linkcolor').';}';
        $css .= '#rt-header .rt-container {background:'.$gantry->get('headercolor').';}';
        $css .= '#rt-bottom .rt-container {background:'.$gantry->get('bottomcolor').';}';
        $css .= '#rt-footer .rt-container, #rt-copyright .rt-container, #rt-menu .rt-container {background:'.$gantry->get('footercolor').';}';

        $gantry->addInlineStyle($css);

		// add inline css from the Custom CSS field
		$gantry->addInlineStyle($gantry->get('customcss'));

        //style stuff
        $gantry->addStyle($gantry->get('cssstyle').".css");
	}

}