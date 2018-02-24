<?php
//#section#[header]
// Namespace
namespace WAPI\Platform;


//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Platform
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

/**
 * Website Core Importer
 * 
 * It is used to import all files in the system and from the web core.
 * 
 * @version	0.1-2
 * @created	October 1, 2014, 15:30 (EEST)
 * @revised	November 27, 2014, 20:13 (EET)
 */
class importer
{
	/**
	 * The core repository path.
	 * 
	 * @type	string
	 */
	private static $vcsPath = "/.developer/Repository/p6e3b463e3106e4d9d1377738a5a0a180.project/Source/trunk/master/SDK/";
	
	/**
	 * The object's inner class path.
	 * 
	 * @type	string
	 */
	private static $innerClassPath = ".object/src/class.php";
	
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
	 * @param	boolean	$strict
	 * 		Indicates whether the object will be forced to load from latest.
	 * 
	 * @return	void
	 */
	public static function import($library, $package, $class = "", $strict = FALSE)
	{
		// Normalize class
		$class = str_replace("::", "/", $class);
		
		// Load Entire Package
		if ($class == "")
		{
			//$sdkPkg = new sdkPackage();
			//return $sdkPkg->load($library, $package);
			return;
		}
		
		// Check if the class is already loaded
		if (self::checkLoaded($library, $package, $class))
			return;
		
		// Check whether we are on the development server
		if (self::onDevelopment() && !($strict && !self::getTesterStatus($library, $package)))
			$nspath = self::$vcsPath.$library."/".$package."/".$class."/".self::$innerClassPath;
		else
			$nspath = systemWSDK."/".$library."/".$package."/".$class.".php";
		
		// Import file
		self::req($nspath, TRUE, TRUE);
		
		// Set Class as Loaded
		self::setLoaded($library, $package, $class);
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
		$nspath = ($root ? systemRoot : "").$path;

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
		$nspath = ($root ? systemRoot : "").$path;

		// Include File
		if (file_exists($nspath))
			return ($once ? require_once($nspath) : require($nspath));
		else
			throw new Exception("File '".$path."' doesn't exist for inclusion...", 2);
	}
	
	/**
	 * Checks whether the website is running on the Development or the Production server, based on the web url.
	 * 
	 * @return	boolean
	 * 		True if the server is for development, false otherwise.
	 */
	private static function onDevelopment()
	{
		// Get host and split into parts
		$origin = (empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_ORIGIN']);
		$origin = str_replace("http://", "", $origin);
		$origin = str_replace("https://", "", $origin);
		$parts = explode(".", $origin);
		
		// Reverse and get first two for domain
		$parts = array_reverse($parts);
		if ($parts[1] == "redback")
			return TRUE;
		
		return FALSE;
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
	private static function getTesterStatus($libName, $packageName)
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
		$list = self::getCookie("wecsdkTester");
		
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
		return (is_null(self::getCookie("wecsdkTester")) ? FALSE : self::getCookie("wecsdkTester"));
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
}
//#section_end#
?>