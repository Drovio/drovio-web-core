<?php
//#section#[header]
// Namespace
namespace WAPI\Resources;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Resources
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

/**
 * Website Path Manager
 * 
 * It's the website's path giver.
 * All classes should rely on this path giver class in order for the code to work both on development and production server.
 * 
 * @version	1.0-1
 * @created	November 27, 2014, 21:07 (EET)
 * @revised	December 30, 2014, 13:42 (EET)
 */
class paths
{
	/**
	 * The website folder (not the root).
	 * 
	 * @type	string
	 */
	private static $websiteFolder = "/.website/";
	
	/**
	 * The website library root folder.
	 * 
	 * @type	string
	 */
	private static $websiteLibrary = "/lib/";
	
	/**
	 * The extensions' root folder.
	 * 
	 * @type	string
	 */
	private static $extensionsFolder = "/extensions/";
	
	/**
	 * Get the website folder which contains website data (pages, source, scripts).
	 * 
	 * @return	string
	 * 		The website folder path.
	 */
	public static function getWebsiteFolder()
	{
		return self::$websiteFolder;
	}
	
	/**
	 * Get website library root folder.
	 * It includes resources (media), scripts and styles.
	 * 
	 * @return	string
	 * 		The website library root folder.
	 */
	public static function getWebsiteLibrary()
	{
		return self::$websiteLibrary;
	}
	
	/**
	 * Gets the website core resources folder.
	 * 
	 * @return	string
	 * 		The core resources root folder.
	 */
	public static function getCoreRsrcFolder()
	{
		return self::getWebsiteResources()."/c/";
	}
	
	/**
	 * Gets the website resources root folder.
	 * 
	 * @return	string
	 * 		The websites resources root folder path.
	 */
	public static function getWebsiteResources()
	{
		return self::getWebsiteLibrary()."/rsrc/";
	}
	
	/**
	 * Get the folder path for the given extension.
	 * 
	 * @param	string	$extensionID
	 * 		The extension id to get the folder path.
	 * 
	 * @return	string
	 * 		The extension folder path.
	 */
	public static function getExtensionFolder($extensionID = "")
	{
		return self::$extensionsFolder."/".$extensionID."/";
	}
}
//#section_end#
?>