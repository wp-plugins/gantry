<?php
/**
 * @version   $Id: divider.php 59361 2013-03-13 23:10:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrywidget');

add_action('widgets_init', array("GantryWidgetDivider", "init"));
add_action('admin_head-widgets.php', array('GantryWidgetDivider', 'addHeaders'), -1000);

class GantryWidgetDivider extends WP_Widget
{
	var $_defaults = array();

	function init()
	{
		register_widget("GantryWidgetDivider");
	}

	function GantryWidgetDivider()
	{
		$widget_ops  = array('classname' => 'widget_gantry_divider', 'description' => _g('Gantry Divider Widget'));
		$control_ops = array('width' => 0, 'height' => 0);
		$this->WP_Widget('gantrydivider', _g('Gantry Divider'), $widget_ops, $control_ops);
	}

	function widget($args, $instance)
	{
		extract($args);
		$defaults = $this->_defaults;
	}

	function form($instance)
	{
		return "noform";
	}

	function addHeaders()
	{
		/** @global $gantry Gantry */
global $gantry;
		$gantry->addScript('gantrydivider.js');
		$gantry->addStyle('gantrydivider.css');
	}
}
