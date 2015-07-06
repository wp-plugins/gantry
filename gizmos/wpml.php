<?php
/**
 * @version   $Id: wpml.php 60800 2014-05-07 13:08:13Z jakub $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined( 'GANTRY_VERSION' ) or die();

gantry_import( 'core.gantrygizmo' );

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryGizmoWPML extends GantryGizmo {

	var $_name = 'wpml';

	function isEnabled() {
		if( class_exists( 'SitePress' ) ) {
			return true;
		}

		return false;
	}

	/**
	 *     Copyright (C) 2014 Jakub Baran
	 */

	function init() {
		/** @global $gantry Gantry */
		global $gantry, $sitepress;

		if( isset( $sitepress ) ) {

			// add WPML language conditional to WP_Query
			add_action( 'parse_query', array( &$this, 'query_add_wpml_language_conditionals' ) );

			// remove widgets that won't get displayed (for current language) from the widget list to make layout appear properly
			add_filter( 'gantry_renderer_filtered_widgets', array( &$this, 'remove_unnecessary_widgets_from_list' ) );
		}

	}

	function admin_init() {
		/** @global $gantry Gantry */
		global $gantry, $sitepress;

		if( isset( $sitepress ) ) {

			// add WPML languages selection to the Assignments tabs
			add_action( 'gantry_assignment_custom_meta_boxes', array( &$this, 'gantry_assignment_wpml_languages_meta_boxes' ) );

			// re-registers ICL Language Switcher widget to extend it for missing form needed for widget variations
			add_action( 'widgets_init', array( &$this, 'reregister_language_switcher_widget' ), 11 );

			// remove possibility of converting regular text widgets to multilingual ones so it wouldn't break Gantry widget structure
			if( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE ) {
				remove_action( 'in_widget_form', 'icl_widget_text_in_widget_form_hook', 10 );
			}

		}
	}

	/**
	 * add WPML language conditional to WP_Query
	 */
	function query_add_wpml_language_conditionals( $wp_query ) {
		global $sitepress;

		$current_language  = $sitepress->get_current_language();
		if( isset( $current_language ) ) {
			$query_conditional = 'is_wpml_lang_' . $current_language;
			$wp_query->$query_conditional = true;
		}
	}

	/**
	 * add WPML languages selection to the Assignments tabs
	 */
	function gantry_assignment_wpml_languages_meta_boxes() {
		$type               = new AssignmentType();
		$type->archetype    = "wpmllang";
		$type->type_label   = _g( 'WPML Languages' );
		$type->single_label = _g( 'WPML Language' );
		$type->name         = _g( 'WPML Language' );
		add_meta_box( $type->archetype, _g( 'WPML Languages' ), array( $this, 'gantry_assignment_wpml_languages_meta_box' ), 'gantry_assignments', 'panel', 'low', $type );
	}

	/**
	 * @param $object
	 * @param $box
	 * @param $assignments
	 *
	 * walk through the WPML languages and make a list of them
	 */
	function gantry_assignment_wpml_languages_meta_box( $object, $box, $assignments ) {
		global $sitepress;

		$wpml_languages = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
		if( !$wpml_languages ) return;

		$wpml_languages = apply_filters( 'gantry_admin_assignments_wpml_languages_list', $wpml_languages );

		if( !empty( $wpml_languages ) && is_array( $wpml_languages ) ) {
			$language_list = array();

			foreach( $wpml_languages as $lang_code => $lang_params ) {
				$item                  = new AssignmentItem();
				$item->archetype       = $box['args']->archetype;
				$item->type            = $lang_code;
				$item->title           = $lang_params['translated_name'];
				$item->parent_id       = 0;
				$item->single_label    = 'WPML Language';
				$language_list[] = $item;
			}
			$args['assignments'] = $assignments;
			$walker              = new GantryAssignmentWalker();

			?>
			<div id="wpml-languages-list" class="wpmllangdiv">
				<ul id="wpml-list checklist" class="list:wpml-languages-list categorychecklist form-no-clear">
					<?php
					$args['walker'] = $walker;
					echo gantry_walk_assignment_tree( $language_list, 0, (object)$args );
					?>
				</ul>
			</div>
		<?php
		}

	}

	/**
	 * Remove widgets that won't get displayed (for current language) from the widget list to make layout appear properly
	 *
	 * @param $widgets
	 *
	 * @return mixed
	 */
	function remove_unnecessary_widgets_from_list( $widgets ) {
		global $gantry;

		if( class_exists( 'WP_Widget_Text_Icl' ) || class_exists( 'WPML_Widgets' ) ) {
			if( !empty( $widgets ) ) {
				foreach( $widgets as $widget_id ) {
					$widget_instance = $this->getWidgetInstanceParams( $widget_id );
					if( ( isset( $widget_instance['icl_language'] ) && ( $widget_instance['icl_language'] != 'multilingual' && $widget_instance['icl_language'] != ICL_LANGUAGE_CODE ) )
				        || ( isset( $widget_instance['wpml_language'] ) && ( $widget_instance['wpml_language'] != 'all' && $widget_instance['wpml_language'] != ICL_LANGUAGE_CODE ) ) ) {
						$found = array_search( $widget_id, $widgets );
						unset( $widgets[ $found ] );
					}
				}
			}
		}

		return $widgets;
	}

	// Re-registers ICL Language Switcher widget to extend it for missing form needed for widget variations
	function reregister_language_switcher_widget() {
		unregister_widget( 'ICL_Language_Switcher' );
		register_widget( 'Gantry_ICL_Language_Switcher' );
	}

	// Helper function to get the widget instance
	function getWidgetInstanceParams( $widget_id ) {
		global $wp_registered_widgets;
		$widget_info =& $wp_registered_widgets[$widget_id];

		if ( is_array( $widget_info['callback'] ) ) {
			$widget =& $widget_info['callback'][0];
			if ( is_object( $widget ) && $widget instanceof WP_Widget ) {
				$instances       = $widget->get_settings();
				$instance_params = $instances[$widget_info['params'][0]['number']];
			}
		} else {
			$instance_params = $wp_registered_widgets[$widget_id]['params'];
		}
		if ( empty( $instance_params ) ) $instance_params = array();
		return $instance_params;
	}

}

/**
 * Extend ICL Language Switcher widget with form
 */
if( class_exists( 'ICL_Language_Switcher' ) ) {
	class Gantry_ICL_Language_Switcher extends ICL_Language_Switcher {
		public function form( $args ) {
			echo '<p>' . __( 'There are no options for this widget.' ) . "</p>\n";
		}
	}
}