<?php
//#section#[header]
// Namespace
namespace WEB\Website;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WEB
 * @package	Website
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Resources", "paths");

use \WAPI\Resources\paths;

/**
 * Website Manager
 * 
 * This is the website manager class.
 * It imports website source objects and load website pages.
 * 
 * @version	0.1-1
 * @created	November 27, 2014, 21:57 (EET)
 * @revised	November 27, 2014, 21:57 (EET)
 */
class website
{
	/**
	 * Import a website source object
	 * 
	 * @param	string	$package
	 * 		The object's package name.
	 * 
	 * @param	string	$class
	 * 		The full name of the class (including namespaces separated by "/")
	 * 
	 * @return	void
	 */
	public static function import($package, $class = "")
	{
		// Load the entire package if class is empty
		if (empty($class))
			return self::importPackage($package);
		
		// Break classname
		$class = str_replace("::", "/", $class);
		$classParts = explode("/", $class);
		
		// Get Class Name
		$className = $classParts[count($classParts)-1];
		unset($classParts[count($classParts)-1]);
		
		// Get namespace
		$namespace = implode("/", $classParts);
		
		// ...
	}
	
	/**
	 * Import an entire website source package.
	 * 
	 * @param	string	$package
	 * 		The website source package name.
	 * 
	 * @return	void
	 */
	private function importPackage($package)
	{
	}
	
	/**
	 * Load a website page from the inner page directory.
	 * 
	 * @param	string	$pagePath
	 * 		The page path to load.
	 * 
	 * @return	mixed
	 * 		The page output.
	 */
	public static function loadPage($pagePath)
	{
		// Get website page path
		$pagePath = paths::getWebsiteFolder()."/pages/".$pagePath.".page/index.php";
		
		// Try to load the page
		try
		{
			// Load the page view
			$content = importer::req($pagePath);
		}
		catch (Exception $ex)
		{
			// Show an error notification if page doesn't exist
			$content  = "<h2>Error on Loading</h2>";
		}
		
		return $content;
	}
}
//#section_end#
?>