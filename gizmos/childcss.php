<?php
/**
 * @version   $Id: childcss.php 58623 2012-12-15 22:01:32Z btowles $
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
class GantryGizmoChildCSS extends GantryGizmo
{

	var $_name = 'childcss';

	function isEnabled()
	{
		return true;
	}

	function finalize()
	{
		global $gantry;

		$gantry->addStyles(array(get_bloginfo('stylesheet_url')));

	}
}