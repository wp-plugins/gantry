<?php
/**
 * @version $Id: fileCacheDriver.class.php 58595 2012-12-11 19:59:45Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
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

class FileCacheDriver implements CacheDriver
{

	/**
	 * Directory with ending slash!
	 *
	 * @var string
	 */
	protected $dir = 'cache/';

	/**
	 * Extension of cache file - with comma!
	 *
	 * @var string
	 */
	protected $ext = '.cache';


	/**
	 * Constructor
	 *
	 * @throws CacheException
	 * @param string $dir Directory - with ending slash!
	 */
	public function __construct($dir=NULL)
	{
		if(!empty($dir) && $this->checkDirectory($dir)) $this->dir=$dir;
		else if(!$this->checkDirectory($this->dir)) throw new CacheException('Unable to use given directory. Check file permissions.');
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
		if(!$this->checkDirectory($this->createPath($groupName))) return false;
		return file_put_contents($this->createPath($groupName, $identifier), $data)===false?false:true;
	}

	/**
	 * Gets data from cache
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier of data
	 * @return mixed
	 */
	public function get($groupName, $identifier){
		return file_get_contents($this->createPath($groupName, $identifier));
	}

	/**
	 * Clears cache of specified identifier of group
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier
	 * @return boolean
	 */
	public function clearCache($groupName, $identifier){
		@unlink($this->createPath($groupName, $identifier));
	}

	/**
	 * Clears cache of specified group
	 *
	 * @param string $groupName Name of group
	 * @return boolean
	 */
	public function clearGroupCache($groupName){
		$this->deleteDir($this->createPath($groupName));
	}

	/**
	 * Clears all cache generated by this class with this driver
	 *
	 * @return boolean
	 */
	public function clearAllCache(){
		$this->deleteDir($this->dir, true);
	}



	/**
	 * Gets last modification time of specified cache data
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier
	 * @return int
	 */
	public function modificationTime($groupName, $identifier){
		return filemtime($this->createPath($groupName, $identifier));
	}

	/**
	 * Check if cache data exists
	 *
	 * @param string $groupName Name of group
	 * @param string $identifier Identifier
	 * @return boolean
	 */
	public function exists($groupName, $identifier){
		return is_file($this->createPath($groupName, $identifier));
	}


	/**
	 * Sets cache directory
	 *
	 * @param string $dir Path to directory - with ending slash!
	 * @return void
	 */
	public function setDirectory($dir)
	{
		if($this->checkDirectory($dir)) $this->dir=$dir;
		else return false;
		return true;
	}

	/**
	 * Sets cache file extension
	 *
	 * @param string $ext File extension
	 */
	public function setExtension($ext)
	{
		$this->ext=$ext;
	}

	/**
	 * Check directory if it exists and is writable. It tries to create directory and give it good permissions
	 *
	 * @param string $dir
	 * @return boolean
	 */
	protected function checkDirectory($dir){
		if(!is_dir($dir) && mkdir($dir, 0777)==false) return false;
		if(!is_writable($dir) && chmod($dir, 0777)==false) return false;
		return true;
	}

	/**
	 * Deletes directory with content
	 *
	 * @param string $dir Path to directory with ending slash
	 * @param boolean $contentOnly
	 */
	protected function deleteDir($dir, $contentOnly=false){
		if ( ! $currentDir = @opendir($dir))
		return;

		while(FALSE !== ($fileName = @readdir($currentDir)))
		{
			if ($fileName != "." && $fileName != "..")
			{

				if (is_dir($dir.$fileName))
				{
					$this->deleteDir($dir.$fileName.'/');
				}
				else
				{
					@unlink($dir.$fileName);
				}
			}
		}
		@closedir($currentDir);

		if(!$contentOnly) @rmdir($dir);
	}

	/**
	 * Creates path to file/directory
	 *
	 * @param string $groupName
	 * @param string $identifier
	 * @return string
	 */
	protected function createPath($groupName, $identifier=NULL){
		return $this->dir.$groupName.'/'.(empty($identifier)?'':$identifier.$this->ext);
	}

} /* end of class FileCacheDriver */

?>