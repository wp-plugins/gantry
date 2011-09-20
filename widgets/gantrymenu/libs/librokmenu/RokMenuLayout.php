<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (!interface_exists('RokMenuLayout')) {

    /**
     *
     */
    interface RokMenuLayout {

        public function __construct($args);


        public function renderMenu(&$menu);

        public function getScriptFiles();

        public function getStyleFiles();

        public function getInlineStyle();

        public function getInlineScript();

        public function doStageHeader();

        public function stageHeader();
    }
}