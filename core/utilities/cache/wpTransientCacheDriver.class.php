<?php
/**
 * @version   1.19 September 20, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 *
 *
 * Original Author and Licence
 * @author Mateusz 'MatheW' Wójcik, <mat.wojcik@gmail.com>
 * @link http://mwojcik.pl
 * @version 1.0
 * @license GPL
 */

class WpTransientCacheDriver implements CacheDriver
{



    protected $expire = 900;

	/**
	 * Constructor
	 *
	 * @throws CacheException
	 * @param string $dir Directory - with ending slash!
	 */
	public function __construct($expire = 900)
	{
        $this->expire = $expire;
	}

	/**
	 * Sets data to cache
	 *
	 * @param string $groupName Name of group of cache
	 * @param string $identifier Identifier of data
	 * @param mixed $data Data
	 * @return boolean
	 */
	public function set($groupName, $identifier, $data){
        $fullid = $groupName.$identifier;
        set_transient($fullid, $data, $this->expire);
	}

	/**
	 * Gets data from cache
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier of data
	 * @return mixed
	 */
	public function get($groupName, $identifier){
        $fullid = $groupName.$identifier;
        return get_transient($fullid);
	}

	/**
	 * Clears cache of specified identifier of group
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier
	 * @return boolean
	 */
	public function clearCache($groupName, $identifier){
        $fullid = $groupName.$identifier;
        return delete_transient($fullid);
	}

	/**
	 * Clears cache of specified group
	 *
	 * @param string $groupName Name of group
	 * @return boolean
	 */
	public function clearGroupCache($groupName){
        return false;
	}

	/**
	 * Clears all cache generated by this class with this driver
	 *
	 * @return boolean
	 */
	public function clearAllCache(){
        return false;
	}
    
	/**
	 * Gets last modification time of specified cache data
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier
	 * @return int
	 */
	public function modificationTime($groupName, $identifier){
        $timeout_id ='_transient_timeout_'.$groupName.$identifier;
        $timeout = get_option( $timeout_id );
        return $timeout - $this->expire;
	}

	/**
	 * Check if cache data exists
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier
	 * @return boolean
	 */
	public function exists($groupName, $identifier){
        $fullid = $groupName.$identifier;
        $info = get_transient($fullid);
		return ($info == false)?false:true;
	}

} /* end of class WpTransientCacheDriver */