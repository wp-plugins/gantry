<?php
/**
 * @version   1.26 September 14, 2012
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

class GantryFormFieldGANTRY extends GantryFormField {
    
	protected $type = 'gantry';
    protected $basetype = 'none';

	public function getInput(){
		global $gantry;
		
		if (!defined('GANTRY_CSS')) {
			$gantry->addStyle($gantry->gantryUrl.'/admin/widgets/gantry.css');
			define('GANTRY_CSS', 1);
		}
		
		return null;
	}
	
	public function getLabel(){
        return "";
    }
	
}