<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @package   gantry
 * @subpackage core
 */
class GantryPlatform {

    var $php_version;
    var $platform;
    var $platform_version;
    var $jslib;
    var $jslib_version;
    var $jslib_shortname;
    var $_js_file_checks = array('');

    function GantryPlatform(){
        $this->php_version = phpversion();
        $this->_getPlatformInfo();
    }

    function _getPlatformInfo(){
        // See if its joomla
        if (defined('_JEXEC') && defined('JVERSION')){
            $this->platform='joomla';
            if (version_compare(JVERSION, '1.5', '>=') && version_compare(JVERSION, '1.6', '<')){
                $this->platform_version = JVERSION;
                $this->_getJoomla15Info();
            }
            else if (version_compare(JVERSION, '1.6', '>=') && version_compare(JVERSION, '1.7', '<')){
                $this->platform_version = JVERSION;
                $this->_getJoomla16Info();
            }
            else {
                $this->_unsuportedInfo();
            }
        }
        else if (defined('ABSPATH') && function_exists('do_action')){
            global $wp_version;
            $this->platform='wordpress';
            require_once(ABSPATH . WPINC . '/version.php');
            if (version_compare($wp_version,'2.8',">=")){
                $this->platform_version = $wp_version;
                $this->jslib = 'mootools';
                $this->jslib_shortname= 'mt';
                $this->jslib_version = '1.2';
                $this->_js_file_checks = array(
                    '-'.$this->jslib.$this->jslib_version,
                    '-'.$this->jslib_shortname.$this->jslib_version,
                    ''
                    );
            }
        }
        else {
            $this->_unsuportedInfo();
        }
    }

    function _unsuportedInfo(){
        foreach (get_object_vars($this) as $var_name => $var_value){
            if (!is_array($this->$var_name) && null == $var_value) $this->$var_name = "unsupported";
        }
    }

    // Get info for Joomla 1.5 versions
    function _getJoomla15Info(){
        $this->jslib = 'mootools';
        $this->jslib_shortname= 'mt';
        if (JPluginHelper::isEnabled('system', 'mtupgrade')){
            $this->jslib_version = '1.2';
        }
        else {
            $this->jslib_version = '1.1';
        }

        // Create the JS checks for Joomla 1.5
        $this->_js_file_checks = array(
            '-'.$this->jslib.$this->jslib_version,
            '-'.$this->jslib_shortname.$this->jslib_version
        );
        if (JPluginHelper::isEnabled('system', 'mtupgrade')){
            $this->_js_file_checks[] = '-upgrade';
        }
        $this->_js_file_checks[] = '';
    }

    // Get info for Joomla 1.6 versions
    function _getJoomla16Info(){
        $this->jslib = 'mootools';
        $this->jslib_shortname = 'mt';
        $this->jslib_version = '1.2';
        $this->_js_file_checks = array(
            '-'.$this->jslib.$this->jslib_version,
            '-'.$this->jslib_shortname.$this->jslib_version,
            ''
        );
    }

    function getJSChecks($file, $keep_path = false){
        $checkfiles = array();
        $ext = substr($file, strrpos($file, '.'));
        $path = ($keep_path)?dirname($file).DS:'';
        $filename = basename($file, $ext);
        if (is_array($this->_js_file_checks)){
            foreach($this->_js_file_checks as $suffix){
                $checkfiles[] = $path.$filename.$suffix.$ext;
            }
        }
        return $checkfiles;
    }

    function getJSInit(){
        return $this->jslib_shortname . '_'. str_replace('.','_',$this->jslib_version);
    }
}