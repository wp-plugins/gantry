<?php
/**
 * @version   $Id: functions.php 58636 2012-12-16 20:22:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

function _g($str)
{
	return __($str, 'gantry');
}

function _ge($str)
{
	_e($str, 'gantry');
}

function _gn($single, $plural, $number)
{
	_n($single, $plural, $number, 'gantry');
}


function _r($str)
{
	global $gantry;
	return __($str, $gantry->templateName . '_lang');
}

function _re($str)
{
	global $gantry;
	_e($str, $gantry->templateName . '_lang');
}

function _rn($single, $plural, $number)
{
	global $gantry;
	_n($single, $plural, $number, $gantry->templateName . '_lang');
}

/**
 * Like implode but with keys
 *
 * @param       string[optional] $glue
 * @param array $pieces
 * @param       string[optional] $hifen
 *
 * @return string
 */
function implode_with_key($glue = null, $pieces, $hifen = ',')
{
	$return = null;
	foreach ($pieces as $tk => $tv) $return .= $glue . $tk . $hifen . $tv;
	return substr($return, 1);
}

/**
 * @param  string $path the gantry path to the class to import
 *
 * @return
 */
function gantry_import($path)
{
	require_once (realpath(dirname(__FILE__)) . '/core/gantryloader.class.php');
	return GantryLoader::import($path);
}

function gantry_template_include_filter($filename)
{
	if (defined('NONGANTRY_TEMPLATE')) return $filename;
	global $gantry;
//    if (empty($filename)){
//        $filename = "index.php";
//    }
	$ext  = substr($filename, strrpos($filename, '.'));
	$file = basename($filename, $ext);
	//$checks = $gantry->_getBrowserBasedChecks($file);
	$checks   = $gantry->browser->getChecks($filename);
	$platform = $gantry->browser->platform;
	$enabled  = $gantry->get($platform . '-enabled', 1);
	$view     = $gantry->get('template_prefix') . $platform . '-switcher';
	$cookie   = (array_key_exists($view, $_COOKIE)) ? htmlentities($_COOKIE[$view]) : 1;

	// flip to get most specific first
	$checks = array_reverse($checks);

	// remove the default index.php page
	array_pop($checks);

	$template_paths = array(
		$gantry->templatePath,
		$gantry->gantryPath . DS . 'tmpl'
	);

	foreach ($template_paths as $template_path) {
		if (file_exists($template_path) && is_dir($template_path)) {
			foreach ($checks as $check) {
				$check_path = preg_replace("/\?(.*)/", '', $template_path . DS . $check);
				if (file_exists($check_path) && is_readable($check_path) && $enabled && $cookie != 0) {
					$filename = $check_path;
					break(2);
				}
			}
		}
	}
	return $filename;
}

function gantry_mootools_init()
{
	if (defined('NONGANTRY_TEMPLATE')) return;
	global $gantry;
	@wp_register_script('mootools.js', $gantry->gantryUrl . '/js/mootools.js');
}

function gantry_change_widiget_init_action()
{
	if (defined('NONGANTRY_TEMPLATE')) return;
	remove_action('init', 'wp_widgets_init', 1);
	add_action('wp', 'wp_widgets_init', 1);
}

function gantry_force_base_widget_settings()
{
	/** @var $wp_widget_factory WP_Widget_Factory */
	global $wp_widget_factory;
	foreach ($wp_widget_factory->widgets as $classname => $widget_instance) {
		/** @var $widget_instance WP_Widget */
		if (!get_option($widget_instance->option_name)) {
			$widget_instance->save_settings(array());
		}
	}
}

function gantry_construct()
{
	global $gantry, $gantry_path, $wp_query, $current_blog;
	$gantry_templatepath = get_template_directory() . '/templateDetails.xml';
	if (!file_exists($gantry_templatepath)) {
		define('NONGANTRY_TEMPLATE', 'NONGANTRY_TEMPLATE');
		return;
	}

	if (!defined('GANTRY_VERSION')) {
		/**
		 * @name GANTRY_VERSION
		 */
		define('GANTRY_VERSION', '1.31');


		if (!defined('DS')) {
			define('DS', DIRECTORY_SEPARATOR);
		}

		// Turn on sessions for Wordpress
		if (!defined('GANTRY_SESSIONS_ENABLED')) {
			if (!session_id()) {
				define('GANTRY_SESSIONS_ENABLED', true);
				session_start();
			}
		}

		$options        = get_option(get_template() . "-template-options");
		$cache_enabled  = $options['cache']['enabled'];
		$cache_lifetime = $options['cache']['time'];

		load_plugin_textdomain('gantry', false, basename($gantry_path) . '/languages');

		// Get the gantry instance
		gantry_import('core.gantry');

		if ($cache_enabled) {
			gantry_import('core.utilities.gantrycache');
			$cache = GantryCache::getInstance();
			$cache->setLifetime($cache_lifetime);
			$cache->init();
			$gantry = $cache->get('gantry', 'gantry', array('Gantry', 'getInstance'));
		} else {
			$gantry = Gantry::getInstance();
		}

		// Load the widget positions for the template
		$gantry->_loadWidgetPositions();
		add_filter('query_vars', array('GantryTemplateDetails', 'addUrlVars'));
	}
}


function gantry_load_template_lang_action()
{
	if (defined('NONGANTRY_TEMPLATE')) return;
	global $gantry;
	load_theme_textdomain($gantry->templateName . '_lang');
}

function gantry_init_action()
{
	if (defined('NONGANTRY_TEMPLATE')) return;
	global $gantry;
	$gantry->init();
	$gantry->basicLoad();
}

function gantry_post_parse_load_action()
{
	if (defined('NONGANTRY_TEMPLATE')) return;
	global $gantry;
	$gantry->postParseLoad();
}

function gantry_admin_ajax()
{
	global $gantry;
	$model = $gantry->getAjaxModel($_POST['model'], true);
	if ($model === false) die();
	include_once($model);
	die();
}

function gantry_ajax()
{
	global $gantry;
	$model = $gantry->getAjaxModel($_POST['model'], false);
	if ($model === false) die();
	include_once($model);
	die();
}

function gantry_force_blank_comment($path)
{
	if (defined('NONGANTRY_TEMPLATE')) return $path;
	global $gantry;
	if ($path == $gantry->templatePath . '/comments.php') {
		return $path;
	}
	return $gantry->gantryPath . '/html/comments.php';
}

function gantry_get_override_catalog($templateName)
{
	$override_catalog = get_option($templateName . '-template-options-overrides');
	if ($override_catalog === false) {
		$override_catalog = array();
	}
	return $override_catalog;
}

function gantry_udpate_override_catalog($catalog = array())
{
	global $gantry;
	$override_catalog_name = $gantry->templateName . '-template-options-overrides';
	update_option($override_catalog_name, $catalog);
}

function gantry_load_sidebar_intercept($sidebar_widgets)
{
	if (defined('NONGANTRY_TEMPLATE')) return $sidebar_widgets;
	global $gantry;
	$override_tree = $gantry->_override_tree;
	if (!empty($override_tree)) {
		$default_sidebar_widgets = $sidebar_widgets;
		foreach ($override_tree as $override) {
			$override_sidebar_widgets = get_option($gantry->templateName . '-template-options-override-sidebar-' . $override->override_id);
			if ($override_sidebar_widgets !== false) {
				foreach ($default_sidebar_widgets as $sidebar => $default_widgets) {
					if (array_key_exists($sidebar, $override_sidebar_widgets)) {
						foreach ($override_sidebar_widgets[$sidebar] as $ow) {
							$widget_base = substr($ow, 0, strrpos($ow, '-'));
							add_filter('option_widget_' . $widget_base, 'gantry_setup_override_widget_instances_intercept', -1000, 1);
						}
						$sidebar_widgets[$sidebar] = $override_sidebar_widgets[$sidebar];
					}
				}
			}
		}
	}
	return $sidebar_widgets;
}

function gantry_setup_override_widget_instances()
{
	if (defined('NONGANTRY_TEMPLATE')) return;
	global $wp_registered_widget_updates;
	$widget_names = array_keys($wp_registered_widget_updates);
	foreach ($widget_names as $widget) {
		add_filter('option_widget_' . $widget, 'gantry_setup_override_widget_instances_intercept', -1000, 1);
	}
}

function gantry_setup_override_widget_instances_intercept($widget_instance)
{
	global $gantry;
	$current_widget_type = str_replace('option_', '', current_filter());
	$override_catalog    = gantry_get_override_catalog($gantry->templateName);
	if (!empty($override_catalog)) {
		foreach ($override_catalog as $override_id => $override_name) {
			$override_widget_settings = get_option($gantry->templateName . '-template-options-override-widgets-' . $override_id);
			if ($override_widget_settings !== false) {
				if (array_key_exists($current_widget_type, $override_widget_settings)) {
					$widget_instance = $widget_instance + $override_widget_settings[$current_widget_type];
				}
			}
		}
	}
	return $widget_instance;
}

function gantry_load_sidebar_widgets_settings_intercept($widget_instance)
{
	global $gantry;
	$current_widget_type = str_replace('option_', '', current_filter());
	$override_tree       = $gantry->_override_tree;
	if (!empty($override_tree)) {
		foreach ($override_tree as $override) {
			$override_widget_settings = get_option($gantry->templateName . '-template-options-override-widgets-' . $override->override_id);
			if ($override_widget_settings !== false) {
				if (array_key_exists($current_widget_type, $override_widget_settings)) {
					$widget_instance = $widget_instance + $override_widget_settings[$current_widget_type];
				}
			}
		}
	}
	return $widget_instance;
}


/**
 * Filter to get the template file name that is being run and pass it gantry so that it can include the correct main body.
 *
 * @param  $template
 *
 * @return the re
 */
function gantry_get_template_page_filter($template)
{
	if (defined('NONGANTRY_TEMPLATE')) return $template;
	global $gantry;
	$newtemplate = $template;
	$gantry->addTemp('template', 'page_name', $newtemplate);
	return $template;
}

/**
 * Forces the template to use a specific main body file. This file must be in the template or the gantry "html" dir.
 * The template one overrides the gantry one.  If that file is not there it will run through the normal hierarchy.
 *
 * @param  $mainbody_page string the page name "authors.php"
 */
function gantry_force_mainbody_page($mainbody_page)
{
	global $gantry;
	$gantry->addTemp('template', 'page_name', $mainbody_page);
}

/**
 * This funciton will clear the override tree of all overrides.
 * @return void
 */
function gantry_clear_overrides()
{
	global $gantry;
	$gantry->clearOverrides();
}

/**
 * This function adds a set of overrides to the overrides tree.  You can pass an array of override id numbers or the names
 * ('Custom Override 1'..etc) to the function and it will set that override in line to be used in the override tree.  An
 * option priority can be passed so you can control placement in the tree.
 *
 * @param mixed $overrides an array of override ids or names or a single override id or name
 * @param int   $priority  (optional)
 *
 * @return void
 */
function gantry_set_overrides($overrides, $priority = 10)
{
	global $gantry;

	if (!is_array($overrides)) {
		$overrides = array($overrides);
	}

	$catalog            = gantry_get_override_catalog($gantry->templateName);
	$overrides_to_force = array();
	foreach ($overrides as $forced_override) {
		if (is_int($forced_override) && array_key_exists($forced_override, $catalog)) {
			$overrides_to_force[] = $forced_override;
		} else {
			if ($loc = array_search($forced_override, $catalog)) {
				$overrides_to_force[] = $loc;
			} else {
				//TODO log unable to find override
			}
		}
		$gantry->addOverrides($overrides_to_force, $priority);
	}
}










