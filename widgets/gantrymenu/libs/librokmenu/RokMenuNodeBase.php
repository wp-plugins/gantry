<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!class_exists('RokMenuNodeBase')) {

    /**
     *
     */
    class RokMenuNodeBase {
        /**
         * Base ID for the menu  as ultimate parent
         */
        protected $id = 0;
        protected $parent = 0;
        protected $_parentRef = null;
        protected $level = -1;
        protected $_children = array();

        /**
         * Gets the id
         * @access public
         * @return integer
         */
        public function getId() {
            return $this->id;
        }

        /**
         * Sets the id
         * @access public
         * @param integer $id
         */
        public function setId($id) {
            $this->id = $id;
        }

        /**
         * Gets the level
         * @access public
         * @return integer
         */
        public function getLevel() {
            return $this->level;
        }

        /**
         * Sets the level
         * @access public
         * @param integer $level
         */
        public function setLevel($level) {
            $this->level = $level;
        }


        /**
         * Gets the parent
         * @access public
         * @return integer
         */
        public function getParent() {
            return $this->parent;
        }

        /**
         * Sets the parent
         * @access public
         * @param integer $parent
         */
        public function setParent($parent) {
            $this->parent = $parent;
        }

        public function setChildren($children){
            $this->_children = $children;
        }
        
        public function addChild(RokMenuNode &$node) {
            if ($this->id == $node->getParent()) {
                $node->setParentref($this);
                $this->_children[$node->getId()] = & $node;
                $node->setLevel($this->level + 1);
                return true;
            }
            else if ($this->hasChildren()) {
                reset($this->_children);
                while (list($key, $value) = each($this->_children)) {
                    $child =& $this->_children[$key];
                    if ($child->addChild($node)) {
                        return true;
                    }
                }
            }
            return false;
        }

        public function hasChildren() {
            return count($this->_children);
        }

        public function &getChildren() {
            return $this->_children;
        }

        public function &findChild($node_id) {
            if (array_key_exists($node_id, $this->_children)) {
                return $this->_children[$node_id];
            }
            else if ($this->hasChildren()) {
                reset($this->_children);
                while (list($key, $value) = each($this->_children)) {
                    $child =& $this->_children[$key];
                    $wanted_node = $child->findChild($node_id);
                    if ($wanted_node !== false) {
                        return $wanted_node;
                    }
                }
            }
            $ret = false;
            return $ret;
        }

        public function removeChild($node_id) {
            if (array_key_exists($node_id, $this->_children)) {
                unset($this->_children[$node_id]);
                return true;
            }
            else if ($this->hasChildren()) {
                reset($this->_children);
                while (list($key, $value) = each($this->_children)) {
                    $child =& $this->_children[$key];
                    $ret = $child->removeChild($node_id);
                    if ($ret === true) {
                        return $ret;
                    }
                }
            }
            return false;
        }

        public function removeLevel($end) {
            if ($this->level == $end) {
                $this->_children = array();
            }
            else if ($this->level < $end) {
                if ($this->hasChildren()) {
                    reset($this->_children);
                    while (list($key, $value) = each($this->_children)) {
                        $child =& $this->_children[$key];
                        $child->removeLevel($end);
                    }
                }
            }
        }

        public function removeIfNotInTree(&$active_tree, $last_active) {
            if (!empty($active_tree)) {

                if (in_array((int) $this->id, $active_tree) && $last_active == $this->id) {
                    // i am the last node in the active tree
                    if ($this->hasChildren()) {
                        reset($this->_children);
                        while (list($key, $value) = each($this->_children)) {
                            $child =& $this->_children[$key];
                            $child->_children = array();
                        }
                    }
                }
                else if (in_array((int) $this->id, $active_tree)) {
                    // i am in the active tree but not the last node
                    if ($this->hasChildren()) {
                        reset($this->_children);
                        while (list($key, $value) = each($this->_children)) {
                            $child =& $this->_children[$key];
                            $child->removeIfNotInTree($active_tree, $last_active);
                        }
                    }
                }
                else {
                    // i am not in the active tree
                    $this->_children = array();
                }
            }
        }

        public function getParentRef() {
            return $this->_parentRef;
        }

        public function setParentref(RokmenuNodeBase & $paentRef) {
            $this->_parentRef = &$paentRef;
        }
    }
}