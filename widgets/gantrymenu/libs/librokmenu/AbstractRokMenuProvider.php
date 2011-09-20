<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

require_once(dirname(__FILE__) . '/RokMenuProvider.php');

if (!class_exists('AbstractRokMenuProvider')) {

    /**
     * The base class for all data providers for menus
     */
    abstract class AbstractRokMenuProvider implements RokMenuProvider {

        protected $args = array();
        protected $active_branch = array();
        protected $current_node = 0;

        public function __construct($args) {
            $this->args = $args;
        }

        public function getActiveBranch() {
            return $this->active_branch;
        }

        public function getCurrentNodeId() {
            return $this->current_node;
        }

        protected function populateActiveBranch($nodeList) {
            // setup children array to find parents and children
            $children = array();
            $list = array();
            foreach ($nodeList as $node) {

                $thisref = &$children[$node->getId()];
                $thisref['parent_id'] = $node->getParent();
                if ($node->getParent() == 0) {
                    $list[$node->getId()] = &$thisref;
                } else {
                    $children[$node->getParent()]['children'][] = $node->getId();
                }
            }
            // Find active branch
            if ($this->current_node != 0) {
                $this->active_branch[$this->current_node] = $nodeList[$this->current_node];
                $parent_id = $children[$this->current_node]['parent_id'];
                while ($parent_id != 0) {
                    $this->active_branch[$parent_id] = $nodeList[$parent_id];
                    $parent_id = $children[$parent_id]['parent_id'];
                }
            }
        }
    }
}
