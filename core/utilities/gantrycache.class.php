<?php
/**
 * @version   $Id: gantrycache.class.php 58625 2012-12-15 22:41:23Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

require_once(dirname(__FILE__) . '/cache/cache.class.php');
require_once(dirname(__FILE__) . '/cache/wpTransientCacheDriver.class.php');

/**
 *
 */
class GantryCache
{

	protected static $instance;

	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new GantryCache();
		}
		return self::$instance;
	}

	/**
	 * The cache object.
	 *
	 * @var Cache
	 */
	protected $cache = null;

	/**
	 * Lifetime of the cache
	 * @access private
	 * @var int
	 */
	protected $lifetime = 900;

	public function __construct()
	{
		$this->cache = new RokCache();
		$this->init();
	}

	public function init()
	{
		$this->cache->addDriver('wptransient', new WpTransientCacheDriver($this->lifetime));
	}

	public function get($groupName, $identifier, $function = null, $arguments = array())
	{
		$ret = $this->cache->get($groupName, $identifier, $this->lifetime);
		if ($ret == false && $function != null) {
			$ret = call_user_func_array($function, $arguments);
			$this->cache->set($groupName, $identifier, $ret);
		}
		return $ret;
	}

	public function clearAllCache()
	{
		return $this->cache->clearAllCache();
	}

	public function clearGroupCache($groupName)
	{
		return $this->cache->clearGroupCache($groupName);
	}

	public function clear($groupName, $identifier)
	{
		return $this->cache->clearCache($groupName, $identifier);
	}

	/**
	 * Gets the lifetime for gantry
	 * @access public
	 * @return int
	 */
	public function getLifetime()
	{
		return $this->lifetime;
	}

	/**
	 * Sets the lifetime for gantry
	 * @access public
	 *
	 * @param int $lifetime
	 */
	public function setLifetime($lifetime)
	{
		$this->lifetime = $lifetime;
	}

}