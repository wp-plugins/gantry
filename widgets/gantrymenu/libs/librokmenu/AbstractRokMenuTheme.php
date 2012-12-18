<?php
/**
 * @version   $Id: AbstractRokMenuTheme.php 58623 2012-12-15 22:01:32Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

require_once(dirname(__FILE__) . '/RokMenuTheme.php');

if (!class_exists('AbstractRokMenuTheme')) {

	abstract class AbstractRokMenuTheme implements RokMenuTheme
	{
		/**
		 * @var array
		 */
		protected $defaults = array();

		/**
		 * @return array
		 */
		public function getDefaults()
		{
			return $this->defaults;
		}
	}
}
