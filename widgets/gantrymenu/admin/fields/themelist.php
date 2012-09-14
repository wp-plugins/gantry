<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

defined('GANTRY_VERSION') or die;

global $gantry;
gantry_import('core.config.gantryformfield');
gantry_import('core.config.gantryhtmlselect');

require_once($gantry->gantryPath.'/admin/forms/fields/list.php');



class GantryFormFieldThemeList extends GantryFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'themelist';
    protected $basetype = 'select';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
    protected function getOptions() {
        global $gantry;
        $options = array();
        $options = parent::getOptions();

        foreach ( GantryWidgetMenu::$themes as $theme ) {
			// Create a new option object based on the <option /> element.
			$tmp = GantryHtmlSelect::option($theme['name'], $theme['fullname'], 'value', 'text', false);
			$options[] = $tmp;
		}
        return $options;
    }
}