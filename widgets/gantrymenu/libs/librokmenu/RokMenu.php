<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


require_once(dirname(__FILE__) . '/RokMenuNodeTree.php');
require_once(dirname(__FILE__) . '/RokMenuNode.php');
require_once(dirname(__FILE__) . '/RokMenuNodeBase.php');
require_once(dirname(__FILE__) . '/RokMenuFormatter.php');
require_once(dirname(__FILE__) . '/AbstractRokMenuFormatter.php');
require_once(dirname(__FILE__) . '/RokMenuLayout.php');
require_once(dirname(__FILE__) . '/AbstractRokMenuLayout.php');
require_once(dirname(__FILE__) . '/RokMenuProvider.php');
require_once(dirname(__FILE__) . '/AbstractRokMenuProvider.php');
require_once(dirname(__FILE__) . '/RokMenuTheme.php');
require_once(dirname(__FILE__) . '/AbstractRokMenuTheme.php');

if (!class_exists('RokMenu')) {

    /**
     *
     */
    abstract class RokMenu {
        protected $theme;
        protected $args;
        protected $formatter;
        protected $layout;
        protected $menu;
        protected $provider;

        protected static $menu_defaults = array(
            'limit_levels' => 0,
            'startLevel' => 0,
            'endLevel' => 0,
            'showAllChildren' => 1,
            'maxdepth' => 10
        );

        public function __construct(RokMenuTheme $theme, $args) {
            $this->theme = $theme;

            // get defaults for theme
            $theme_defaults = $this->theme->getDefaults();

            // merge theme defaults with class defaults theme defaults overrding
            $defaults = array_merge(self::$menu_defaults, $theme_defaults);

            // merge defaults into passed args   passed args overriding
            $this->args = array_merge($defaults, $args);

            $this->formatter = $this->theme->getFormatter($this->args);
            $this->layout = $this->theme->getLayout($this->args);
            $this->provider = $this->getProvider();
        }

        public static function getDefaults() {
            return self::$menu_defaults;
        }

        public function getArgs() {
            return $this->args;
        }

        public function getTheme() {
            return $this->theme;
        }

        public function initialize() {
            $nodes = $this->provider->getMenuItems();
            $this->menu = $this->createMenuTree($nodes);
            if (!empty($this->menu) && $this->menu !== false) {
                $this->formatter->setActiveBranch($this->provider->getActiveBranch());
                $this->formatter->setCurrentNodeId($this->provider->getCurrentNodeId());
                $this->formatter->format_tree($this->menu);
            }
        }

        public function render() {
            $output = '';
            if (!empty($this->menu) && $this->menu !== false) {
                $output = $this->layout->renderMenu($this->menu);
            }
            return $output;
        }

        public function renderHeader() {
            $this->layout->doStageHeader();
        }

        protected function createMenuTree(&$nodes) {
            if (!empty($nodes)) {
                $menu = new RokMenuNodeTree();
                $maxdepth = $this->args['maxdepth'];
                // Build Menu Tree root down (orphan proof - child might have lower id than parent)
                $ids = array();
                $ids[0] = true;
                $last = null;
                $unresolved = array();
                // pop the first item until the array is empty if there is any item
                if (is_array($nodes)) {
                    while (count($nodes) && !is_null($row = array_shift($nodes)))
                    {
                        if (!$menu->addNode($row)) {
                            if (!array_key_exists($row->getId(), $unresolved) || $unresolved[$row->getId()] < $maxdepth) {
                                array_push($nodes, $row);
                                if (!isset($unresolved[$row->getId()])) $unresolved[$row->getId()] = 1;
                                else $unresolved[$row->getId()]++;
                            }
                        }
                    }
                }
            }
            return $menu;
        }

        protected abstract function getProvider();
    }
}

