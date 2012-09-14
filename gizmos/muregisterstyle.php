<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrygizmo');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoMURegisterStyle extends GantryGizmo {

    var $_name = 'muregisterstyle';

    function isEnabled(){
        return true;
    }


    function query_parsed_init() {
        global $gantry, $pagenow;

        if ($pagenow == 'wp-signup.php') {
            $css = '.mu_register {width: 900px;padding-bottom:10px;}';
            $gantry->addInlineStyle($css);
        }

        if ($pagenow == 'wp-activate.php') {
            $activation_css = '#content {width: 960px; margin: 0 auto;}'."\n";
            $activation_css .= '#content h2, #content form {margin-left: 10px; margin-right: 10px;}';
            $gantry->addInlineStyle($activation_css);
        }

    }
}