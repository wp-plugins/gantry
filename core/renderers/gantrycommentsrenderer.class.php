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
 * @subpackage  core.renderers
 */
class GantryCommentsRenderer {
    // wrapper for feature display
    function display($layout = 'basic', $commentLayout = 'basic') {
        global $gantry;
        $output = $gantry->renderLayout('commentstempl_' . $layout, array('commentLayout' => $commentLayout));
        return $output;
    }
}   