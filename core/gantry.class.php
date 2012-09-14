<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('GANTRY_VERSION') or die();

gantry_import('core.gantrytemplatedetails');
gantry_import('core.gantryini');
gantry_import('core.gantrypositions');
gantry_import('core.gantrystylelink');
gantry_import('core.gantrysingleton');
gantry_import('core.rules.gantryoverridesengine');
gantry_import('core.gantryplatform');
gantry_import('core.utilities.gantryurl');



/**
 * This is the base class for the Gantry framework.   It is the primary mechanisim for template definition
 *
 * @package gantry
 * @subpackage core
 */
class Gantry extends GantrySingleton {

    protected static $instance;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Gantry();
        }
        return self::$instance;
    }

	// Cacheable
    /**
     *
     */
	var $basePath;
	var $baseUrl;
    var $templateName;
	var $templateUrl;
	var $templatePath;
    var $gantryPath;
    var $gantryUrl;
    var $layoutSchemas = array();
    var $mainbodySchemas = array();
    var $pushPullSchemas = array();
    var $mainbodySchemasCombos = array();
    var $default_grid = 12;
	var $presets = array();
	var $originalPresets = array();
	var $customPresets = array();
    var $dontsetinoverride = array();
    var $defaultMenuItem;
    var $currentMenuItem;
    var $currentMenuTree;
    var $template_prefix;
    var $custom_dir;
    var $custom_menuitemparams_dir;
    var $custom_presets_file;
    var $positions = array();
    var $altindex = false;
    var $platform;
    var $templateInfo;

    // Not cacheable
    var $document;
    var $browser;
    var $language;
    var $session;
    var $currentUrl;
    var $pageTitle;


    // Private Vars
	/**#@+
     * @access private
     */


    // cacheable privates
	var $_templateDetails;
	var $_aliases = array();
	var $_preset_names = array();
	var $_param_names = array();
    var $_base_params_checksum = null;
	var $_setbyurl = array();
	var $_setbycookie = array();
	var $_setbysession = array();
	var $_setinsession = array();
	var $_setincookie = array();
    var $_setinoverride = array();
    var $_setbyoverride = array();
	var $_features = array();
    var $_gizmos = array();
    var $_widgets = array();
    var $_widget_configs = array();
    var $_ajaxmodels = array();
    var $_adminajaxmodels = array();
    var $_layouts = array();
	var $_bodyclasses = array();
	var $_classesbytag = array();
    var $_ignoreQueryParams = array('reset-settings');
    var $_config_vars = array(
        'layoutschemas'=>'layoutSchemas',
        'mainbodyschemas'=>'mainbodySchemas',
        'mainbodyschemascombos' => 'mainbodySchemasCombos',
        'pushpullschemas'=>'pushPullSchemas',
        'presets'=>'presets',
        'browser_params' => '_browser_params',
        'grid'=>'grid'
    );
    var $_working_params;
    var $_override_engine = null;
    var $_contentTypePaths = array();

    // non cachable privates
	var $_bodyId = null;
    var $_browser_params = array();
    var $_menu_item_params = array();
    var $_tmp_vars = array();


    // reseetable noncache
    var $_scripts = array();
    var $_full_scripts = array();
    var $_domready_script = '';
    var $_loadevent_script = '';
    var $_inline_script = '';
    var $_inline_style = '';
    var $_styles = array();

    var $_override_tree = array();
    /**#@-*/

    var $__cacheables = array(
            '__cacheables',
            'basePath',
            'baseUrl',
            'templateName',
            'templateUrl',
            'templatePath',
            'gantryPath',
            'gantryUrl',
            'layoutSchemas',
            'mainbodySchemas',
            'pushPullSchemas',
            'mainbodySchemasCombos',
            'default_grid',
            'presets',
            'originalPresets',
            'customPresets',
            'dontsetinoverride',
            'defaultMenuItem',
            'currentMenuItem',
            'currentMenuTree',
            'template_prefix',
            'custom_dir',
            'custom_menuitemparams_dir',
            'custom_presets_file',
            'positions',
            '_templateDetails',
			'_aliases',
            '_preset_names',
            '_param_names',
            '_base_params_checksum',
            '_setbyurl',
            '_setbycookie',
            '_setbysession',
            '_setinsession',
            '_setincookie',
            '_setinoverride',
            '_setbyoverride',
            '_features',
            '_ajaxmodels',
            '_adminajaxmodels',
            '_layouts',
            '_bodyclasses',
            '_classesbytag',
            '_ignoreQueryParams',
            '_config_vars',
            '_working_params',
            'platform',
            'templateInfo',
            '_override_engine',
            '_gizmos',
            '_contentTypePaths'
        );

    function __sleep() {
        return $this->__cacheables;
    }

    /**
     * Constructor
     * @return void
     */
	function Gantry() {
        //global $mainframe;
        global $gantry_path;
        // load the base gantry path
        $this->gantryPath = $gantry_path;
        $this->gantryUrl =  WP_PLUGIN_URL.'/'.basename($this->gantryPath);

        // set the base class vars
		//$doc =& JFactory::getDocument();
		//$this->document =& $doc;


		$this->basePath = ABSPATH;
        $this->templateName = $this->_getCurrentTemplate();
        $this->templatePath = get_template_directory();
        $this->custom_dir = $this->templatePath.DS.'custom';
        $this->custom_menuitemparams_dir= $this->custom_dir.DS.'menuitemparams';
        $this->custom_presets_file = $this->custom_dir.DS.'presets.ini';

        $urlinfo = parse_url(get_option('siteurl'));
        $this->baseUrl = $urlinfo["path"]."/";
        $urlinfo = parse_url(get_bloginfo('template_url'));
        $this->templateUrl = $urlinfo["path"];

        $this->_loadConfig();

		// Load up the template details
		$this->_templateDetails = GantrySingleton::getInstance('GantryTemplateDetails');
		$this->_templateDetails->init($this);
        $this->templateInfo = &GantryTemplateInfo::getInstance();
        $this->_base_params_checksum = $this->_templateDetails->getParamsHash();

        gantry_import('core.gantryplatform');
		$this->platform = new GantryPlatform();


        // set base ignored query string params    dont pass these back
        $this->_ignoreQueryParams[] = 'reset-settings';  //TODO  Add Filter
        
        // Put a base copy of the saved params in the working params
		$this->_working_params = $this->_templateDetails->params;
		$this->_param_names = array_keys($this->_templateDetails->params);
        $this->template_prefix =  $this->_working_params['template_prefix']['value'];


		// set the GRID_SYSTEM define;
        if (!defined('GRID_SYSTEM')) {
            define ('GRID_SYSTEM',$this->get('grid_system',$this->default_grid));
        }

		// process the presets
        if (!empty($this->presets)) {
			// check for custom presets
			$this->_customPresets();
            $this->_preset_names = array_keys($this->presets);
            //$wp_keys = array_keys($this->_templateDetails->params);
            //$this->_param_names = array_diff($wp_keys, $this->_preset_names);
        }

        $this->_loadLayouts();
        $this->_loadGizmos();

        $this->_loadAjaxModels();
        $this->_loadAdminAjaxModels();

        // set up the positions object for all gird systems defined
        foreach(array_keys($this->mainbodySchemasCombos) as $grid){
            $this->positions[$grid] = GantryPositions::getInstance($grid);
        }

        
        $this->_override_engine = $this->_loadOverrideEngine();

//		// add GRID_SYSTEM class to body
		$this->addBodyClass("col".GRID_SYSTEM);
	}
    
    /**
     * Initializer.
     * This should run when gantry is run from the front end in order and before the template file to
     * populate all user session level data
     * @return void
     */
    function init() {
        ob_start();
        if (defined('GANTRY_INIT')) {
            return;
        }
        // Run the admin init
        if ($this->isAdmin()) {
            $this->adminInit();
            return;
        }
        define('GANTRY_INIT', "GANTRY_INIT");

        $this->_loadWidgets();
        $this->_initWidgets();
        $this->_loadGizmos();

        // set the GRID_SYSTEM define;
        if (!defined('GRID_SYSTEM')) {
            define ('GRID_SYSTEM',$this->get('grid_system',$this->default_grid));
        }

        $this->language = get_bloginfo('language');

        // Set the call specific URL vars
        $urlinfo = parse_url(get_option('siteurl'));
        $this->baseUrl = $urlinfo["path"]."/";        
        $urlinfo = parse_url(get_bloginfo('template_url'));
        $this->templateUrl = $urlinfo["path"];


        $this->_initContentTypePaths();
        
        // Set the Platform info
        gantry_import('core.gantryplatform');
		$this->platform = new GantryPlatform();

        // Set the Brwoser info
        gantry_import('core.gantrybrowser');
        $this->browser = new GantryBrowser();
    }

    function adminInit() {
        if (defined('GANTRY_INIT')) {
            return;
        }
        define('GANTRY_INIT', "GANTRY_INIT");
        gantry_import('core.gantrybrowser');
        $this->browser = new GantryBrowser();
        // Set the Platform info
        gantry_import('core.gantryplatform');
		$this->platform = new GantryPlatform();
        $this->_loadWidgets();
        $this->_initWidgets();
        $this->_getWidgetConfigs();

        // Init all gizmos
        foreach($this->_gizmos as $gizmo){
            $gizmo_instance = $this->_getGizmo($gizmo);
            if ($gizmo_instance !==  false && $gizmo_instance->isEnabled() && method_exists( $gizmo_instance , 'init')) {
                $gizmo_instance->admin_init();
            }
        }
    }

    function basicLoad(){
        //add default gantry stylesheet
        $this->addStyle('gantry.css');

        //add correct grid system css
        $this->addStyle('grid-'.GRID_SYSTEM.'.css');
        $this->addStyle('wordpress.css');

        // Init all gizmos
        foreach($this->_gizmos as $gizmo){
            $gizmo_instance = $this->_getGizmo($gizmo);
            if ($gizmo_instance !==  false && $gizmo_instance->isEnabled() && method_exists( $gizmo_instance , 'init')) {
                $gizmo_instance->init();
            }
        }
    }

    /**
     * Function to init params, gizmos, and widgets once the http query string has been parsed
     * This should only be run once.
     * @return nothing
     */
    function postParseLoad(){
        global $wp_query;
        $engine_output = $this->_override_engine->run($wp_query);
        $this->_override_tree = $engine_output->getOverrideList();
        $this->_postParseLoad();
        $this->_loadWidgetPositions();
        $this->currentUrl = $_SERVER['REQUEST_URI'];
    }

    function _postParseLoad(){
        // Populate all the params for the session
        $this->_populateParams();
        $this->_loadBrowserConfig();
        // Init all gizmos
        foreach($this->_gizmos as $gizmo){
            $gizmo_instance = $this->_getGizmo($gizmo);
            if ($gizmo_instance !==  false && $gizmo_instance->isEnabled() && method_exists( $gizmo_instance , 'query_parsed_init')) {
                $gizmo_instance->query_parsed_init();
            }
        }
        // Init all widgets
        foreach($this->_widgets as $widget){
            if (method_exists($widget,'gantry_init')){
                call_user_func(array($widget,'gantry_init'));
            }
        }
    }

    function reset(){
        if (defined('GANTRY_FINALIZED')) return;
        $this->_scripts = array();
        $this->_full_scripts = array();
        $this->_domready_script = '';
        $this->_loadevent_script = '';
        $this->_inline_script = '';
        $this->_inline_style = '';
        $this->_styles = array();

        $this->basicLoad();
        $this->_postParseLoad();
    }

    function finalize() {
        if (!defined('GANTRY_FINALIZED')){
            gantry_import('core.params.gantrycookieparams');
            gantry_import('core.params.gantrysessionparams');

            // finalize all widgets
            foreach($this->_widgets as $widget){
                if (method_exists($widget,'gantry_finalize')){
                    call_user_func(array($widget,'gantry_finalize'));
                }
            }

            // finalize all gizmos
            foreach($this->_gizmos as $gizmo){
                $gizmo_instance = $this->_getGizmo($gizmo);
                if ($gizmo_instance !==  false && $gizmo_instance->isEnabled() && method_exists( $gizmo_instance , 'finalize')) {
                    $gizmo_instance->finalize();
                }
            }

            // Run the cleanup or store on cookies and sessions
            if (isset($_REQUEST['reset-settings'])) {
                GantrySessionParams::clean();
                GantryCookieParams::clean();
            }
            else {
                GantrySessionParams::store();
                GantryCookieParams::store();
            }

            // Apply compression if enabled
            if ($this->get("gzipper-enabled",false)) {
                gantry_import('core.gantrygzipper');
                GantryGZipper::processCSSFiles();
                GantryGZipper::processJsFiles();
            }
            
		    define('GANTRY_FINALIZED', true);
		}
        
		if ($this->altindex !== false) {
            $contents = ob_get_contents();
            ob_end_clean();
            ob_start();
            echo $this->altindex;
        }

        $output =  ob_get_clean();

        // process page output to add header in
        $this->_displayHead($output);
        $this->_displayFooter($output);
        $this->_displayBodyTag($output);
        echo $output;
    }

    function finalizeAdmin(){
        if (!defined('GANTRY_FINALIZED')){
            // Apply compression if enabled
            if ($this->get("gzipper-enabled",false)) {
                gantry_import('core.gantrygzipper');
                GantryGZipper::processCSSFiles();
                GantryGZipper::processJsFiles();
            }
		    define('GANTRY_FINALIZED', true);
        }

        $output =  ob_get_clean();

        // process page output to add header in
        $this->_displayHead($output);
        $this->_displayFooter($output);
        $this->_displayBodyTag($output);
        echo $output;
    }

    function isAdmin(){
        return is_admin();
    }

    function get($param = false, $default = "") {
		if (array_key_exists($param, $this->_working_params)) $value = $this->_working_params[$param]['value'];
		else $value = $default;
		return $value;
	}

	function getDefault($param = false) {
		$value = "";
		if (array_key_exists($param, $this->_working_params)) $value = $this->_working_params[$param]['default'];
		return $value;
	}

    function getFeatures(){
        return $this->_features;
    }

	function set($param, $value=false) {
		$return = false;
		if (array_key_exists($param, $this->_working_params)){
			$this->_working_params[$param]['value'] = $value;
			$return = true;
		}
		return $return;
	}

    function getAjaxModel($model_name, $admin=false){
        $model_path = false;
        if ($admin) {
            if (array_key_exists($model_name, $this->_adminajaxmodels)){
                $model_path = $this->_adminajaxmodels[$model_name];
            }
        }
        else {
            if (array_key_exists($model_name, $this->_ajaxmodels)){
                $model_path = $this->_ajaxmodels[$model_name];
            }
        }
        return $model_path;
    }
    
    function getPositions($position = null, $pattern = null) {
		if ($position != null) {
			$positions = $this->_templateDetails->parsePosition($position, $pattern);
			return $positions;
		}
		return $this->_templateDetails->getPositions();
	}
    
	function getUniquePositions() {
		return $this->_templateDetails->getUniquePositions();
	}
    
    function getPositionInfo($position_name) {
		return $this->_templateDetails->getPositionInfo($position_name);
	}

	function getParams($prefix=null,$remove_prefix=false) {
        if (null==$prefix){
		    return $this->_working_params;
        }
        $params=array();
        foreach ($this->_working_params as $param_name => $param_value){
            $matches = array();
            if (preg_match("/^".$prefix."-(.*)$/", $param_name, $matches)){
                if ($remove_prefix){
                    $param_name = $matches[1];
                }
                $params[$param_name] = $param_value;
            }
        }
        return $params;
	}

    /**
     * Gets the current URL and query string and can ready it for more query string vars
     * @param array $ignore
     * @param bool $qs_preped
     * @return mixed|string
     */
    function getCurrentUrl($ignore=array()){
        gantry_import('core.utilities.gantryurl');

        $url = GantryUrl::explode($this->currentUrl);

        if (!empty($ignore) && array_key_exists('query_params', $url)) {
            foreach ($ignore as $k) {
               if (array_key_exists($k, $url['query_params'])) unset($url['query_params'][$k]);
            }
        }
        return GantryUrl::implode($url);
    }

    function addQueryStringParams($url, $params = array()) {
        gantry_import('core.utilities.gantryurl');
        return GantryUrl::updateParams($url, $params);
    }

    // wrapper for count modules
	function countModules($positionStub, $pattern = null) {
		if (defined('GANTRY_FINALIZED')) return 0;
		global $wp_registered_sidebars, $wp_registered_widgets;

		$count = 0;
        gantry_import('core.renderers.gantrywidgetsrenderer');
        add_filter('sidebars_widgets', array('GantryWidgetsRenderer', 'filterWidgetCount'));
        $sidebars_widgets = wp_get_sidebars_widgets();
        //$sidebar = $wp_registered_sidebars[$index];

        if (array_key_exists($positionStub, $sidebars_widgets) && !empty($sidebars_widgets[$positionStub])){
            $section_counted = false;
            foreach ($sidebars_widgets[$positionStub] as $widget){
                if (!preg_match("/^gantrydivider/", $widget) && !$section_counted ){
                    $count++;
                    $section_counted=true;
                }
                else if (preg_match("/^gantrydivider/", $widget)){
                    $section_counted = false;
                }
            }
        }
        return $count;
	}
	
	/**
     * @param  $position
     * @param  $pattern
     * @return int
     */
    function countSubPositionModules($position, $pattern = null) {
        if (defined('GANTRY_FINALIZED')) return 0;
        global $wp_registered_sidebars, $wp_registered_widgets;

        $count = 0;
        gantry_import('core.renderers.gantrywidgetsrenderer');
        $sidebars_widgets = wp_get_sidebars_widgets();
        
		if (array_key_exists($position, $this->_aliases)) {
            return $this->countSubPositionModules($this->_aliases[$position]);	
       }

       if (!$this->isAdmin()) {
            if ($this->countModules($position)) {
                $count += count($sidebars_widgets[$position]);
            }
        }
        return $count;
    }
    
    function countWidgetsBeforeDivider($position) {
    	if (defined('GANTRY_FINALIZED')) return 0;
        global $wp_registered_sidebars, $wp_registered_widgets;
        
        gantry_import('core.renderers.gantrywidgetsrenderer');
        add_filter('sidebars_widgets', array('GantryWidgetsRenderer', 'filterWidgetCount'));
        
        $sidebars_widgets = wp_get_sidebars_widgets();
        $filtered_widgets = GantryWidgetsRenderer::filterWidgetCount($sidebars_widgets);
        
        $widgets = $filtered_widgets[$position];
        $position_count = 0;
    
    	if(count($widgets) > 0) {
	        foreach ($widgets as $widget) {
	        	if (!preg_match("/^gantrydivider/", $widget)) {
	                $position_count++;
	                if($position_count > 1) break;
	            } else {
					$position_count = 0;
	            }
	        }
		}
		
		return $position_count;
		
    }

	// wrapper for mainbody display
    function displayMainbody($bodyLayout = 'mainbody', $sidebarLayout = 'sidebar', $sidebarChrome = 'standard', $contentTopLayout = 'standard', $contentTopChrome = 'standard', $contentBottomLayout = 'standard', $contentBottomChrome = 'standard',  $gridsize = null) {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantrymainbodyrenderer');
        return GantryMainBodyRenderer::display($bodyLayout, $sidebarLayout, $sidebarChrome, $contentTopLayout, $contentTopChrome, $contentBottomLayout, $contentBottomChrome, $gridsize);
    }

    // wrapper for mainbody display
    function displayOrderedMainbody($bodyLayout = 'mainbody', $sidebarLayout = 'sidebar', $sidebarChrome = 'standard', $contentTopLayout = 'standard', $contentTopChrome = 'standard', $contentBottomLayout = 'standard', $contentBottomChrome = 'standard', $gridsize = null) {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantryorderedmainbodyrenderer');
        return GantryOrderedMainBodyRenderer::display($bodyLayout, $sidebarLayout, $sidebarChrome, $contentTopLayout, $contentTopChrome, $contentBottomLayout, $contentBottomChrome, $gridsize);
    }

    // wrapper for display modules
    function displayModules($positionStub, $layout = 'standard', $chrome = 'standard', $gridsize = GRID_SYSTEM, $pattern = null) {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantrywidgetsrenderer');
        return GantryWidgetsRenderer::display($positionStub, $layout, $chrome, $gridsize, $pattern);
    }
        // wrapper for display modules
    function displayFeature($feature, $layout = 'basic') {
        if (defined('GANTRY_FINALIZED')) return;
        gantry_import('core.renderers.gantryfeaturerenderer');
        return GantryFeatureRenderer::display($feature, $layout);
    }

    //
    function displayComments($seperate_comments = false, $layout = 'basic', $commentLayout ='basic') {
        if (defined('GANTRY_FINALIZED')) return;
        // check to see if there is a comments.php in the root

        if(file_exists($this->templatePath.'/comments.php')){
            comments_template('', $seperate_comments);
            return '';
        }
        comments_template($this->gantryPath.'/html/comments.php', $seperate_comments);

        // return empty of not using wordpress comments
        if (!$this->get('wordpress-comments', true)) return '';

        gantry_import('core.renderers.gantrycommentsrenderer');
        return GantryCommentsRenderer::display($layout, $commentLayout);

    }

    function getWidgetStyles() {
        return $this->_templateDetails->widget_styles;
    }
    
    function addTemp($namespace, $varname, &$variable) {
        if (defined('GANTRY_FINALIZED')) return;
        $this->_tmp_vars[$namespace][$varname] = $variable;
        return;
    }

    function &retrieveTemp($namespace, $varname, $default = null){
        if (defined('GANTRY_FINALIZED')) return;
        if (!array_key_exists($namespace,$this->_tmp_vars) ||!array_key_exists($varname, $this->_tmp_vars[$namespace])){
            return $default;
        }
        return  $this->_tmp_vars[$namespace][$varname];
    }

    function setBodyId($id = null){
    	$this->_bodyId = $id;
    }

    function addBodyClass($class) {
        if (defined('GANTRY_FINALIZED')) return;
    	$this->_bodyclasses[] = $class;
    }

    function addClassByTag($id , $class) {
        if (defined('GANTRY_FINALIZED')) return;
    	$this->_classesbytag[$id][] = $class;
    }

    function displayHead() {
        if (defined('GANTRY_FINALIZED')) return;
		foreach($this->_gizmos as $gizmo){
            $gizmo_instance = $this->_getGizmo($gizmo);
            if ($gizmo_instance->isEnabled() && method_exists( $gizmo_instance , 'render')) {
                $gizmo_instance->render();
            }
        }

        do_action( 'get_header', null );
        
        echo "<gantry:header/>";
    }

    function displayFooter() {
        if (defined('GANTRY_FINALIZED')) return;
        do_action( 'get_footer', null);
        echo "<gantry:footer/>";
    }

    function _displayHead(&$output) {
        // get line endings
		$lnEnd = "\12";
		$tab = "\11";
		$tagEnd	= ' />';
        $strHtml = '';


        // Enqueue Styles
        $deps=array();
		foreach ($this->_styles as $style_priority)
		{
            foreach ($style_priority as $strSrc) {
                if ($strSrc->type == 'local'){
                    $path = parse_url($strSrc->url, PHP_URL_PATH);
                    if ($this->baseUrl != "/"){
                        $path = '/'.preg_replace('#^'.quotemeta($this->baseUrl).'#',"",$path);
                    }
                    $filename = strtolower(basename($path, '.css')) . rand(0,1000);
                    wp_enqueue_style($filename, $path, array(), '1.26');
                    $deps[]=$path;
                }
            }
		}

        // Add scripts to the header
        $deps=array();
		foreach ($this->_scripts as  $strSrc) {
            $path = parse_url($strSrc, PHP_URL_PATH);
            if ($this->baseUrl != "/"){
                    $path = '/'.preg_replace('#^'.quotemeta($this->baseUrl).'#',"",$path);
            }
            wp_enqueue_script($path, $path, $deps, '1.26');
            $deps[]=$path;
		}
        foreach ($this->_full_scripts as $strSrc) {
            wp_enqueue_script( $strSrc, $strSrc, $deps, '1.26');
            $deps[]=$strSrc;
		}

        if (!$this->isAdmin()){
            $strHtml .= $this->_renderCharset();
            $strHtml .= $this->_renderTitle();
            add_action('wp_head', array($this,'_renderRemoteStyles'), 8);
            add_action('wp_head', array($this,'_renderRemoteScripts'), 9);
            ob_start();
            wp_head();
            $strHtml .= ob_get_clean();
            $strHtml .= $this->_renderStylesHead();
            $strHtml .= $this->_renderScriptsHead();
        }
        else {
            ob_start();
            $this->_renderRemoteStyles();
            print_admin_styles();
            $this->_renderRemoteScripts();
            print_head_scripts();
            $strHtml .= ob_get_clean();
            $strHtml .= $this->_renderStylesHead();
            $strHtml .= $this->_renderScriptsHead();
        }

        $output = preg_replace("#<gantry:header/>#", $strHtml, $output);
    }

    function _displayFooter(&$output) {
        ob_start();
        if (!$this->isAdmin()) wp_footer();
        $strHtml .= ob_get_clean();
        $output = preg_replace("#<gantry:footer/>#", $strHtml, $output);
    }

    function _renderRemoteStyles(){
         ob_start();
        foreach ($this->_styles as $style_priority)
		{
            foreach ($style_priority as $strSrc) {
                if ($strSrc->type == 'url'){
                    echo sprintf('<link rel="stylesheet" href="%s" type="text/css"/>', $strSrc->url);
                }
            }
		}
        echo ob_get_clean();
    }
    function _renderRemoteScripts(){
        ob_start();
        foreach ($this->_scripts as $strSrc) {
            if ($strSrc->type == 'url'){
                echo sprintf('<script  type="text/javascript" src="%s"></script>', $strSrc->url);
            }
        }
        echo ob_get_clean();
    }

    function _renderCharset() {
        $charset = '<meta http-equiv="Content-Type" content="' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset') . '" />'."\n";
        return $charset;
    }

    function _renderTitle() {
		if ($this->isAdmin()) return "";
		
        if (!isset($this->pageTitle)) {
            $this->pageTitle = wp_title('&raquo;', false);
        }
        
        $this->pageTitle = str_replace('$', '\$', $this->pageTitle);
        $title = '<title>' . $this->pageTitle . '</title>' . chr(13);
        return $title;
    }

    function _renderScriptsHead(){
        // get line endings
		$lnEnd = "\12";
		$tab = "\11";
		$tagEnd	= ' />';
        $strHtml = '';



         // Generate inline script
        if (isset($this->_inline_script) && strlen(trim($this->_inline_script)) > 0){
            $strHtml .= $tab.'<script type="text/javascript">'.$lnEnd;
            // This is for full XHTML support.
            $strHtml .= $this->_inline_script.$lnEnd;
            $strHtml .= $tab.'</script>'.$lnEnd;
        }

         // Generate domready script
        if (isset($this->_domready_script) && !empty($this->_domready_script) && count($this->_domready_script)){
            $strHtml .= $tab.'<script type="text/javascript">//<![CDATA['.$lnEnd;
            // This is for full XHTML support.
            $strHtml .= 'window.addEvent(\'domready\', function() {'.$this->_domready_script.$lnEnd.'});';
            $strHtml .= $tab.'//]]></script>'.$lnEnd;
        }

		// Generate load script
		if (isset($this->_loadevent_script) && !empty($this->_loadevent_script) && count($this->_loadevent_script)){
			$strHtml .= $tab.'<script type="text/javascript">//<![CDATA['.$lnEnd;
            // This is for full XHTML support.
            $strHtml .= 'window.addEvent(\'load\', function() {'.$this->_loadevent_script.$lnEnd.'});';
            $strHtml .= $tab.'//]]></script>'.$lnEnd;
		}


        return $strHtml;
    }

    function _renderStylesHead(){
        // get line endings
		$lnEnd = "\12";
		$tab = "\11";
		$tagEnd	= ' />';
        $strHtml = '';

        // Generate inline css
        if (isset($this->_inline_style) && strlen(trim($this->_inline_style)) > 0 ) {
            $strHtml .= $tab.'<style type="text/css">'.$lnEnd;
            // This is for full XHTML support.
            $strHtml .= $tab.$tab.'<!--'.$lnEnd;
            $strHtml .= $this->_inline_style . $lnEnd;
            $strHtml .= $tab.$tab.'-->'.$lnEnd;
            $strHtml .= $tab.'</style>'.$lnEnd;
        }
        return $strHtml;
    }

    function displayBodyTag() {
        if (defined('GANTRY_FINALIZED')) return;
        echo "<gantry:bodytag/>";
    }

    function _displayBodyTag(&$output){
        $body_classes = get_body_class();
        foreach ($this->_bodyclasses as $param) {
        	$param_value = $this->get($param);
        	if ($param_value != "") {
            	$body_classes[] = strtolower(str_replace(" ","-",$param ."-".$param_value));
            } else {
            	$body_classes[] = strtolower(str_replace(" ","-",$param));
            }
        }
        $body_tag = $this->renderLayout('doc_body', array('classes'=>implode(" ", $body_classes),'id'=>$this->_bodyId));
        $output = preg_replace("#<gantry:bodytag/>#", $body_tag, $output);
    }

    function displayClassesByTag($tag) {
        if (defined('GANTRY_FINALIZED')) return;
        $tag_classes = array();

        $output = "";

        if (array_key_exists($tag,$this->_classesbytag)) {
            foreach ($this->_classesbytag[$tag] as $param) {
                $param_value = $this->get($param);
                if ($param_value != "") {
                    $tag_classes[] = $param ."-".$param_value;
                } else {
                    $tag_classes[] = $param;
                }


            }
            $output = 'class="'.implode(" ", $tag_classes).'"';

        }
        return $this->renderLayout('doc_tag', array('classes'=>implode(" ", $tag_classes)));
    }

    // debug function for body
    function debugMainbody($bodyLayout = 'debugmainbody', $sidebarLayout = 'sidebar', $sidebarChrome = 'standard') {
        gantry_import('core.renderers.gantrydebugmainbodyrenderer');
        return GantryDebugMainBodyRenderer::display($bodyLayout, $sidebarLayout, $sidebarChrome);
    }

    	/* ------ Stylesheet Funcitons  ----------- */

    function addStyle($file = '', $priority=10, $template_files_override = false) {
        if (defined('GANTRY_FINALIZED')) return;
        if (is_array($file)) return $this->addStyles($file, $priority);
        $type = 'css';

        $template_path = $this->templatePath.DS .$type.DS;
        $template_url = $this->templateUrl.'/css/';
        $gantry_path = $this->gantryPath.DS.$type.DS;
        $gantry_url = $this->gantryUrl.'/css/';

        $gantry_first_paths = array(
            $gantry_url => $gantry_path,
            $template_url => $template_path
        );
        $template_first_paths = array_reverse($gantry_first_paths,true);

        $out_files = array();
        $ext = substr($file, strrpos($file, '.'));
        $filename = basename($file, $ext);
        $base_file = basename($file);
        $override_file = $filename . "-override" . $ext;

        // get browser checks and remove base files
        $checks = $this->_getBrowserBasedChecks(basename($file));
        unset($checks[array_search($base_file,$checks)]);

        $override_checks = $this->_getBrowserBasedChecks(basename($override_file));
        unset($override_checks[array_search($override_file,$override_checks)]);

        // check to see if this is a full path file
        $dir = dirname($file);
        if ($dir != ".") {
            // Add full url directly to document
            if (preg_match('/^http/', $file)) {
                $link = new GantryStyleLink('url','',$file);
                $this->_styles[$priority][]=$link;
                return;
            }

            // process a url passed file and browser checks
            $url_path = $dir;
            $file_path = $this->_getFilePath($file);
            $file_parent_path = dirname($file_path);

            if (file_exists($file_parent_path) && is_dir($file_parent_path)) {
                $base_path = preg_replace("/\?(.*)/", '', $file_parent_path.DS.$base_file);
                // load the base file
                if (file_exists($base_path) && is_file($base_path) && is_readable($base_path)){
                   $out_files[$base_path] = new GantryStyleLink('local',$base_path, $file);
                }
                foreach ($checks as $check) {
                    $check_path = preg_replace("/\?(.*)/", '', $file_parent_path . DS . $check);
                    $check_url_path = $url_path . "/" . $check;
                    if (file_exists($check_path) && is_readable($check_path)) {
                        $out_files[$check] = new GantryStyleLink('local',$check_path, $check_url_path);
                    }
                }
            }
        }
        else {
            $base_override = false;
            $checks_override = array();

            // Look for an base override file in the template dir
            $template_base_override_file = $template_path.$override_file;
            if (file_exists($template_path) && is_dir($template_path) && file_exists($template_base_override_file) && is_file($template_base_override_file)){
                $out_files[$template_base_override_file] = new GantryStyleLink('local',$template_base_override_file, $template_url.$override_file);
                $base_override = true;
            }

            // look for overrides for each of the browser checks
            foreach($override_checks as $check_index => $override_check) {
                $template_check_override = preg_replace("/\?(.*)/", '', $template_path.$override_check);
                $checks_override[$check_index] = false;
                if (file_exists($template_path) && is_dir($template_path) && file_exists($template_check_override) && is_file($template_check_override)){
                    $checks_override[$check_index] = true;
                    if ($base_override){
                         $out_files[$template_check_override] = new GantryStyleLink('local',$template_check_override,$template_url.$override_check);
                    }
                }
            }

            if (!$base_override){
                // Add the base files if there is no  base -override
                foreach ($gantry_first_paths as $base_url => $path) {
                    // Add the base file
                    $base_path = preg_replace("/\?(.*)/", '', $path.$base_file);
                    // load the base file
                    if (file_exists($base_path) && is_file($base_path) && is_readable($base_path)){
                       $outfile_key = ($template_files_override)? $base_file : $base_path;
                       $out_files[$outfile_key] = new GantryStyleLink('local',$base_path,$base_url.$base_file);
                    }

                    // Add the browser checked files or its override
                    foreach($checks as $check_index => $check) {
                        // replace $check with the override if it exists
                        if ($checks_override[$check_index]){
                            $check = $override_checks[$check_index];
                        }

                        $check_path = preg_replace("/\?(.*)/", '', $path.$check);

                        if (file_exists($check_path) && is_file($check_path) && is_readable($check_path) ){
                            $outfile_key = ($template_files_override)? $check : $check_path;
                            $out_files[$outfile_key] = new GantryStyleLink('local',$check_path,$base_url.$check);
                        }
                    }
                }
            }
        }

        foreach ($out_files as $link) {
            $addit = true;
            foreach($this->_styles as $style_priority => $priority_links){
                $index = array_search($link, $priority_links);
                if ($index !== false){
                    if ($priority < $style_priority){
                        unset($this->_styles[$style_priority][$index]);
                    }
                    else {
                        $addit = false;
                    }
                }
            }
            if ($addit) {
                $this->_styles[$priority][] = $link;
            }
        }

        //clean up styles
        foreach($this->_styles as $style_priority => $priority_links){
            if (count($priority_links) == 0){
                unset($this->_styles[$style_priority]);
            }
        }
    }

	function addStyles($styles = array(),$priority=10) {
        if (defined('GANTRY_FINALIZED')) return;
		foreach($styles as $style) $this->addStyle($style, $priority);
	}

	function addInlineStyle($css = '') {
        if (defined('GANTRY_FINALIZED')) return;
		if (!isset($this->_inline_style)) {
			$this->_inline_style = $css;
		} else {
			$this->_inline_style .= chr(13).$css;
		}
	}

	function addScript($file = '') {
        if (defined('GANTRY_FINALIZED')) return;
		if (is_array($file)) return $this->addScripts($file);
        //special case for main JS libs
        if ($file == 'mootools.js'){
            wp_enqueue_script($file);
            return;
        }
        $type = 'js';


        // check to see if this is a full path file
        $dir = dirname($file);
        $scripturl = GantryUrl::explode($file);
        $base = GantryUrl::explode(get_option('siteurl'));
		$same_domain = ($scripturl['host'] == $base['host'] && preg_match('#^'.$base['path'].'#',$scripturl['path']));

        if ($dir != ".") {
            // full url
            if (($scripturl['scheme'] == 'http' || $scripturl['scheme'] == 'https') && !$same_domain) {
                 $this->_full_scripts[] = $file;
                return;
            }

			if ($same_domain) {
				$dir = dirname($scripturl['path']);
			}


            // For local url path get the local path based on checks
            $url_path = $dir;
            $file_path = $this->_getFilePath($file);
            $url_file_checks = $this->platform->getJSChecks($file_path, true);
            foreach ($url_file_checks as $url_file){
                $full_path = realpath($url_file);
                if ($full_path !== false && file_exists($full_path)){
                    $check_url_path = $url_path.'/'.basename($url_file);
                    $this->_scripts[$full_path] = $check_url_path;
                    break;
                }
            }
            return;
        }

        $out_files = array();

        $paths = array(
           $this->templateUrl => $this->templatePath.DS.$type,
           $this->gantryUrl => $this->gantryPath.DS.$type
        );

		$checks = $this->platform->getJSChecks($file);
        foreach($paths as  $baseurl => $path){
            if (file_exists($path) && is_dir($path)){
                foreach($checks  as $check) {
                    $check_path = preg_replace("/\?(.*)/",'',$path.DS.$check);
                    $check_url_path = $baseurl ."/".$type."/".$check;
                    if (file_exists($check_path) && is_readable($check_path)){
                        $this->_scripts[$check_path] = $check_url_path;
                        break(2);
                    }
                }
            }
        }
	}



	function addScripts($scripts = array()) {
        if (defined('GANTRY_FINALIZED')) return;
		foreach($scripts as $script) $this->addScript($script);
	}

	function addInlineScript($js = '') {
		if (defined('GANTRY_FINALIZED')) return;
        if (!isset($this->_inline_script)) {
			$this->_inline_script = $js;
		} else {
			$this->_inline_script .= chr(13).$js;
		}
    }

	function addDomReadyScript($js = '') {
		if (defined('GANTRY_FINALIZED')) return;
        if (!isset($this->_domready_script)) {
			$this->_domready_script = $js;
		} else {
			$this->_domready_script .= chr(13).$js;
		}
    }

	function addLoadScript($js = '') {
		if (defined('GANTRY_FINALIZED')) return;
        if (!isset($this->_loadevent_script)) {
			$this->_loadevent_script = $js;
		} else {
			$this->_loadevent_script .= chr(13).$js;
		}
    }

    /**
     * @param $path
     * @return void
     */
    function addContentTypePath($path)
    {
        if (!empty($path) && is_dir($path))
        {
            array_unshift($this->_contentTypePaths,$path);
        }
    }

    /**
     * @return array
     */
    function getContentTypePaths()
    {
        if (empty($this->_contentTypePaths))
            $this->_initContentTypePaths();
        return $this->_contentTypePaths;
    }

    /**
     * @return void
     */
    function _initContentTypePaths(){
        if (empty($this->_contentTypePaths))
        {
            $this->_contentTypePaths[] = $this->templatePath. '/html';
            $this->_contentTypePaths[] = $this->gantryPath. '/html';
        }
    }

    function readMenuItemParams($id, $asArray = false){
        $outstring = '';

        if (!array_key_exists($id, $this->_menu_item_params)){
            $menu_items_title = 'menu_item_overrides';
            $prefix = "menuitemparam";
            $menu_params_file = $this->custom_menuitemparams_dir.DS.$id.'.menuparams.ini';
            if (file_exists($menu_params_file) && is_readable($menu_params_file)){
                $outarray = GantryINI::read($menu_params_file, $menu_items_title, $prefix);
                if ($outarray != null){
                    $this->_menu_item_params[$id] = &$outarray;
                }
            }
        }
        if (array_key_exists($id, $this->_menu_item_params)) {
            $outarray = &$this->_menu_item_params[$id];
            if ($asArray) return $outarray;
            if (count($outarray)>0) {
                $parts = array();
                foreach($outarray as $paramname => $paramvalue) {
                    $parts[] = $paramname."=".$paramvalue;
                }
                $outstring = implode("\n",$parts);
            }
         }
        return $outstring;
    }

    function writeMenuItemParams($id, $data){
        $menu_items_title = 'menu_item_overrides';
        $prefix = "menuitemparam";

        if (file_exists($this->custom_menuitemparams_dir)){

            $menu_params_file = $this->custom_menuitemparams_dir.DS.$id.'.menuparams.ini';
            if (is_array($data)){
                $in_data = array($menu_items_title=>array($prefix=>$data));
                GantryINI::write($menu_params_file,$in_data,false);
            }
        }
    }

    function clearOverrides(){
        $this->_override_tree = array();
    }

    function addOverrides($overrides, $priority){
        if (!array($overrides)){
            $overrides = array($overrides);
        }
        $catalog = gantry_get_override_catalog($this->templateName);
        foreach($overrides as $override){
            if (array_key_exists($override, $catalog)){
                $this->_override_tree[] = new GantryOverrideItem($override,$priority,0,_g('Added by template function'));
            }
        }
        $this->_override_tree = GantryOverrides::sortOverridesList($this->_override_tree);
        $this->reset();
    }

    /**
     * @param string $layout the layout name to render
     * @param array $params all parameters needed for rendering the layout as an associative array with 'parameter name' => parameter_value
     * @return void
     */
    function renderLayout($layout_name, $params=array()){
        $layout = $this->_getLayout($layout_name);
        if ($layout === false){
            return "<!-- Unable to render layout... can not find layout class for " . $layout_name . " -->";
        }
        return $layout->render($params);
    }


    /**#@+
     * @access private
     */

    /**
     * @param  $url
     * @return string
     */
    function _getFilePath($url) {
        $parsedurl = parse_url(get_option('siteurl'));
        $base       = $parsedurl['scheme']."://".$parsedurl['host'];
        if (array_key_exists('port', $parsedurl))   $base .=':'.$parsedurl['port'];
        $path       = preg_replace("#/$#","",$this->baseUrl);
	    if ($url && $base && strpos($url,$base)!==false) $url = preg_replace('#^'.quotemeta($base).'#',"",$url);
	    if ($url && $path && strpos($url,$path)!==false) $url = preg_replace('#^'.quotemeta($path).'#',"",$url);
	    if (substr($url,0,1) != DS) $url = DS.$url;
	    $filepath = preg_replace("#/$#","",$this->basePath).$url;
	    return $filepath;
	}

    /**
     * internal util function to get key from schema array
     * @param  $schemaArray
     * @return #Fimplode|?
     */
    function _getKey($schemaArray) {

        $concatArray = array();

        foreach ($schemaArray as $key=>$value) {
            $concatArray[] = $key . $value;
        }

        return (implode("-",$concatArray));
    }


    /**
     * @return #M#Vdb.loadResult|#P#Vdefault_item.id|int|?
     */
    function _getDefaultMenuItem(){
        if (!$this->isAdmin()){
            $menu   =& JSite::getMenu();
            $default_item = $menu->getDefault();
            return $default_item->id;
        }
        else
        {
            $db		=& JFactory::getDBO();
            $default = 0;
            $query = 'SELECT id'
                . ' FROM #__menu AS m'
                . ' WHERE m.home = 1';

            $db->setQuery( $query );
            $default = $db->loadResult();
            return $default;
        }
    }

    /**
     * @return void
     */
    function _loadConfig() {
        // Process the config
        $default_config_file = $this->gantryPath.DS.'gantry.config.php';
        if (file_exists($default_config_file) && is_readable($default_config_file)){
             include_once($default_config_file);
        }

        $template_config_file = $this->templatePath.DS.'gantry.config.php';
        if (file_exists($template_config_file   ) && is_readable($template_config_file)){
            /** @define "$template_config_file" "VALUE" */
            include_once($template_config_file);
        }

        if (isset($gantry_default_config_mapping)) {
           $temp_array = array_merge($this->_config_vars, $gantry_default_config_mapping);
           $this->_config_vars = $temp_array;
        }
        if (isset($gantry_config_mapping)){
           $temp_array = array_merge($this->_config_vars, $gantry_config_mapping);
           $this->_config_vars = $temp_array;
        }

        foreach($this->_config_vars as $config_var_name =>$class_var_name){
            $default_config_var_name = 'gantry_default_'.$config_var_name;
            if (isset($$default_config_var_name)){
                $this->$class_var_name = $$default_config_var_name;
                $this->__cacheables[] = $class_var_name;
            }
            $template_config_var_name = 'gantry_'.$config_var_name;
            if (isset($$template_config_var_name)){
                $this->$class_var_name = $$template_config_var_name;
                $this->__cacheables[] = $class_var_name;
            }
        }
    }

    function _loadWidgetPositions() {
        $positions = $this->getUniquePositions();


        if ( function_exists('register_sidebars') )
        {
            foreach ($positions  as $position) {
                $positionInfo = $this->getPositionInfo($position);
                register_sidebars(1, array(
                    'name' => _g($positionInfo->name),
                    'id' => $positionInfo->id,
                    'description' => _g($positionInfo->description),
                    'before_widget' => '',
                    'after_widget' => '',
                    'before_title' => '',
                    'after_title' => '',
                ));
            }
        }
    }

    /**
     * Gets the xml config for all gantry widgets
     * @return void
     */
    function _getWidgetConfigs() {
        gantry_import('core.config.gantryform');

        $form_paths = array(
            $this->gantryPath . DS . 'widgets',
            $this->templatePath . DS . 'widgets'
        );
        foreach ($form_paths as $form_path) {
            if (file_exists($form_path) && is_dir($form_path)) {
                GantryForm::addFormPath($form_path);
            }
        }

        $field_paths = array(
            $this->gantryPath . DS . 'admin/forms/fields',
            $this->templatePath . DS . 'admin/forms/fields'
        );
        foreach ($field_paths as $field_path) {
            if (file_exists($field_path) && is_dir($field_path)) {
                GantryForm::addFieldPath($field_path);
            }
        }

        $group_paths = array(
            $this->gantryPath . DS . 'admin/forms/groups',
            $this->templatePath . DS . 'admin/forms/groups'
        );
        foreach ($group_paths as $group_path) {
            if (file_exists($group_path) && is_dir($group_path)) {
                GantryForm::addGroupPath($group_path);
            }
        }
    }
    /**
     * Load up any Browser config values set in the gantry.config.php files
     * @return void
     */
    function _loadBrowserConfig() {
        $checks = $this->browser->_checks;
        foreach($checks as $check){
            if (array_key_exists($check, $this->_browser_params)){
                foreach($this->_browser_params[$check] as $param_name => $param_value) {
                    $this->set($param_name, $param_value);
                }
            }
        }
    }

    /**
     * @param array $ignore
     * @return string
     */
	function _rebuildQueryString($ignore=array()) {
	  if (!empty($_SERVER['QUERY_STRING'])) {
	      $parts = explode("&", $_SERVER['QUERY_STRING']);
	      $newParts = array();
	      $qs = '';

	      foreach ($parts as $val) {
	          $val_parts = explode("=", $val);
	          if (!in_array($val_parts[0], $this->_setbyurl) && !in_array($val_parts[0], $this->_ignoreQueryParams) && !in_array($val_parts[0], $ignore)) {
	          	if (empty($val_parts[1])) $val_parts[1]='';
	          	$newParts[$val_parts[0]] = $val_parts[1];
	          }
	      }
	      $newqs = array();
	      foreach ($newParts as $newparam => $newval) {
	      	if (!empty($newval)){
	      		$newqs[] = $newparam."=".$newval;
	      	}
	      	else {
	      		$newqs[] = $newparam;
	      	}
	      }

	      if (count($newqs) != 0) {
	          $qs = implode("&amp;", $newqs);
	      } else {
	          return "?";
	      }

	      return "?" . $qs . "&amp;"; // this is your new created query string
	  } else {
	      return "?";
	  }
	}


    /**
     * @return void
     */
	function _customPresets() {
		$this->originalPresets = $this->presets;
		if (file_exists($this->custom_presets_file)) {

			$customPresets = GantryINI::read($this->custom_presets_file);
			$this->customPresets = $customPresets;
			$this->originalPresets = $this->presets;
			if (count($customPresets)) {
				$this->presets = $this->_array_merge_replace_recursive($this->presets, $customPresets);
				foreach($this->presets as $key => $preset) {
					uksort($preset, array($this, "_compareKeys"));
					$this->presets[$key] = $preset;
				}
			}

		}
	}

    /**
     * @param  $key1
     * @param  $key2
     * @return int
     */
	function _compareKeys($key1, $key2) {
		if (strlen($key1) < strlen($key2)) return -1;
		else if (strlen($key1) > strlen($key2)) return 1;
		else {
			if ($key1 < $key2) return -1;
			else return 1;
		}
	}

    /**
     * @param  $name
     * @param  $preset
     * @return array
     */
	function _getPresetParams($name,$preset){
		$return_params = array();
        if (array_key_exists($preset,$this->presets[$name])){
		    $preset_params = $this->presets[$name][$preset];
            foreach ($preset_params as $preset_param_name => $preset_param_value) {
                if (array_key_exists($preset_param_name, $this->_working_params) && $this->_working_params[$preset_param_name]['type'] == 'preset') {
                    $return_params = $this->_getPresetParams($preset_param_name,$preset_param_value);
                }
            }
            foreach ($preset_params as $preset_param_name => $preset_param_value) {
                if (array_key_exists($preset_param_name, $this->_working_params) && $this->_working_params[$preset_param_name]['type'] != 'preset') {
                    $return_params[$preset_param_name] = $preset_param_value;
                }
            }
        }
		return $return_params;
	}

    /**
     * @return void
     */
	function _populateParams(){
        gantry_import('core.params.gantryurlparams');
        gantry_import('core.params.gantrysessionparams');
        gantry_import('core.params.gantrycookieparams');
        gantry_import('core.params.gantryoverrideparams');

        // get a copy of the params for working with on this call
		$this->_working_params = $this->_templateDetails->params;

        //$reset =  get_query_var('reset-settings');

        if (!isset($_REQUEST['reset-settings'])) {
            GantrySessionParams::populate();
            GantryCookieParams::populate();
        }

        GantryOverrideParams::populate();

        if (!isset($_REQUEST['reset-settings'])) {
            GantryUrlParams::populate();
        }
	}

	/**
     * @param  $position
     * @return array
     */
    function _getFeaturesForPosition($position) {
   		$return = array();
   		// Init all features
		foreach($this->_features as $feature){
            $feature_instance = $this->_getFeature($feature);
			if ($feature_instance->isEnabled() && $feature_instance->isInPosition($position) && method_exists( $feature_instance , 'render')) {
				$return[] = $feature;
			}
		}
		return $return;
    }

    /**
     * internal util to get short name from long name
     * @param  $longname
     * @return string
     */
    function _getShortName($longname) {
        $shortname = $longname;
        if (strlen($longname)>2) {
            $shortname = substr($longname,0,1) . substr($longname,-1);
        }
        return $shortname;
    }

    /**
     * internal util to get long name from short name
     * @param  $shortname
     * @return string
     */
    function _getLongName($shortname) {
        $longname = $shortname;
        switch (substr($shortname,0,1)) {
            case "s":
            default:
                $longname = "sidebar";
                break;
        }
        $longname .= "-".substr($shortname,-1);
        return $longname;
    }


    /**
     * internal util to retrieve the prefix of a position
     * @param  $position
     * @return #Fsubstr|?
     */
	function _getPositionPrefix($position) {
		return substr($position, 0, strrpos($position, "-"));
	}

	/**
     * internal util to retrieve the stored position schema
     * @param  $position
     * @param  $gridsize
     * @param  $count
     * @param  $index
     * @return #P#CGantry.layoutSchemas|boolean|?
     */
	function _getPositionSchema($position, $gridsize, $count, $index) {
		$param = $position . '-layout';
        $defaultSchema = false;

		$storedParam = $this->get($param);
		if (!preg_match("/{/", $storedParam)) $storedParam = '';
		$setting = unserialize($storedParam);

 		$schema =& $setting[$gridsize][$count][$index];
 		if (isset($schema))
            return $schema;
		else {
            if (count($this->layoutSchemas[$gridsize]) < $count){
                $count = count($this->layoutSchemas[$gridsize]);
            }
            for ($i=$count;$i>0;$i--) {
				$layout = $this->layoutSchemas[$gridsize][$i];
                if (isset($layout[$index])) {
                    $defaultSchema = $layout[$index];
                    break;
                }
            }
            return $defaultSchema;
        }
	}


    /**
     * @param  $filename
     * @return
     */
    function _getBrowserBasedChecks($file, $keep_path=false) {
        $ext = substr($file, strrpos($file, '.'));
        $path = ($keep_path)?dirname($file).DS:'';
        $filename = basename($file, $ext);

        $checks = $this->browser->getChecks($file, $keep_path);

        // check if RTL version needed
        $document =& $this->document;
        if (get_bloginfo('text_direction') == 'rtl' && $this->get('rtl-enabled')) {
            $checks[] = $path.$filename . '-rtl'.$ext;
        }
        return $checks;
    }

    /**
     * @return
     */
    function _getCurrentTemplate() {
        if (defined('TEMPLATEPATH')) {
            return basename(TEMPLATEPATH);
        }
        elseif ( function_exists('get_template') )
        {
            return get_template();
        }
        else {
            return false;
        }
    }

    /**
     * @param  $condition
     * @return
     */
	function _adminCountModules($condition)
	{
		$result = '';

		$words = explode(' ', $condition);
		for($i = 0; $i < count($words); $i+=2)
		{
			// odd parts (modules)
			$name		= strtolower($words[$i]);
			$words[$i]	= ((isset($this->_buffer['modules'][$name])) && ($this->_buffer['modules'][$name] === false)) ? 0 : count($this->_getModulesFromAdmin($name));
		}
		$str = 'return '.implode(' ', $words).';';
		return eval($str);
	}

	/**
	 * Get modules by position
	 *
	 * @param string 	$position	The position of the module
	 * @return array	An array of module objects
	 */
	function &_getModulesFromAdmin($position)
	{
		$position	= strtolower( $position );
		$result		= array();

		$modules = $this->_loadModulesFromAdmin();

		$total = count($modules);
		for($i = 0; $i < $total; $i++) {
			if($modules[$i]->position == $position) {
				$result[] =& $modules[$i];
			}
		}
		return $result;
	}

	/**
     * @return #M#Vdb.loadObjectList|array|boolean|?
     */
	function _loadModulesFromAdmin()
	{
		static $modules;

		if (isset($modules)) {
			return $modules;
		}

		$db		=& JFactory::getDBO();

		$modules = array();

        $wheremenu =   ' AND ( mm.menuid = '. (int) $this->currentMenuItem .' OR mm.menuid = 0 )';

        $query = 'SELECT id, position'
            . ' FROM #__modules AS m'
            . ' LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id'
            . ' WHERE m.published = 1'
            . ' AND m.access <= 0'
            . ' AND m.client_id = 0'
            . $wheremenu
            . ' ORDER BY position, ordering';

		$db->setQuery( $query );
		if (null === ($modules = $db->loadObjectList())) {
            JError::raiseWarning( 'SOME_ERROR_CODE', JText::_( 'Error Loading Modules' ) . $db->getErrorMsg());
            return false;
		}

		$total = count($modules);
		for($i = 0; $i < $total; $i++)
		{
			$modules[$i]->position	= strtolower($modules[$i]->position);
		}
		return $modules;
	}

    /**
     * @return void
     */
    function _loadFeatures(){
         $feature_paths = array(
            $this->templatePath.DS.'features',
            $this->gantryPath.DS.'features'
         );

        $raw_features = array();
        foreach($feature_paths as  $feature_path){
            if (file_exists($feature_path) && is_dir($feature_path)){
                $d = dir($feature_path);
                while (false !== ($entry = $d->read())) {
                    if($entry != '.' && $entry != '..'){
                        $feature_name = basename($entry, ".php");
                        $path	= $feature_path.DS.$feature_name.'.php';
                        $className = 'GantryFeature'.ucfirst($feature_name);
                        if (!class_exists($className)) {
                            if (file_exists( $path ))
                            {
                                require_once( $path );
                                if(class_exists($className))
                                {
                                    $raw_features[$feature_name] = $feature_name;
                                }
                            }

                        }
                    }
                }
                $d->close();
            }
        }

        $ordered_feature_string = $this->get('features-order');
        $ordered_features = explode(",",$ordered_feature_string);
        foreach ($ordered_features as $ordered_feature) {
            if (array_key_exists($ordered_feature, $raw_features)){
                $this->_features[$ordered_feature] = $ordered_feature;
            }
        }
        foreach ($raw_features as $feature){
            if (!in_array($feature,  $this->_features)){
                $this->_features[$feature] = $feature;
            }
        }
    }

    /**
     * @return void
     */
    function _loadGizmos(){
         $gizmo_paths = array(
            $this->templatePath.DS.'gizmos',
            $this->gantryPath.DS.'gizmos'
         );

        $raw_gizmos = array();
        foreach($gizmo_paths as  $gizmo_path){
            if (file_exists($gizmo_path) && is_dir($gizmo_path)){
                $d = dir($gizmo_path);
                while (false !== ($entry = $d->read())) {
                    if($entry != '.' && $entry != '..'){
                        $gizmo_name = basename($entry, ".php");
                        $path	= $gizmo_path.DS.$gizmo_name.'.php';
                        $className = 'GantryGizmo'.ucfirst($gizmo_name);
                        if (!class_exists($className)) {
                            if (file_exists( $path ))
                            {
                                require_once( $path );
                                if(class_exists($className))
                                {
                                    $raw_gizmos[$this->get($gizmo_name."-priority",10)][] = $gizmo_name;
                                }
                            }

                        }
                    }
                }
                $d->close();
            }
        }

        ksort($raw_gizmos);
        foreach($raw_gizmos as $gizmos){
            foreach ($gizmos as $gizmo){
                if (!in_array($gizmo,  $this->_gizmos)){
                    $this->_gizmos[$gizmo] = $gizmo;
                }
            }
        }
    }

        /**
     * @return void
     */
    function _loadWidgets(){
         $widget_paths = array(
            $this->templatePath.DS.'widgets',
            $this->gantryPath.DS.'widgets'
         );

        $widgets = array();
        foreach($widget_paths as  $widget_path){
            if (file_exists($widget_path) && is_dir($widget_path)){
                $d = dir($widget_path);
                while (false !== ($entry = $d->read())) {
                    if($entry != '.' && $entry != '..'){
                        $widget_name = basename($entry, ".php");
                        $path	= $widget_path.DS.$widget_name.'.php';
                        $plugin = $path;
                        $className = 'GantryWidget'.ucfirst($widget_name);
                        if (!class_exists($className)) {
                            if (file_exists( $path ))
                            {
                                require_once( $path );
                                if(class_exists($className))
                                {
                                    $this->_widgets[$widget_name] = $className;
                                }
                            }

                        }
                    }
                }
                $d->close();
            }
        }
    }

    function _initWidgets() {
        foreach($this->_widgets as $widgetClass) {
            add_action('widgets_init', array($widgetClass,"init"));
        }
    }

    /**
     * @return void
     */
    function _loadAjaxModels(){
         $models_paths = array(
            $this->templatePath.DS.'ajax-models',
            $this->gantryPath.DS.'ajax-models'
         );
        $this->_loadModels($models_paths, $this->_ajaxmodels);
        return;
    }

    function _loadAdminAjaxModels(){
         $models_paths = array(
            $this->templatePath.DS.'admin'.DS.'ajax-models',
            $this->gantryPath.DS.'admin'.DS.'ajax-models'
         );
        $this->_loadModels($models_paths, $this->_adminajaxmodels);
        return;
    }

    function _loadModels($paths, &$results){
        $raw_models = array();
        foreach($paths as  $model_path){
            if (file_exists($model_path) && is_dir($model_path)){
                $d = dir($model_path);
                while (false !== ($entry = $d->read())) {
                    if($entry != '.' && $entry != '..'){
                        $model_name = basename($entry, ".php");
                        $path	= $model_path.DS.$model_name.'.php';
                        if (file_exists( $path ) && !array_key_exists($model_name, $results))
                        {
                            $results[$model_name] = $path;
                        }
                    }
                }
                $d->close();
            }
        }
    }


    /**
     * @param  $feature_name
     * @return boolean
     */
    function _getFeature($feature_name){
        $className = 'GantryFeature'.ucfirst($feature_name);

        if (!class_exists($className)){
            $this->_loadFeatures();
        }

        if (class_exists($className))
        {
            return new $className();
        }
        return false;
    }

    /**
     * @param  $gizmo_name
     * @return boolean
     */
    function _getGizmo($gizmo_name){
        $className = 'GantryGizmo'.ucfirst($gizmo_name);

        if (!class_exists($className)){
            $this->_loadGizmos();
        }

        if (class_exists($className))
        {
            return new $className();
        }
        return false;
    }

    function _loadLayouts(){
         $layout_paths = array(
            $this->templatePath.DS.'html'.DS.'layouts',
            $this->gantryPath.DS.'html'.DS.'layouts'
         );

        $raw_layouts = array();
        foreach($layout_paths as  $layout_path){
            if (file_exists($layout_path) && is_dir($layout_path)){
                $d = dir($layout_path);
                while (false !== ($entry = $d->read())) {
                    if($entry != '.' && $entry != '..'){
                        $layout_name = basename($entry, ".php");
                        $path	= $layout_path.DS.$layout_name.'.php';
                        $className = 'GantryLayout'.ucfirst($layout_name);
                        if (!class_exists($className)) {
                            if (file_exists( $path ))
                            {
                                require_once( $path );
                                if(class_exists($className))
                                {
                                   $this->_layouts[$layout_name] = $className;
                                }
                            }
                        }
                    }
                }
                $d->close();
            }
        }
    }

    function _getLayout($layout_name){
        $className = 'GantryLayout'.ucfirst($layout_name);
        if (!class_exists($className)){
            $this->_loadLayouts();
        }

        if (class_exists($className))
        {
            return new $className();
        }
        return false;
    }

    /**
     * @param  $schema
     * @return array
     */
    function _flipBodyPosition($schema) {

    	$backup = array_keys($schema);
    	$backup_reverse = array_reverse($schema);
    	$reverse = array_reverse($backup);

    	$pos = array_search('mb',$backup);

    	unset($backup[$pos]);

  		$new_keys = array();
  		$new_schema = array();

		reset($backup);
  		foreach($reverse as $value) {
  			if ($value != 'mb')	{
  				$value = current($backup);
  				next($backup);
  			}
  			$new_keys[] = $value;
  		}

  		reset($backup_reverse);
  		foreach ($new_keys as $key) {
  			$new_schema[$key] = current($backup_reverse);
  			next($backup_reverse);
  		}
    	return $new_schema;
    }

    /**
     * @param  $array1
     * @param  $array2
     * @return
     */
	function _array_merge_replace_recursive( &$array1,  &$array2) {
		$merged = $array1;

		foreach($array2 as $key => $value) {
			if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
				$merged[$key] = $this->_array_merge_replace_recursive($merged[$key], $value);
			}
			else {
				$merged[$key] = $value;
			}
		}

		return $merged;
	}

    function _loadOverrideEngine() {
        $_override_engine = new GantryOverridesEngine();
        $_override_engine->init($this->templateName);
        return $_override_engine;
    }


    /**#@-*/

    function getCookiePath(){
        $cookieUrl = '';
        if (!empty($this->baseUrl)){
            if (substr($this->baseUrl, -1, 1) == '/') {
                $cookieUrl = substr($this->baseUrl, 0, -1);
            }
            else {
                $cookieUrl = $this->baseUrl;
            }
        }
        return $cookieUrl;
    }

}


