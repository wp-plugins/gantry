<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();

/**
 * Implement the singleton pattern for the Gantry framework
 *
 * @package gantry
 * @subpackage core
 */
class GantrySingleton {

    /**
     * Gets the singleton instance of the class name passed in.
     *
     * @param  string $class The name of the class to get a singleton for
     * @return The singleton instance of the class name passed in.
     */
    public static function getInstance($class)
    {
        static $instances = array ();
            // array of instance names
        if (!array_key_exists($class, $instances)) {
            // instance does not exist, so create it
            $instances[$class] = new $class;
        }
            // if
        $instance =& $instances[$class];
        return $instance;
    }
}