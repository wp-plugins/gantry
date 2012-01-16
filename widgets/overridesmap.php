<?php
/**
 * @version		1.23 January 15, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();


gantry_import('core.gantrywidget');

/**
 * @package     gantry
 * @subpackage  features
 */
add_action('widgets_init', array("GantryWidgetOverridesMap","init"));


class GantryWidgetOverridesMap extends GantryWidget {
    var $short_name = 'overridesmap';
    var $wp_name = 'gantry_overrides_map';
    var $long_name = 'Gantry Overrides Map';
    var $description = 'Gantry widget to show the overrides used on a page';
    var $css_classname = 'widget_gantry_overrides_map';
    var $width = 300;
    var $height = 400;

    function init() {
        register_widget("GantryWidgetOverridesMap");
    }

    function render_title($args, $instance) {
    	global $gantry;
    	if($instance['title'] != '') :
    		echo $instance['title'];
    	endif;
    }

	function render($args, $instance){
        global $gantry;

        $catalog = gantry_get_override_catalog($gantry->templateName);
        $override_tree = $gantry->_override_tree;
	    ob_start();
	    
	    echo 'Overrides in Order Applied';
	    
	    ?>
        <ol>
            <?php foreach($override_tree as $override):?>
                <li><?php echo $catalog[$override->override_id];?> - <?php echo $override->rulename;  if (isset($override->nice_name)) echo  ' "' . $override->nice_name . '"';?> </li>
            <?php endforeach;?>
	    </ol>
		<?php
	    echo ob_get_clean();
	}
}