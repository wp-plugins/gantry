<?php
/**
 * @version   $Id: copyright.php 59361 2013-03-13 23:10:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrywidget');

add_action('widgets_init', array("GantryWidgetCopyright", "init"));

class GantryWidgetCopyright extends GantryWidget
{
	var $short_name = 'copyright';
	var $wp_name = 'gantry_copyright';
	var $long_name = 'Gantry Copyright';
	var $description = 'Gantry Copyright Widget';
	var $css_classname = 'widget_gantry_copyright';
	var $width = 200;
	var $height = 400;

	function init()
	{
		register_widget("GantryWidgetCopyright");
	}

	function render_widget_open($args, $instance)
	{
		?>
		<div class="clear"></div>
		<?php
		parent::render_widget_open($args, $instance);
	}

	function render($args, $instance)
	{
		/** @global $gantry Gantry */
global $gantry;
		ob_start();
		?>
		<a href="http://www.rockettheme.com/" title="rockettheme.com" id="rocket"></a>
		<?php echo $instance['text']; ?>
		<?php
		echo ob_get_clean();
	}
}
