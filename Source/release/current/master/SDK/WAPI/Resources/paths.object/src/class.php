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
 * 
 * @copyright	Copyright (C) 2015 Drovio. All rights reserved.
 */

importer::import("WAPI", "Resources", "filesystem/directory");

use \WAPI\Resources\filesystem\directory;

/**
 * Website Path Manager
 * 
 * It's the website's path giver.
 * All classes should rely on this path giver class in order for the code to work both on development and production server.
 * 
 * @version	4.0-2
 * @created	November 27, 2014, 19:07 (GMT)
 * @updated	December 11, 2015, 13:34 (GMT)
 */
class paths
{
	/**
	 * The website library root folder.
	 * 
	 * @type	string
	 */
	private static $websiteLibrary = "/lib/";
	
	/**
	 * Get the website root folder.
	 * 
	 * @return	string
	 * 		The root website folder.
	 */
	public static function getWebsiteFolder()
	{
		return websiteRoot;
	}
	
	/**
	 * Get the inner settings folder in the website folder.
	 * 
	 * @return	string
	 * 		The website settings folder.
	 */
	public static function getSettingsFolder()
	{
		return websiteSettings;
	}
	
	/**
	 * Get the inner map folder in the website folder.
	 * 
	 * @return	string
	 * 		The website map folder.
	 */
	public static function getMapFolder()
	{
		return self::getWebsiteFolder()."/map/";
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
		return self::getWebsiteFolder()."/pages/".$pagePath.".page/";
	}
	
	/**
	 * Get the inner source folder in the website folder.
	 * 
	 * @return	string
	 * 		The website source folder.
	 */
	public static function getSourceFolder()
	{
		return self::getWebsiteFolder()."/src/";
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
		return self::getWebsiteResources()."/wc/";
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
		return self::$extensionsFolder."/extensions/";
	}
}
//#section_end#
?>