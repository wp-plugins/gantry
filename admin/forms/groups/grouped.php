<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die;

gantry_import('core.config.gantryformgroup');


class GantryFormGroupGrouped extends GantryFormGroup
{
    protected $type = 'grouped';
    protected $baseetype = 'group';

    public function getInput(){
        return '';
    }

    public function render($callback)
    {

        $buffer = '';

        $buffer .= "<div class='wrapper'>";
        foreach ($this->fields as $field) {
            $buffer .= $field->render($callback);
        }
		$buffer .= "</div>";
        return $buffer;
    }


}