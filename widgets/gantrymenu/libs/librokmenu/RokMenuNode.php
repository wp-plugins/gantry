<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

require_once(dirname(__FILE__) . '/RokMenuNodeBase.php');

if (!class_exists('RokMenuNode')) {

    /**
     * RokMenuNode
     */
    class RokMenuNode extends RokMenuNodeBase {
        protected $title = null;
        protected $link = null;
        protected $cssId = null;
        protected $target = null;

        protected $attributes = array();

        protected $_link_additions = array();
        protected $_link_attribs = array();

        protected $_li_classes = array();
        protected $_a_classes = array();
        protected $_span_classes = array();

        /**
         * Gets the title
         * @access public
         * @return string
         */
        function getTitle() {
            return $this->title;
        }

        /**
         * Sets the title
         * @access public
         * @param string $title
         */
        function setTitle($title) {
            $this->title = $title;
        }

        public function setLink($link) {
            $this->link = $link;
        }

        public function hasLink() {
            return (isset($this->link));
        }

        public function getLink() {
            $outlink = $this->link;
            $outlink .= $this->getLinkAdditions(!strpos($this->link, '?'));
            return $outlink;
        }

        /**
         * Gets the css_id
         * @access public
         * @return string
         */
        public function getCssId() {
            return $this->cssId;
        }

        /**
         * Sets the css_id
         * @access public
         * @param string $cssId
         */
        public function setCssId($cssId) {
            $this->cssId = $cssId;
        }

        /**
         * Gets the target
         * @access public
         * @return string the target
         */
        public function getTarget() {
            return $this->target;
        }

        /**
         * Sets the target
         * @access public
         * @param string the target $target
         */
        public function setTarget($target) {
            $this->target = $target;
        }

        public function addAttribute($key, $value) {
            $this->attributes[$key] = $value;
        }

        public function getAttribute($key) {
            if (array_key_exists($key, $this->attributes))
                return $this->attributes[$key];
            else
                return false;
        }

        public function getAttributes() {
            return $this->attributes;
        }

        public function addLinkAddition($name, $value) {
            $this->_link_additions[$name] = $value;
        }

        public function getLinkAdditions($starting_query = false, $starting_seperator = false) {
            $link_additions = " ";
            reset($this->_link_additions);
            $i = 0;
            while (list($key, $value) = each($this->_link_additions)) {
                $link_additions .= (($i == 0) && $starting_query) ? '?' : '';
                $link_additions .= (($i == 0) && !$starting_query) ? '&' : '';
                $link_additions .= ($i > 0) ? '&' : '';
                $link_additions .= $key . '=' . $value;
                $i++;
            }
            return rtrim(ltrim($link_additions));
        }

        public function hasLinkAdditions() {
            return count($this->_link_additions);
        }

        public function addLinkAttrib($name, $value) {
            $this->_link_attribs[$name] = $value;
        }

        public function getLinkAttribs() {
            $link_attribs = " ";
            reset($this->_link_attribs);
            while (list($key, $value) = each($this->_link_attribs)) {
                $link_attribs .= $key . "='" . $value . "' ";
            }
            return rtrim(ltrim($link_attribs));
        }

        public function hasLinkAttribs() {
            return count($this->_link_attribs);
        }

        public function getListItemClasses() {
            $html_classes = " ";
            reset($this->_li_classes);
            while (list($key, $value) = each($this->_li_classes)) {
                $class =& $this->_li_classes[$key];
                $html_classes .= $class . " ";
            }
            return rtrim(ltrim($html_classes));
        }

        public function addListItemClass($class) {
            $this->_li_classes[] = $class;
        }

        public function hasListItemClasses() {
            return count($this->_li_classes);
        }

        public function getLinkClasses() {
            $html_classes = " ";
            reset($this->_a_classes);
            while (list($key, $value) = each($this->_a_classes)) {
                $class =& $this->_a_classes[$key];
                $html_classes .= $class . " ";
            }
            return rtrim(ltrim($html_classes));
        }

        public function addLinkClass($class) {
            $this->_a_classes[] = $class;
        }

        public function hasLinkClasses() {
            return count($this->_a_classes);
        }

        public function getSpanClasses() {
            $html_classes = " ";
            reset($this->_span_classes);
            while (list($key, $value) = each($this->_span_classes)) {
                $class =& $this->_span_classes[$key];
                $html_classes .= $class . " ";
            }
            return rtrim(ltrim($html_classes));
        }

        public function addSpanClass($class) {
            $this->_span_classes[] = $class;
        }

        public function hasSpanClasses() {
            return count($this->_span_classes);
        }

        public function addChild(RokMenuNode &$node) {
            //$ret = parent::addChild($node);
            $ret = false;

            if ($this->id == $node->getParent()) {
                $node->setParentref($this);
                $this->_children[$node->getId()] = & $node;
                $node->setLevel($this->level + 1);
                $ret = true;
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
            if ($ret === true) {
                if (!array_search('parent', $this->_li_classes)) {
                    $this->addListItemClass('parent');
                }
            }
            return $ret;
        }
    }
}
