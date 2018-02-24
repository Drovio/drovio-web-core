<?php
//#section#[header]
// Namespace
namespace WAPI\Platform;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Platform
 * 
 * @copyright	Copyright (C) 2015 DrovIO. All rights reserved.
 */

importer::import("WAPI", "Prototype", "sourceMap");

use \WAPI\Prototype\sourceMap;

/**
 * Website Core Importer
 * 
 * It is used to import all files in the system and from the web core.
 * 
 * @version	4.0-1
 * @created	October 1, 2014, 15:30 (EEST)
 * @updated	September 20, 2015, 12:37 (EEST)
 */
class importer
{
	/**
	 * All loaded classes so far.
	 * 
	 * @type	array
	 */
	private static $loaded = array();
	
	/**
	 * Import an SDK Object from the given library and package.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @param	string	$class
	 * 		The full name of the class (including namespaces separated by "/").
	 * 
	 * @return	void
	 */
	public static function import($library, $package, $class = "")
	{
		// Normalize class name
		$class = str_replace("::", "/", $class);
		
		// Load Entire Package
		if (empty($class))
			return self::loadPackage($library, $package);
		
		// Check if the class is already loaded
		if (self::checkLoaded($library, $package, $class))
			return;
		
		// Get class path
		if (self::getTesterStatus($library, $package))
			$nspath = wsdkRepoRoot."/".$library."/".$package."/".str_replace("::", "/", $class).innerClassPath;
		else
			$nspath = self::getReleaseObjectPath($library, $package, $class);
		
		// Import file
		self::req($nspath, TRUE, TRUE);
		
		// Set Class as Loaded
		self::setLoaded($library, $package, $class);
	}
	
	/**
	 * Loads all objects in the given package.
	 * If the package is tester, it loads the objects from the repository trunk.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @return	void
	 */
	private static function loadPackage($library, $package)
	{
		// If packageName not given return;
		if (empty($package))
			return;
		
		// Check package tester status
		$packageTesterStatus = self::getTesterStatus($library, $package);

		// Load from SDK root
		$sourceMap = new sourceMap(wsystemRoot.wsdkRoot, "map.xml");
		$pkgObjects = $sourceMap->getObjectList($library, $package);
		foreach ($pkgObjects as $object)
		{
			// Normalize object name
			$objectName = (empty($object['namespace']) ? "" : $object['namespace']."/").$object['name'];
			
			// Check tester status
			if ($packageTesterStatus)
				$nspath = wsdkRepoRoot."/".$library."/".$package."/".str_replace("::", "/", $objectName).innerClassPath;
			else
				$nspath = self::getReleaseObjectPath($library, $package, $objectName);
			
			// Import class
			importer::incl($nspath, TRUE, TRUE);
			
			// Set Class as Loaded
			self::setLoaded($library, $package, $objectName);
		}
	}
	
	/**
	 * Checks if a class has already been loaded.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @param	string	$class
	 * 		The class full name (including namespace).
	 * 
	 * @return	boolean
	 * 		True if the class has already been loaded, false otherwise.
	 */
	private static function checkLoaded($library, $package, $class)
	{
		$fullName = $library."/".$package."/".$class;
		return in_array($fullName, self::$loaded);
	}
	
	/**
	 * Sets a class as loaded.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @param	string	$class
	 * 		The class full name (including namespace).
	 * 
	 * @return	void
	 */
	private static function setLoaded($library, $package, $class)
	{
		$fullName = $library."/".$package."/".$class;
		self::$loaded[] = $fullName;
	}
	
	/**
	 * Include file (doesn't throw exception...)
	 * 
	 * @param	string	$path
	 * 		The file path to include.
	 * 
	 * @param	boolean	$root
	 * 		Indicator that defines whether the path will be normalized to system's root.
	 * 
	 * @param	boolean	$once
	 * 		Include once or not.
	 * 
	 * @return	mixed
	 * 		The result of the inclusion.
	 */
	public static function incl($path, $root = TRUE, $once = FALSE)
	{
		// Normalize path
		$nspath = ($root ? wsystemRoot : "").$path;

		// Include File
		if (file_exists($nspath))
			return ($once ? include_once($nspath) : include($nspath));
	}
	
	/**
	 * Require file.
	 * It throws an exception if the file doesn't exist.
	 * 
	 * @param	string	$path
	 * 		The file path to include.
	 * 
	 * @param	boolean	$root
	 * 		Indicator that defines whether the path will be normalized to system's root.
	 * 
	 * @param	boolean	$once
	 * 		Include once or not.
	 * 
	 * @return	mixed
	 * 		The result of the inclusion.
	 * 
	 * @throws	Exception
	 */
	public static function req($path, $root = TRUE, $once = FALSE)
	{
		// Normalize path
		$nspath = ($root ? wsystemRoot : "").$path;

		// Include File
		if (file_exists($nspath))
			return ($once ? require_once($nspath) : require($nspath));
		else
			throw new Exception("File '".$nspath."' doesn't exist for inclusion...", 2);
	}
	
	/**
	 * Returns whether the user has set the given package for testing.
	 * 
	 * @param	string	$libName
	 * 		The library name.
	 * 
	 * @param	string	$packageName
	 * 		The package name.
	 * 
	 * @return	boolean
	 * 		True if the given package is in tester mode, false otherwise.
	 */
	public static function getTesterStatus($libName, $packageName)
	{
		return self::packageStatus($libName."_".$packageName);
	}
	
	/**
	 * Returns whether the user has set the given package for testing.
	 * 
	 * @param	string	$package
	 * 		The merged package name.
	 * 
	 * @return	boolean
	 * 		True if the given package is in tester mode, false otherwise.
	 */
	private static function packageStatus($package)
	{
		// Get Packages
		$pkgList = self::getTesterPackages();
		
		// Return if exists
		return (self::testerStatus() && in_array($package, $pkgList));
	}
	
	/**
	 * Get all tester packages.
	 * 
	 * @return	array
	 * 		An array of all packages in tester mode.
	 */
	private static function getTesterPackages()
	{
		// Get Package List
		$list = self::getCookie("wsdkTester");
		
		// Search if package exists
		return explode(":", $list);
	}
	
	/**
	 * Returns the global tester's status value.
	 * 
	 * @return	string
	 * 		The cookie value.
	 */
	private static function testerStatus()
	{
		return self::onDevelopment() && (is_null(self::getCookie("wsdkTester")) ? FALSE : self::getCookie("wsdkTester"));
	}
	
	/**
	 * Get the value of a cookie with the given name.
	 * 
	 * @param	string	$name
	 * 		The cookie's name.
	 * 
	 * @return	string
	 * 		The cookie value.
	 */
	private static function getCookie($name)
	{
		return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL);
	}
	
	/**
	 * Checks whether the website is running on the Development or the Production server, based on the web url.
	 * 
	 * @return	boolean
	 * 		True if the server is the development platform, false otherwise.
	 */
	public static function onDevelopment()
	{
		// Get host and split into parts
		$origin = (empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_ORIGIN']);
		$origin = str_replace("http://", "", $origin);
		$origin = str_replace("https://", "", $origin);
		$parts = explode(".", $origin);
		
		// Reverse and get first two for domain
		$parts = array_reverse($parts);
		if ($parts[1] == "redback" || $parts[1] == "drov")
			return TRUE;
		
		return FALSE;
	}
	
	/**
	 * Gets the release object path.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @param	string	$object
	 * 		The object name (including namespace separated by "::").
	 * 
	 * @return	string
	 * 		The release system object path.
	 */
	public static function getReleaseObjectPath($library, $package, $object)
	{
		return wsdkRoot."/".$library."/".$package."/".str_replace("::", "/", $object).".php";
	}
}
//#section_end#
?>