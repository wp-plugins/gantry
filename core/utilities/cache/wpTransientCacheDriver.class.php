<?php
/**
 * @version   $Id: wpTransientCacheDriver.class.php 59390 2013-03-15 20:51:45Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class WpTransientCacheDriver implements GantryCacheLibDriver
{
	const DEFAULT_LIFETIME = 900;
	const NO_EXPIRE_LIFETIME = 0;

	protected $lifeTime = self::DEFAULT_LIFETIME;
	protected $groupname;

	public function __construct($groupName, $lifeTime = self::DEFAULT_LIFETIME)
	{
		$this->groupname = $groupName;
		$this->lifeTime = $lifeTime;
	}

	/**
	 * Check if cache data exists
	 *
	 * @param string $groupName  Name of group
	 * @param string $identifier Identifier
	 *
	 * @return boolean
	 */
	public function exists($groupName, $identifier)
	{
		$fullid = $groupName .'-'. $identifier;
		$value = get_transient($fullid);
		return ($value !== false) ? true : false;
	}

	/**
	 * Gets last modification time of specified cache data
	 *
	 * @param string $groupName  Name of g
	 * roup
	 * @param string $identifier Identifier
	 *
	 * @return int
	 */
	public function modificationTime($groupName, $identifier)
	{
		$timeout_id = '_transient_timeout_' . $groupName .'-'. $identifier;
		$timeout    = get_option($timeout_id);
		return $timeout - $this->lifeTime;
	}



	/**
	 * Clears all cache generated by this class with this driver
	 *
	 * @return boolean
	 */
	public function clearAllCache()
	{
		return false;
	}

	/**
	 * Clears cache of specified group
	 *
	 * @param string $groupName Name of group
	 *
	 * @return boolean
	 */
	public function clearGroupCache($groupName)
	{
		/** @global wpdb $wpdb */
		global $wpdb;
		$cache_entries = $wpdb->get_results($wpdb->prepare( "SELECT option_name FROM $wpdb->options WHERE option_name like %s", '_transient_'.$groupName.'-%' ) );
		foreach($cache_entries as $cache_entry)
		{
			delete_transient(str_replace('_transient_','',$cache_entry->option_name));
		}
		return true;
	}

	/**
	 * Clears cache of specified identifier of group
	 *
	 * @param string $groupName  Name of group
	 * @param string $identifier Identifier
	 *
	 * @return boolean
	 */
	public function clearCache($groupName, $identifier)
	{
		$fullid = $groupName .'-'. $identifier;
		return delete_transient($fullid);
	}


	/**
	 * Gets data from cache
	 *
	 * @param string $groupName  Name of group
	 * @param string $identifier Identifier of data
	 *
	 * @return mixed
	 */
	public function get($groupName, $identifier)
	{
		$fullid = $groupName .'-'. $identifier;
		return get_transient($fullid);
	}

	/**
	 * Sets data to cache
	 *
	 * @param string $groupName  Name of group of cache
	 * @param string $identifier Identifier of data
	 * @param mixed  $data       Data
	 *
	 * @return boolean
	 */
	public function set($groupName, $identifier, $data)
	{
		$fullid = $groupName .'-'. $identifier;
		return set_transient($fullid, $data, $this->lifeTime);
	}

	/**
	 * Sets the lifetime of the cache
	 *
	 * @abstract
	 *
	 * @param  int $lifeTime Lifetime of the cache
	 *
	 * @return void
	 */
	public function setLifeTime($lifeTime)
	{
		$this->lifeTime = $lifeTime;
	}

}