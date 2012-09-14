<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();
class GantryWidget extends WP_Widget {
    var $short_name = "";
    var $wp_name = '';
    var $long_name = "";
    var $description = "";
    var $css_classname = "";

    var $width = 300;
    var $height = 400;

    var $_values = array();

    var $_defaults = array();

    /**
     * constructors
     */
    function GantryWidget() {
        $this->__construct();
    }

    //static function for WP initialization
    function wp_init() {
        die('Gantry Widgets must override the wp_init() function and do a register_widget(current class name)');
    }

    /**
     * Static funciton for Gantry Init run during the Init() of gantry
     * @return void
     */
    function ganry_init() {

    }

    /**
     * Static funciton for Gantry Finalize run during the finalize() of gantry
     * @return void
     */
    function ganry_finalize() {

    }

    function __construct() {
        if (empty($this->short_name) || empty($this->long_name)) {
            die("A widget must have a valid type and classname defined");
        }
        $widget_options = array('classname' => $this->css_classname, 'description' => _g($this->description));
        $control_options = array('width' => $this->width, 'height' => $this->height);
        parent::__construct($this->wp_name, $this->long_name, $widget_options, $control_options);
    }


    function _cleanOutputVariable($variable, $value) {
        if (is_string($value)) {
            return htmlspecialchars($value);
        }
        elseif (is_array($value)) {
            foreach ($value as $subvariable => $subvalue) {
                $value[$subvariable] = GantryWidget::_cleanOutputVariable($subvariable, $subvalue);
            }
            return $value;
        }
        return $value;
    }

    function _cleanInputVariable($variable, $value) {
        if (is_string($value)) {
            return stripslashes($value);
        }
        elseif (is_array($value)) {
            foreach ($value as $subvariable => $subvalue) {
                $value[$subvariable] = GantryWidget::_cleanInputVariable($subvariable, $subvalue);
            }
            return $value;
        }
        return $value;
    }

    function form($instance) {
        gantry_import('core.config.gantryform');

        global $gantry;

        $defaults = $this->_defaults;

        $gantry->addScript('mootools.js');

        $instance = wp_parse_args((array) $instance, $defaults);

        foreach ($instance as $variable => $value)
        {
            $$variable = GantryWidget::_cleanOutputVariable($variable, $value);
            $instance[$variable] = $$variable;
        }

        $this->_values = $instance;
        $form = GantryForm::getInstance($this, $this->short_name, $this->short_name);
        $form->bind($this->_values);

        ob_start();

        $fieldSets = $form->getFieldsets();

        foreach ($fieldSets as $name => $fieldSet) {
            ?>
            <fieldset class="panelform">
            <?php foreach ($form->getFieldset($name) as $field) : ?>
                <div class="field-wrapper">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
                </div>
            <?php endforeach; ?>
            </fieldset>
            <?php

        }
        echo ob_get_clean();
    }


    function update($new_instance, $old_instance) {
        
        // Add any missing checkbox fields that changed
        $missing_keys = array_diff(array_keys($old_instance), array_keys($new_instance));
        foreach($missing_keys as $missing_key){
            $new_instance[$missing_key] = 0;
        }
        
        // clean up the input
        $tmp_instance = array();
        foreach($new_instance as $key=>$value){
            $clean_val = GantryWidget::_cleanInputVariable($key, $value);
            $tmp_instance[$key] = $clean_val;
        }
        return $tmp_instance;
    }


    function widget($args, $instance) {
        extract($args);
        $defaults = $this->_defaults;
        $instance = wp_parse_args((array) $instance, $defaults);
        foreach ($instance as $variable => $value)
        {
            $$variable = GantryWidget::_cleanOutputVariable($variable, $value);
            $instance[$variable] = $$variable;
        }
        ob_start();
        $this->render_position_open($args, $instance);
        $this->render_pre_widget($args, $instance);
        $this->render_widget_open($args, $instance);

        ob_start();
        $this->render_title($args, $instance);
        $title = ob_get_clean();

        if (!empty($title)) {
            $this->render_title_open($args, $instance);
            echo $title;
            $this->render_title_close($args, $instance);
        }

        $this->pre_render($args, $instance);
        $this->render($args, $instance);
        $this->post_render($args, $instance);
        $this->render_widget_close($args, $instance);
        $this->render_post_widget($args, $instance);
        $this->render_position_close($args, $instance);
        echo ob_get_clean();
    }

    function render_position_open($args, $instance) {
        echo $args['position_open'];
    }

    function render_pre_widget($args, $instance) {
        echo $args['pre_widget'];
    }

    function render_widget_open($args, $instance) {
        echo $args['widget_open'];
    }

    function render_title_open($args, $instance) {
        echo $args['title_open'];
    }


    function render_title($args, $instance) {

    }

    function render_title_close($args, $instance) {
        echo $args['title_close'];
    }

    function pre_render($args, $instance) {
        echo $args['pre_render'];
    }

    function render($args, $instance) {
    }

    function post_render($args, $instance) {
        echo $args['post_render'];
    }

    function render_widget_close($args, $instance) {
        echo $args['widget_close'];
    }

    function render_post_widget($args, $instance) {
        echo $args['post_widget'];
    }

    function render_position_close($args, $instance) {
        echo $args['position_close'];
    }
}