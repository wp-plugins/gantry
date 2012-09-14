<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantryparams');

/**
 * @package gantry
 * @subpackage core.params
 */
class GantryUrlParams  extends GantryParams {
    function populate(){
        global $gantry, $wp_query;        
        // get any url param overrides and set to that
        // set preset values
        foreach($gantry->_preset_names as $param_name) {
            if (in_array($param_name, $gantry->_setbyurl) && array_key_exists($param_name,$wp_query->query_vars)) {
                $param =& $gantry->_working_params[$param_name];
                $url_value = htmlentities(get_query_var($param['name']));
                $url_preset_params = $gantry->_getPresetParams($param['name'],$url_value);
                foreach($url_preset_params as $url_preset_param_name => $url_preset_param_value) {
                    if (!is_null($url_preset_param_value)){
                        $gantry->_working_params[$url_preset_param_name]['value'] = $url_preset_param_value;
                        $gantry->_working_params[$url_preset_param_name]['setby'] = 'url';
                    }
                }
            }
        }
        // set individual values
        foreach($gantry->_param_names as $param_name) {
            if (in_array($param_name, $gantry->_setbyurl) && array_key_exists($param_name,$wp_query->query_vars)) {
                $param =& $gantry->_working_params[$param_name];
                $url_value = htmlentities(get_query_var($param['name']));
                if (!empty($url_value)){
                    $gantry->_working_params[$param['name']]['value'] = $url_value;
                     $gantry->_working_params[$param['name']]['setby'] = 'url';
                }
            }
        }
    }
}