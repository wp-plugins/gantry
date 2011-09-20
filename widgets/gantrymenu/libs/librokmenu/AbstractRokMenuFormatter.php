<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


require_once(dirname(__FILE__) . '/RokMenuFormatter.php');


if (!class_exists('AbstractRokMenuFormatter')) {
    /**
     *
     */
    abstract class AbstractRokMenuFormatter implements RokMenuFormatter {
        protected $active_branch = array();
        protected $args = array();
        protected $current_node = 0;


        public function __construct($args) {
            $this->args = $args;
        }

        public function setCurrentNodeId($current_node) {
            $this->current_node = $current_node;
        }

        public function setActiveBranch($active_branch) {
            $this->active_branch = $active_branch;
        }

        public function format_tree(&$menu) {
            if (!empty($menu) && $menu !== false) {
                if ($menu->hasChildren()) {
                    foreach ($menu->getChildren() as $child_node) {
                        $this->_format_subnodes($child_node);
                    }
                }
                $this->_default_format_menu($menu);
                $this->format_menu($menu);
            }
        }


        protected function _format_subnodes(&$node) {
            $this->_default_format_subnode($node);

            $this->format_subnode($node);

            if ($node->hasChildren()) {
                foreach ($node->getChildren() as $child_node) {
                    $this->_format_subnodes($child_node);
                }
            }
        }

        protected function _default_format_menu(&$menu) {
            // Limit the levels of the tree is called for By limitLevels
            $start = $this->args['startLevel'];
            $end = $this->args['endLevel'];


            if ($this->args['limit_levels']) {
                //Limit to the active path if the start is more the level 0
                if ($start > 0) {
                    $found = false;
                    // get active path and find the start level that matches
                    if (count($this->active_branch)) {
                        foreach ($this->active_branch as $active_child) {
                            if ($active_child->getLevel() == $start - 1) {
                                $menu->resetTop($active_child->getId());
                                $found = true;
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        $menu->setChildren(array());
                    }
                }
                //remove lower then the defined end level
                $menu->removeLevel($end);
            }

            if (!$this->args['showAllChildren']) {
                if ($menu->hasChildren()) {
                    $active = array_keys($this->active_branch);
                    foreach ($menu->getChildren() as $toplevel) {
                        if (array_key_exists($toplevel->getId(), $this->active_branch) !== false) {
                            end($active);
                            $toplevel->removeIfNotInTree($active, current($active));
                        }
                        else {
                            $toplevel->removeLevel($toplevel->getLevel());
                        }
                    }
                }
            }
        }

        protected function _default_format_subnode(&$node) {
            if ($node->getId() == $this->current_node) {
                $node->setCssId('current');
                $node->addListItemClass('active');
            }
            else if ($node->findChild($this->current_node) !== false) {
                $node->addListItemClass('active');
            }
        }

        public function format_menu(&$menu) {

        }
    }
}

