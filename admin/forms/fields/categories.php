<?php
/**
 * @version		1.29 December 11, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformfield');
gantry_import('core.config.gantryhtmlselect');
require_once('list.php');

class GantryFormFieldCategories extends GantryFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'categories';
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

		$cats = get_terms('category');
        
        foreach ( $cats as $cat ) {
			// Create a new option object based on the <option /> element.
			$tmp = GantryHtmlSelect::option($cat->slug, $cat->name, 'value', 'text', false);
			$options[] = $tmp;
		}
        return $options;
    }
}