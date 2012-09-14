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
 * @package   gantry
 * @subpackage core.params
 */
class GantryOverrideParams extends GantryParams {
    function populate() {
        global
        $gantry;

        foreach ($gantry->_override_tree as $override) {
            GantryOverrideParams::_populateSingleOverride($override->override_id);
        }
    }

    /**
     * Populates the working parameters with the options for an override
     *
     * @param  $override
     * @return void
     */
    function _populateSingleOverride($override) {
        global $gantry;
        $option = $gantry->templateName . '-template-options-override-' . $override;
        $unformatted_override_data = get_option($option);

        if (!$unformatted_override_data) {
            return;
        }
        $override_data = array();
        foreach ($unformatted_override_data as $option_name => $option_value) {
            GantryOverrideParams::_getFormattedParams($option_name, $option_value, $override_data);
        }

        if (empty($override_data)) {
            return;
        }

        foreach ($gantry->_preset_names as $param_name) {
            $override_param_name = $param_name;
            if (in_array($param_name, $gantry->_setbyoverride) && array_key_exists($override_param_name, $override_data)) {
                $param =& $gantry->_working_params[$param_name];
                $override_value = $override_data[$override_param_name];
                $override_preset_params = $gantry->_getPresetParams($param['name'], $override_value);
                foreach ($override_preset_params as $override_preset_param_name => $override_preset_param_value) {
                    if (!is_null($override_preset_param_value)) {
                        $gantry->_working_params[$override_preset_param_name]['value'] = $override_preset_param_value;
                        $gantry->_working_params[$override_preset_param_name]['setby'] = 'override';
                    }
                }
            }
        }
        // set individual values
        foreach ($gantry->_param_names as $param_name) {
            $override_param_name = $param_name;
            if (in_array($param_name, $gantry->_setbyoverride) && array_key_exists($override_param_name, $override_data)) {
                $param =& $gantry->_working_params[$param_name];
                $override_value = $override_data[$override_param_name];
                if (!is_null($override_value)) {
                    $gantry->_working_params[$param['name']]['value'] = $override_value;
                    $gantry->_working_params[$param['name']]['setby'] = 'override';
                }
            }
        }
    }
    
    /**
     * @param  $option_name
     * @param  $option_value
     * @param array $results
     * @return void
     */
    function _getFormattedParams($option_name, $option_value, &$results = array()) {
        if (!is_array($option_value)) {
            $results[$option_name] = $option_value;
        }
        else {
            foreach ($option_value as $sub_option_name => $sub_option_value) {
                GantryOverrideParams::_getFormattedParams($option_name . '-' . $sub_option_name, $sub_option_value, $results);
            }
        }
    }

}