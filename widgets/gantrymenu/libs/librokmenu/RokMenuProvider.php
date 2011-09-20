<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!interface_exists('RokMenuProvider')) {

    /**
     * The base class for all data providers for menus
     */
    interface RokMenuProvider {
        /**
         * Gets an array of RokMenuNodes for that represent the menu items.  This should be a non hierarchical array.
         * @abstract
         * @return array of RokMenuNode objects
         */
        public function getMenuItems();

        public function getActiveBranch();

        public function getCurrentNodeId();
    }
}