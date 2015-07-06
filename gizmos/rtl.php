<?php
/**
 * @version   $Id: rtl.php 61394 2015-07-04 09:48:11Z jakub $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


defined('GANTRY_VERSION') or die();

gantry_import('core.gantrygizmo');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoRTL extends GantryGizmo
{

	var $_name = 'rtl';

	function query_parsed_init()
	{
		/** @global $gantry Gantry */
		global $gantry;

		if (is_rtl() && $gantry->get('rtl-enabled')) {
			$gantry->addBodyClass("rtl");
			$gantry->addStyle("rtl.css");
		}
	}
}