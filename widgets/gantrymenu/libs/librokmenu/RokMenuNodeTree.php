<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


require_once(dirname(__FILE__) . '/RokMenuNodeBase.php');
require_once(dirname(__FILE__) . '/RokMenuNode.php');

if (!class_exists('RokMenuNodeTree')) {

    /**
     * Rok Nav Menu Tree Class.
     */
    class RokMenuNodeTree extends RokMenuNodeBase {
        function addNode(RokMenuNode $node) {
            // Get menu item data
            //$node = $this->_getItemData($item);
            if ($node !== false) {
                return $this->addChild($node);
            }
            else {
                return true;
            }
        }

        function resetTop($top_node_id) {
            $new_top_node = $this->findChild($top_node_id);
            if ($new_top_node !== false) {
                $this->id = $new_top_node->id;
                $this->_children = $new_top_node->getChildren();
            }
            else {
                return false;
            }
        }
    }
}
