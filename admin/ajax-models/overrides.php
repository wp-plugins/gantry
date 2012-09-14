<?php
/**
 * @version		1.26 September 14, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();
gantry_import('core.gantryjson');
gantry_import('core.utilities.gantrytemplateinfo');

global $gantry;

$action = $_POST['gantry_action'];
if (!current_user_can('edit_theme_options')) die('-1');

if ($action == 'get_base_values') {
    $passed_array  = array();
    foreach($gantry->_working_params as $param){
        $param_name = GantryTemplateInfo::get_field_id($param['name'],'template-options');
        $passed_array[$param_name]=$param['value'];
    }
    $outdata = GantryJSON::encode($passed_array);
    //$outdata = str_replace('\\\\\\' , '\\', $outdata);
	echo $outdata;
} else {
	return "error";
}
