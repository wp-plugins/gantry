<?php
/**
 * @version		1.26 September 14, 2012
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

require_once(dirname(__FILE__) . '/gantrymenu/libs/includes.php');

/**
 *
 */
class GantryWidgetMenu extends GantryWidget {
    static $themes = array();

    var $short_name = 'menu';
    var $wp_name = 'gantry_menu';
    var $long_name = 'Gantry Menu';
    var $description = 'Gantry Menu Description';
    var $css_classname = 'widget_gantry_menu';
    var $width = 200;
    var $height = 400;
    var $_defaults = array(
        'show_home' => 1,
        'home_text' => 'Home',
        'limit_levels' => 0,
        'startLevel' => 0,
        'endLevel' => 0,
        'showAllChildren' => 1,
        'maxdepth' => 10,
        'exclude' => '',
        'echo' => 1,
        'sort_column' => 'menu_order',
        'show_empty_menu' => 0,
        'title' => ''
    );

    ///protected $menu = null;

    public static function registerTheme($path, $name, $fullname, $themeClass) {
        $theme = array('name' => $name, 'fullname' => $fullname, 'path' => $path, 'class' => $themeClass);
        self::$themes[$name] = $theme;
    }

    public static function init() {
        global $wp_version, $gantry;
        register_widget("GantryWidgetMenu");
        if (is_admin()) {
            add_action('admin_head-widgets.php', array("GantryWidgetMenu", "displayAdminHeader"));
            add_filter('wp_edit_nav_menu_walker', array("GantryWidgetMenu", "setupWalker"), 1000, 2);
            add_action('wp_nav_menu_item_custom_fields', array("GantryWidgetMenu", "addThemeFormOptions"), 1, 4);
            add_action('wp_update_nav_menu_item', array("GantryWidgetMenu", "updateNavMenuItems"), 1, 3);
            add_filter('wp_get_nav_menu_items', array("GantryWidgetMenu", "getNavMenuItems"), 10, 3);
        }
        else {
            add_filter('wp_get_nav_menu_items', array("GantryWidgetMenu", "getNavMenuItems"), 10, 3);
        }

        $themes_dir = $gantry->templatePath . '/html/gantrymenu/themes';
        if (file_exists($themes_dir . '/catalog.php')) {
            include_once($themes_dir . '/catalog.php');
        }
    }

    public static function displayAdminHeader() {
        global $gantry;
        $gantry->addStyle($gantry->gantryUrl . '/widgets/gantrymenu/css/widget_admin.css');
    }

    protected function getDefaults() {
        $menudefaults = GantryMenu::getDefaults();
        $defaults = array_merge($menudefaults, $this->_defaults);
        foreach (self::$themes as $theme) {
            $themeclass = $theme['class'];
            if (class_exists($themeclass)) {
                $theme = new $themeclass();
                $theme_options = $theme->getDefaults();
                $defaults = array_merge($defaults, $theme_options);
            }
        }
        return $defaults;
    }

    public function render($args, $instance) {
        $menu = $this->initializeMenu($instance);
        $menu_render  = $menu->render();
        if (!$instance['show_empty_menu'] && !empty($menu_render)){
            $menu->renderHeader();            
            ob_start();
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
            echo $menu->render();
            $this->post_render($args, $instance);
            $this->render_widget_close($args, $instance);
            $this->render_post_widget($args, $instance);
            echo ob_get_clean();
        }
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
        $this->render($args, $instance);
        $this->render_position_close($args, $instance);
        echo ob_get_clean();
    }

    protected function getInstance() {
        global $wp_registered_widgets;
        $instance = array();
        $widget_info =& $wp_registered_widgets[$this->id];
        $widget =& $widget_info['callback'][0];
        $instances = $widget->get_settings();
        $instance = $instances[$widget_info['params'][0]['number']];
        return $instance;
    }

    protected function initializeMenu($instance = null) {
        $menu = null;
        if (null == $instance) {
            $instance = $this->getInstance();
        }
        $theme_info = self::$themes[$instance['theme']];
        $theme = new $theme_info['class'];

        $menu = new GantryMenu($theme, $instance);
        $menu->initialize();
        return $menu;
    }

    public function form($instance) {
        gantry_import('core.config.gantryform');
        global $gantry;
        GantryForm::addFieldPath($gantry->gantryPath . '/widgets/gantrymenu/admin/fields');
        $themes = self::$themes;

        foreach ($themes as $theme) {
            GantryForm::addFormPath($theme['path']);
        }


        // TODO see if we need this
        $gantry->addScript('mootools.js');

        $instance = wp_parse_args($instance, $this->getDefaults());

        // If no menus exists, direct the user to go and create some.
        foreach ($instance as $variable => $value)
        {
            $$variable = self::_cleanOutputVariable($variable, $value);
            $instance[$variable] = $$variable;
        }

        $this->_values = $instance;


        $form = GantryForm::getInstance($this, $this->short_name, $this->short_name);
        $form->bind($this->_values);
        $fieldSets = $form->getFieldsets();

        $subforms = array();
        foreach ($themes as $theme) {
            $subform = GantryForm::getInstance($this, $theme['name'], $theme['name']);
            $subform->bind($this->_values);
            $subforms[$theme['name']] = $subform;
        }

        ob_start();
        ?>
        <fieldset class="panelform">
            <?php foreach ($fieldSets as $name => $fieldSet): ?>
                <?php foreach ($form->getFieldset($name) as $field) : ?>
            <div class="field-wrapper">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
        <?php endforeach; ?>
            <?php foreach ($subforms as $subform_name => $subform): ?>
                <?php foreach ($subform->getFieldset($subform_name) as $field) : ?>
            <div class="field-wrapper">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
        <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endforeach;?>
        </fieldset>
        <?php
        echo ob_get_clean();

    }

    public function setupWalker($walker_class, $menu_id) {
        global $gantry;
        require_once($gantry->gantryPath . '/widgets/gantrymenu/admin/RokMenuWalkerNavMenuEdit.php');
        return 'RokMenuWalkerNavMenuEdit';
    }

    public function addThemeFormOptions($item_id, $item, $depth, $args) {
        $theme_info = self::getItemFieldsInstance();
        $theme_info->renderFields($item_id, $item, $depth, $args);
    }

    public function updateNavMenuItems($menu_id, $menu_item_db_id, $args) {
        $menu = wp_get_nav_menu_object($menu_id);
        $gantry_menu_items = get_option('gantry_menu_items');
        if ($gantry_menu_items == false) {
            $gantry_menu_items = array();
        }
        $theme_info = self::getItemFieldsInstance();
        foreach ($theme_info->fields as $field) {
            $gantry_menu_items[$menu->slug][$menu_item_db_id][$field] = $_POST['menu-item-' . $field][$menu_item_db_id];
        }
        update_option('gantry_menu_items', $gantry_menu_items);
    }

    public function getNavMenuItems($items, $menu, $args) {
        $rokmenu_menu_items = get_option('gantry_menu_items');
        if ($rokmenu_menu_items == false) return $items;
        $menu_options = $rokmenu_menu_items[$menu->slug];
        if ($menu_options == false) return $items;
        $modded_items = array();
        foreach ($items as $key => $item) {
            if (array_key_exists($item->ID, $menu_options)) {
                $item_options = $menu_options[$item->ID];
                foreach ($item_options as $item_option => $item_option_value) {
                    $item->$item_option = $item_option_value;
                }
            }
            $modded_items[$key] = $item;
        }
        return $modded_items;
    }

    function getItemFieldsInstance() {
        global $gantry;
        $item_files = array(
            'GantryMenuItemFieldsDefault' => $gantry->templatePath . '/html/gantrymenu/themes/itemfields.php'
        );

        foreach ($item_files as $classkey => $item_file_path) {
            if (file_exists($item_file_path) && is_readable($item_file_path)) {
                $file = $item_file_path;
                $className = $classkey;
                break;
            }
        }
        if (!class_exists($className) && file_exists($file)) {
            require_once($file);
        }
        if (class_exists($className)) {
            return new $className();
        }
        else {
            return false;
        }
    }
    
    function render_title($args, $instance) {
    	global $gantry;
    	if($instance['title'] != '') :
    		echo $instance['title'];
    	endif;
    }
}