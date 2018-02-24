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
 * @copyright	Copyright (C) 2015 Redback. All rights reserved.
 */

importer::import("WAPI", "Resources", "filesystem/directory");

use \WAPI\Resources\filesystem\directory;

/**
 * Website Path Manager
 * 
 * It's the website's path giver.
 * All classes should rely on this path giver class in order for the code to work both on development and production server.
 * 
 * @version	2.0-1
 * @created	November 27, 2014, 21:07 (EET)
 * @updated	January 2, 2015, 19:50 (EET)
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
	 * Get the inner settings folder in the website folder.
	 * 
	 * @return	string
	 * 		The website settings folder.
	 */
	public static function getSettingsFolder()
	{
		return self::$websiteFolder."/settings/";
	}
	
	/**
	 * Get the inner map folder in the website folder.
	 * 
	 * @return	string
	 * 		The website map folder.
	 */
	public static function getMapFolder()
	{
		return self::$websiteFolder."/map/";
	}
	
	/**
	 * Get the inner page folder path in the website folder.
	 * 
	 * @param	string	$pagePath
	 * 		The page path as taken from the request uri.
	 * 		No extension should provided.
	 * 
	 * @return	string
	 * 		The inner page folder path.
	 */
	public static function getPageFolder($pagePath = "")
	{
		// Normalize page path
		$pagePath = trim($pagePath);
		$pagePath = trim($pagePath, "/");
		$pagePath = (empty($pagePath) ? "index" : $pagePath);
		$pagePath = directory::normalize($pagePath);
		
		// Get full page path
		return self::$websiteFolder."/pages/".$pagePath.".page/";
	}
	
	/**
	 * Get the inner source folder in the website folder.
	 * 
	 * @return	string
	 * 		The website source folder.
	 */
	public static function getSourceFolder()
	{
		return self::$websiteFolder."/src/";
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