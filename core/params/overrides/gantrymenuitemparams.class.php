<?php
/**
 * @version   $Id: gantrymenuitemparams.class.php 58606 2012-12-12 18:10:19Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.gantryparams');

/**
 * @package    gantry
 * @subpackage core.params
 */
class GantryMenuItemParams extends GantryParams
{
	function populate()
	{
		global $gantry;

		if (!$gantry->_menu_item_overrides_merged && $gantry->currentMenuItem != null) {
			if (!empty($gantry->currentMenuTree)) {
				foreach ($gantry->currentMenuTree as $treeitem) {
					GantryMenuItemParams::_populateSingleItem($treeitem);
					if ($treeitem == $gantry->currentMenuItem) {
						break;
					}
				}
			} else {
				GantryMenuItemParams::_populateSingleItem($gantry->currentMenuItem);
			}
			$gantry->_menu_item_overrides_merged = true;
		}
	}

	function _populateSingleItem($itemId)
	{
		global $gantry;
		$ini_string = $gantry->readMenuItemParams($itemId);

		$menu_params = new JParameter($ini_string);

		foreach ($gantry->_preset_names as $param_name) {
			$menuitem_param_name = $param_name;
			if (in_array($param_name, $gantry->_setbymenuitem) && $menu_params->get($menuitem_param_name, null) != null) {
				$param                  =& $gantry->_working_params[$param_name];
				$menuitem_value         = $menu_params->get($menuitem_param_name);
				$menuitem_preset_params = $gantry->_getPresetParams($param['name'], $menuitem_value);
				foreach ($menuitem_preset_params as $menuitem_preset_param_name => $menuitem_preset_param_value) {
					if (!is_null($menuitem_preset_param_value)) {
						$gantry->_working_params[$menuitem_preset_param_name]['value'] = $menuitem_preset_param_value;
						$gantry->_working_params[$menuitem_preset_param_name]['setby'] = 'menuitem';
					}
				}
			}
		}
		// set individual values
		foreach ($gantry->_param_names as $param_name) {
			$menuitem_param_name = $param_name;
			if (in_array($param_name, $gantry->_setbymenuitem) && $menu_params->get($menuitem_param_name, null) != null) {
				$param          =& $gantry->_working_params[$param_name];
				$menuitem_value = $menu_params->get($menuitem_param_name);
				if (!is_null($menuitem_value)) {
					$gantry->_working_params[$param['name']]['value'] = $menuitem_value;
					$gantry->_working_params[$param['name']]['setby'] = 'menuitem';
				}
			}
		}

	}
}