<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!interface_exists('RokMenuFormatter')) {
    /**
     *
     */
    interface RokMenuFormatter {
        public function __construct($args);

        public function format_tree(&$menu);

        public function format_menu(&$menu);

        public function format_subnode(&$node);

        public function setActiveBranch($active_branch);

        public function setCurrentNodeId($current_node);
    }
}