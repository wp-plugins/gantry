<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();

class GantryLoader {
    /**
     * Loads a class from specified directories.
     *
     * @param string $name    The class name to look for ( dot notation ).
     * @return void
     */
    function import( $filePath )
    {
        static $paths;
        $base =  realpath(dirname( __FILE__ ).'/..');

        if (!isset($paths)) {
            $paths = array();
        }

        if (!isset($paths[$filePath]))
        {
            $parts = explode( '.', $filePath );
            $classname = array_pop( $parts );
            $path  = str_replace( '.', DS, $filePath );
            $rs   = require($base.DS.$path.'.class.php');
            $paths[$filePath] = $rs;
        }
        return $paths[$filePath];
    }
}


