<?php
/**
 * @version   1.26 September 14, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!class_exists('GantryMenuProviderWordpress')) {

    class GantryMenuProviderWordpress extends AbstractRokMenuProvider {

        protected $current_url;
        const PREFIX = "gantrymenu_";

        public function __construct(&$args) {
            parent::__construct($args);
            $this->current_url = $this->currentPageURL();
        }

        function getMenuItems() {
            $nav_menu_name = $this->args['nav_menu'];
            if (wp_get_nav_menu_object($nav_menu_name) == false) return array();
            $menu_items = wp_get_nav_menu_items($nav_menu_name);
            $outputNodes = array();
            foreach ($menu_items as $menu_item) {
                //Create the new Node
                $node = new RokMenuNode();
                $node->setId($menu_item->ID);
                $node->setParent($menu_item->menu_item_parent);
                $node->setTitle($menu_item->title);
                $node->setLink($menu_item->url);
                $node->setTarget($menu_item->target);
                if (!empty($menu_item->description)) $node->addAttribute('description', $menu_item->description);
                if (!empty($menu_item->xfn)) $node->addLinkAttrib('rel', $menu_item->xfn);
                if (!empty($menu_item->attr_title)) $node->addLinkAttrib('title', $menu_item->attr_title);

                foreach ($menu_item->classes as $miclass) {
                    $node->addListItemClass($miclass);
                }

                $menu_item_vars = get_object_vars($menu_item);
                foreach ($menu_item_vars as $menu_item_var => $menu_item_value) {
                    if (preg_match('/^' . self::PREFIX . '(\w+)$/', $menu_item_var, $matches)) {
                        $node->addAttribute($matches[1], $menu_item_value);
                    }
                }
                $node->addListItemClass("item" . $node->getId());
                $node->addSpanClass('menuitem');
                if ($node->getLink() == $this->current_url && $this->current_node == 0) $this->current_node = $node->getId();
                $outputNodes[$node->getId()] = $node;
            }
            $this->populateActiveBranch($outputNodes);
            return $outputNodes;
        }

        private function currentPageURL() {
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }
    }
}
