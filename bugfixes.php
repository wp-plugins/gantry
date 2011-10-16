<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


if (!get_option('gantry_bugfix_WGANTRYFW_5')) {

    add_action('wp', 'gantry_bugfix_WGANTRYFW_5', 1000);
    add_action('wp', 'gantry_bugfix_redirect', 1001);

    function gantry_bugfix_WGANTRYFW_5()
    {
        global $gantry;
        global $wp_registered_widget_updates;

        $widget_names = array_keys($wp_registered_widget_updates);
        foreach ($widget_names as $widget) {
            remove_filter('option_widget_' . $widget, 'gantry_setup_override_widget_instances_intercept', -1000);
        }
        foreach ($widget_names as $widget) {
            $widget_option = 'widget_' . $widget;
            $instances = get_option($widget_option);
            $working_instances = $instances;
            foreach ($instances as $id => $instance)
            {
                if (is_int($id) && $id > 10000) {
                    unset($working_instances[$id]);
                }
            }
            update_option($widget_option, $working_instances);
        }

        $override_catalog = gantry_get_override_catalog($gantry->templateName);
        if (!empty($override_catalog)) {
            foreach ($override_catalog as $override_id => $override_widgets_option_name) {
                $override_widgets_option_name = $gantry->templateName . '-template-options-override-widgets-' . $override_id;
                $override_sidebar_name = $gantry->templateName . '-template-options-override-sidebar-' . $override_id;

                // get the widgets in the override
                $override_widget_settings = get_option($override_widgets_option_name);


                // Clean up the non override id'd widgets
                $working_overrides = $override_widget_settings;
                if ($override_widget_settings !== false) {
                    foreach ($override_widget_settings as $override_widget_type => $override_widget_instances)
                    {
                        foreach ($override_widget_instances as $owid => $owinstance)
                        {
                            if ($owid < $override_id * 10000 || $owid > ($override_id + 1) * 10000) {
                                unset($working_overrides[$override_widget_type][$owid]);
                            }
                        }
                    }
                }



                $remaps = array();
                // find widget_ids to renumber
                $override_sidebar = get_option($override_sidebar_name);
                foreach ($override_sidebar as $position => &$sbw_instances)
                {
                    foreach ($sbw_instances as $position_id => &$widget_id)
                    {
                        $side_bar_widget_type = substr($widget_id, 0, strrpos($widget_id, '-'));
                        $widget_type_option_name = 'widget_'.$side_bar_widget_type;
                        $id_number = substr($widget_id, strrpos($widget_id, '-') + 1);
                        if ($id_number < $override_id * 10000 || $id_number > ($override_id + 1) * 10000) {
                            $keys = array_keys($working_overrides[$widget_type_option_name]);
                            if (count($keys) == 0) $keys[$override_id*10000+2] = $override_id*10000+2;
                            $new_id = max($keys)+1;
                            $widget_id = $side_bar_widget_type.'-'.$new_id;
                            $remaps[$widget_type_option_name][$id_number]=$new_id;
                        }
                    }
                }
                update_option($override_sidebar_name, $override_sidebar);


                $cleaned_overrides = $override_widget_settings;
                if ($override_widget_settings !== false) {
                    foreach ($override_widget_settings as $override_widget_type => $override_widget_instances)
                    {
                        foreach ($override_widget_instances as $owid => $owinstance)
                        {
                            if (is_int($owid) && $owid != 2 && ($owid < $override_id * 10000 || $owid > ($override_id + 1) * 10000)) {
                                if (isset($remaps[$override_widget_type][$owid]))
                                {
                                    $cleaned_overrides[$override_widget_type][$remaps[$override_widget_type][$owid]] = $owinstance;
                                }
                                unset($cleaned_overrides[$override_widget_type][$owid]);
                            }
                        }
                    }
                }
                update_option($override_widgets_option_name, $cleaned_overrides);
            }
        }

        update_option('gantry_bugfix_WGANTRYFW_5', true);
    }
}

function gantry_bugfix_redirect()
{
    wp_redirect(stripslashes($_SERVER['REQUEST_URI']));
    die();
}