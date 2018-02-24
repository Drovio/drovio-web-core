<?php
//#section#[header]
// Namespace
namespace WEB\Platform;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WEB
 * @package	Platform
 * 
 * @copyright	Copyright (C) 2015 DrovIO. All rights reserved.
 */

importer::import("WAPI", "Prototype", "sourceMap");
importer::import("WAPI", "Resources", "paths");

use \WAPI\Prototype\sourceMap;
use \WAPI\Resources\paths;

/**
 * Website Manager
 * 
 * This is the website manager class.
 * It imports website source objects and load website pages.
 * 
 * @version	0.2-3
 * @created	December 31, 2014, 20:11 (EET)
 * @updated	July 17, 2015, 13:29 (EEST)
 */
class website
{
	/**
	 * Import a website source object.
	 * 
	 * @param	string	$library
	 * 		The object's library name.
	 * 
	 * @param	string	$package
	 * 		The object's package name.
	 * 
	 * @param	string	$class
	 * 		The full name of the class (including namespaces separated by "/").
	 * 
	 * @return	void
	 */
	public static function import($library, $package, $class = "")
	{
		// Load the entire package if class is empty
		if (empty($class))
			return self::importPackage($library, $package);
		
		// Break classname
		$class = str_replace("::", "/", $class);
		$classParts = explode("/", $class);
		
		// Get Class Name
		$className = $classParts[count($classParts)-1];
		unset($classParts[count($classParts)-1]);
		
		// Get namespace
		$namespace = implode("/", $classParts);
		
		// Import object
		$objectPath = paths::getSourceFolder()."/".$library."/".$package."/".$namespace."/".$className.".php";
		return importer::req($objectPath, TRUE, TRUE);
	}
	
	/**
	 * Import an entire website source package.
	 * 
	 * @param	string	$library
	 * 		The object's library name.
	 * 
	 * @param	string	$package
	 * 		The website source package name.
	 * 
	 * @return	void
	 */
	private function importPackage($library, $package)
	{
		// Initialize source map
		$sourceMap = new sourceMap(paths::getMapFolder(), "source.xml");
		
		// Get all objects in the package
		$objects = $sourceMap->getObjectList($library, $package);
		foreach ($objects as $object)
		{
			$objectNamespace = str_replace("::", "/", $object['namespace']);
			$objectName = (empty($objectNamespace) ? "" : $objectNamespace."/").$object['name'];
			$objectFullPath = systemSDK."/".$library."/".$package."/".$objectName.".php";
			importer::incl($objectFullPath, TRUE, TRUE);
		}
	}
	
	/**
	 * Load a website page from the inner page directory.
	 * 
	 * @param	string	$pagePath
	 * 		The page path to load.
	 * 
	 * @return	mixed
	 * 		The page output.
	 * 
	 * @throws	PageNotFound, Exception
	 */
	public static function loadPage($pagePath)
	{
		// Get website page path
		$pagePath = paths::getPageFolder($pagePath)."/page.php";

		// Load the page view
		$content = importer::req($pagePath, $root = TRUE, $once = FALSE);
		
		return $content;
	}
}
//#section_end#
?>