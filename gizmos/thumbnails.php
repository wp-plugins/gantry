<?php
/**
 * @version		1.26 September 14, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrygizmo');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoThumbnails extends GantryGizmo {

    var $_name = 'thumbnails';

    function admin_init() {
        
        global $gantry;
	
		add_theme_support('post-thumbnails');
		set_post_thumbnail_size($gantry->get('thumb-width'), $gantry->get('thumb-height'), true);
		add_image_size('gantryThumb', $gantry->get('thumb-width'), $gantry->get('thumb-height'), true);

    }
    
    function init() {
        global $gantry;

		add_theme_support('post-thumbnails');
		set_post_thumbnail_size($gantry->get('thumb-width'), $gantry->get('thumb-height'), true);
		add_image_size('gantryThumb', $gantry->get('thumb-width'), $gantry->get('thumb-height'), true);

    }
}