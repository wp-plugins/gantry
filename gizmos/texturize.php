<?php
/**
 * @version		1.21 October 20, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


defined('GANTRY_VERSION') or die();

gantry_import('core.gantrygizmo');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoTexturize extends GantryGizmo {

    var $_name = 'texturize';

    function query_parsed_init() {

		remove_filter('the_content', 'wptexturize');
		remove_filter('comment_text', 'wptexturize');

    }
}