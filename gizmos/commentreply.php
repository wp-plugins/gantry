<?php
/**
 * @version   1.26 September 14, 2012
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
class GantryGizmoCommentReply extends GantryGizmo {

    var $_name = 'commentreply';

    function isEnabled(){
        return true;
    }


    function query_parsed_init() {
        global $gantry;

		if(is_singular() && get_option('thread_comments')) : wp_enqueue_script('comment-reply'); endif;

    }
}