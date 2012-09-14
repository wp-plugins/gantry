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
class GantryFeatureRenderer {
    // wrapper for feature display
    function display($feature_name, $layout = 'basic') {
        global
        $gantry;
        $feature = $gantry->_getFeature($feature_name);
        $rendered_feature = "";
        if ($feature->isEnabled() && method_exists($feature, 'render')) {
            $rendered_feature = $feature->render();
        }
        $contents = $rendered_feature . "\n";
        $output = $gantry->renderLayout('feature_' . $layout, array('contents' => $contents));
        return $output;
    }
}