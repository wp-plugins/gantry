<?php
/**
 * @version   1.28 November 13, 2012
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
class GantryGizmoBelatedPNG extends GantryGizmo {
    var $_name = 'belatedPNG';

    function isEnabled(){
        return true;
    }

	function query_parsed_init() {
        global $gantry;

		if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion == '6') {
			$fixes = $gantry->belatedPNG;

			$gantry->addScript('belated-png.js');
			$gantry->addDomReadyScript($this->_belatedPNG($fixes));
		}
	}

	function _belatedPNG($fixes) {
		if (!is_array($fixes) || count($fixes) == 0) $fixes = array('.png');
		$fixes = implode("', '", $fixes);

		$js = "
				var pngClasses = ['$fixes'];
				pngClasses.each(function(fixMePlease) {
					DD_belatedPNG.fix(fixMePlease);
				});
		\n";

		return $js;
	}
}