<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('GANTRY_VERSION') or die();

gantry_import('core.utilities.gantrytemplateinfo');

/**
 * Populates the parameters and template configuration form the templateDetails.xml and params.ini
 *
 * @package gantry
 * @subpackage core
 */
class GantryTemplateDetails {

	var $xml = null;
	var $positions = array();
	var $params = array ();
    var $template_info;
    var $widget_styles = array();

    var $_mainTemplateFile = '/templateDetails.xml';
    var $_pramas_ini = null;
    var $_template_settings = array();
    var $_params_content = array();
    var $_ingorables = array('spacer','gspacer','gantry');

    function __sleep()
    {
        return array('positions', 'params','widget_styles','_template_settings');
    }
    
	function GantryTemplateDetails() {
        add_filter('query_vars', array('GantryTemplateDetails','add_url_vars'));
	}

	function init(&$gantry) {
        gantry_import("core.utilities.gantrysimplexmlelement");

		$this->xml = new GantrySimpleXMLElement($gantry->templatePath . '/templateDetails.xml', null, true);
		if ($this->xml === false){
            // TODO: figure out way to return error properly
            echo "Unable to find templateDetails.xml file";
        }
        $this->positions = &$this->_getPositions();
        $tmp_options = get_option($gantry->templateName . '-template-options');
        if ($tmp_options !== false) {
            foreach($tmp_options as $option_name => $option_value) {
                $this->_addTemplateSettings($option_name, $option_value);
            }
        }
        $this->params = $this->_getParams($gantry);
        $this->template_info = $this->_getTemplateInfo();
        $this->widget_styles = $this->_getWidgetStyles();
	}

    function _addTemplateSettings($option_name, $option_value){
        if (!is_array($option_value)){
            $this->_template_settings[$option_name] = $option_value;
        }
        else {
            foreach ($option_value as $sub_option_name =>$sub_option_value){
                $this->_addTemplateSettings($option_name.'-'.$sub_option_name, $sub_option_value);
            }
        }
    }

	function & _getPositions() {
        $positions = array();
		//$xml_positions = $this->xml->document->positions[0]->children();
        $xml_positions = $this->xml->xpath('//positions/position');
		foreach ($xml_positions as $position) {
            $positionObject = new stdClass();
            $attrs = $position->attributes();
            $positionObject->name = (string)$attrs['name'];
            $positionObject->id = (string)$attrs['id'];
            $positionObject->max_positions = (int)((string)$attrs['max_positions']);
            $positionObject->description = $position->data();
            $positionObject->mobile = ((string)$attrs['mobile'] =='true')?true:false;
            $positions[$positionObject->id] = $positionObject;
		}
        return $positions;
	}

    function & _getWidgetStyles(){
        $style_types = array();
        $xml_stylegroups = $this->xml->xpath('//widget_styles/stylegroup');

        foreach ($xml_stylegroups as $style_group){
           $style_group_entry = array();
           $style_group_entry['label'] = (string)$style_group['label'];
           $style_group_entry['name'] = (string)$style_group['name'];
           $xml_styles = $this->xml->xpath('//widget_styles/stylegroup[@name="'.$style_group['name'].'"]/style');
           foreach($xml_styles as $style){
                $style_group_entry['styles'][(string)$style['name']] = (string)$style['label'];
           }
           $style_types[] = $style_group_entry;
        }
        return $style_types;
    }
	
	function getUniquePositions() {
        return array_keys($this->positions);
	}

    function getPositionInfo($position_name){
        return $this->positions[$position_name];
    }
    
    function parsePosition($position, $pattern) {
		if (null == $pattern) {
			$pattern = "(-)?";
		}
		$filtered_positions = array ();

		if (count($this->positions) > 0) {
			$regpat = "/^" . $position . $pattern . "/";
			foreach (array_keys($this->positions) as $value) {
				if (preg_match($regpat, $value) == 1) {
					$filtered_positions[] = $value;
				}
			}
		}
		return $filtered_positions;
	}

	function _getParams(&$gantry) {
		$this->_params_content=array();

		$this->_loadParamsContent($gantry);

		$data = array();
		//$params = $this->xml->document->config[0]->fields[0]->fieldset[0]->children();
        $params = $this->xml->xpath('//config/fields[@name="template-options"]//field');
        
		foreach ($params as $param) {
//            //skip for unsupported types
			if (in_array($param['type'], $this->_ingorables))
				continue;

            $attrs	= $param->xpath('ancestor::fields[@name]/@name');
			$groups	= array_map('strval', $attrs ? $attrs : array());
			$groups	= array_flip($groups);
            if (array_key_exists('template-options', $groups)) unset($groups['template-options']);
            $groups	= array_flip($groups);
            $prefix ='';
            foreach($groups as $parent){
                $prefix .= $parent."-";
            }
            $param_name = $prefix.$param['name'];
            $this->_getParamInfo($gantry, $param_name, $param, $data);
		}
		$this->params = $data;
		return $data;
	}

    /**
     * Loads the params.ini content
     * @param  $gantry
     * @return void
     */
    function _loadParamsContent(&$gantry){
		$templateOptions = get_option($gantry->templateName . "-template-options","");
        if (!empty($templateOptions) && $templateOptions === false)
		{
			$this->_params_content = $templateOptions;
            return true;
		}
        return false;
    }

    function getParamsHash(){
        return md5($this->_implode_with_key("&", $this->_params_content));
    }

    function _getParamInfo(&$gantry, $param_name, &$param, &$data, $prefix = ""){
        $this->_decodeParamInfo($gantry, $param_name, $param, $data, $prefix);
    }

    function _decodeParamInfo(&$gantry, $param_name, &$param, &$data, $prefix = ""){

        $attributes = array();
        foreach ( $param->attributes() as $key=>$val){
            $attributes[$key] = (string)$val;
        }

        $full_param_name = $prefix.$param_name;
         
        $data[$full_param_name] = array (
            'name' => $full_param_name,
            'type' => $attributes['type'],
            'default' => (array_key_exists('default',$attributes))?$attributes['default']:false,
            'value' => (array_key_exists($full_param_name, $this->_template_settings))?$this->_template_settings[$full_param_name]: (array_key_exists('default', $attributes)?$attributes['default']:false),
            'sitebase' => (array_key_exists($full_param_name, $this->_template_settings))?$this->_template_settings[$full_param_name]: (array_key_exists('default', $attributes)?$attributes['default']:false),
            'setbyurl' => (array_key_exists('setbyurl',$attributes))?($attributes['setbyurl'] == 'true')?true:false :false,
            'setbycookie' => (array_key_exists('setbycookie',$attributes))?($attributes['setbycookie'] == 'true')?true:false :false,
            'setbysession' => (array_key_exists('setbysession',$attributes))?($attributes['setbysession'] == 'true')?true:false :false,
            'setincookie' => (array_key_exists('setbycookie',$attributes))?($attributes['setbycookie'] == 'true')?true:false :false,
            'setinsession' => (array_key_exists('setinsession',$attributes))?($attributes['setinsession'] == 'true')?true:false :false,
            'setinoverride' => (array_key_exists('setinoverride',$attributes))?($attributes['setinoverride'] == 'true')?true:false :true,
            'setbyoverride' => (array_key_exists('setbyoverride',$attributes))?($attributes['setbyoverride'] == 'true')?true:false :true,
            'isbodyclass' => (array_key_exists('isbodyclass',$attributes))?($attributes['isbodyclass'] == 'true')?true:false :false,
            'setclassbytag' => (array_key_exists('setclassbytag',$attributes)) ? $attributes['setclassbytag'] : false,
            'setby' => 'default',
			'attributes' => &$attributes
        );

        if ($data[$full_param_name]['setbyurl']) $gantry->_setbyurl[] = $full_param_name;
        if ($data[$full_param_name]['setbysession']) $gantry->_setbysession[] = $full_param_name;
        if ($data[$full_param_name]['setbycookie']) $gantry->_setbycookie[] = $full_param_name;
        if ($data[$full_param_name]['setinsession']) $gantry->_setinsession[] = $full_param_name;
        if ($data[$full_param_name]['setincookie']) $gantry->_setincookie[] = $full_param_name;
        if ($data[$full_param_name]['setinoverride']) {
            $gantry->_setinoverride[] = $full_param_name;
        }
        else {
            $gantry->dontsetinoverride[] = $full_param_name;
        }
        if ($data[$full_param_name]['setbyoverride']) $gantry->_setbyoverride[] = $full_param_name;
        if ($data[$full_param_name]['isbodyclass']) $gantry->_bodyclasses[] = $full_param_name;
        if ($data[$full_param_name]['setclassbytag']) $gantry->_classesbytag[$data[$full_param_name]['setclassbytag']][] = $full_param_name;
        if ($attributes['type'] == 'alias') $gantry->_aliases[$full_param_name] = $data[$full_param_name]['value'];

    }

    function _implode_with_key($glue = null, $pieces, $hifen = ',') {
        $return = null;
        foreach ($pieces as $tk => $tv) $return .= $glue . $tk . $hifen . $tv;
        return substr($return, 1);
    }


    public static function add_url_vars($aVars) {
        global $gantry;
        foreach ($gantry->_setbyurl as $queryvar){
            $aVars[] = $queryvar;
        }
        $aVars[] = 'reset-settings';
        return $aVars;
    }

    function _getTemplateInfo(){
        $this->template_info = GantryTemplateInfo::getInstance();
        $tdata = $this->xml->xpath('//name');
        $this->template_info->setName($tdata[0]->data());
        $tdata =$this->xml->xpath('//version');
        $this->template_info->setVersion($tdata[0]->data());
        $tdata =$this->xml->xpath('//creationDate');
        $this->template_info->setCreationDate($tdata[0]->data());
        $tdata =$this->xml->xpath('//author');
        $this->template_info->setAuthor($tdata[0]->data());
        $tdata =$this->xml->xpath('//authorUrl');
        $this->template_info->setAuthorUrl($tdata[0]->data());
        $tdata =$this->xml->xpath('//authorEmail');
        $this->template_info->setAuthorEmail($tdata[0]->data());
        $tdata =$this->xml->xpath('//copyright');
        $this->template_info->setCopyright($tdata[0]->data());
        $tdata =$this->xml->xpath('//license');
        $this->template_info->setLicense($tdata[0]->data());
        $tdata =$this->xml->xpath('//description');
        $this->template_info->setDescription($tdata[0]->data());
    }
}