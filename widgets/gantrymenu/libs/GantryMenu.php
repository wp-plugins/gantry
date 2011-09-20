<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

if (class_exists('GantryRokMenu')) return;

class GantryMenu extends RokMenu {
    protected function getProvider() {
        global $gantry;
        $providerClass = "GantryMenuProvider". ucfirst($gantry->platform->platform);
        $file = dirname(__FILE__) . '/providers/' . $providerClass . '.php';
        if (!class_exists($providerClass) && file_exists($file)) {
            require_once($file);
        }
        if (class_exists($providerClass)) {
            return new $providerClass($this->args);
        }
        else {
            return false;
        }
    }

    public function enqueueHeaderFiles() {
        global $gantry;
        foreach ($this->layout->getScriptFiles() as $name => $script) {
            $gantry->addScript($script['url']);
        }
        foreach ($this->layout->getStyleFiles() as $name => $style) {
            $gantry->addScript($style['url']);
        }
    }

    public function renderInlineHeader() {
        global $gantry;
        $style = $this->layout->getInlineStyle();
        if (!empty($style)) {
            $gantry->addInlineStyle($style);
        }
        $js = $this->layout->getInlineScript();
        if (!empty($js)) {
            $gantry->addInlineScript($js);
        }
        return;
    }
}
