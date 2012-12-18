<?php
/**
 * @version   $Id: file.php 58623 2012-12-15 22:01:32Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();
/**
 * @package     gantry
 * @subpackage  admin.elements
 */
gantry_import('core.config.gantryformfield');

class GantryFormFieldFile extends GantryFormField
{


	protected $type = 'html';
	protected $basetype = 'none';

	public function getInput()
	{
		global $gantry;
		$html = '';

		$filepath = $this->element['path'];
		$filepath = realpath($gantry->templatePath . $filepath);
		if ($filepath != false) {
			ob_start();
			include($filepath);
			$html = ob_get_clean();
		}
		return "<div class='html'>" . $html . "</div>";
	}

	public function getLabel()
	{
		return "";
	}
}