<?php
/**
 * @version   $Id: cache.php 58623 2012-12-15 22:01:32Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

global $gantry;

$action = $_POST['gantry_action'];
if (!current_user_can('edit_theme_options')) die('-1');

if ($action == 'clear') {
	gantry_import('core.utilities.gantrycache');
	$cache = GantryCache::getInstance();
	$cache->clear('gantry', 'gantry');
	echo "success";
} else {
	return "error";
}
