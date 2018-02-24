<?php
//#section#[header]
// Namespace
namespace WAPI\Platform;


//#section_end#
//#section#[class]
/**
 * @library	API
 * @package	Platform
 * @namespace	{empty}
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

/**
 * System Importer
 * 
 * It is used to import all files in the system.
 * Classes and resources.
 * 
 * @version	{empty}
 * @created	March 27, 2013, 12:11 (EET)
 * @revised	December 20, 2013, 19:18 (EET)
 */
class importer
{
	/**
	 * The repository path.
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
	 * Loaded classes
	 * 
	 * @type	array
	 */
	private static $loaded = array();
	
	/**
	 * Import an SDK Object from the given library and package.
	 * 
	 * @param	string	$library
	 * 		The library name
	 * 
	 * @param	string	$package
	 * 		The package name
	 * 
	 * @param	string	$class
	 * 		The full name of the class (including namespaces separated by "::")
	 * 
	 * @param	boolean	$strict
	 * 		Indicates whether the object will be forced to load from latest.
	 * 
	 * @return	void
	 */
	public static function import($library, $package, $class = "", $strict = FALSE)
	{
		// Load Entire Package
		if ($class == "")
		{
			//$sdkPkg = new sdkPackage();
			//return $sdkPkg->load($library, $package);
		}
		
		// Check if the class is already loaded
		if (self::checkLoaded($library, $package, $class))
			return;
			
		// Force loading from latest
		if ($strict || !self::getTesterStatus($library, $package))
			$nspath = systemWSDK."/".$library."/".$package."/".str_replace("::", "/", $class).".php";
		else if (self::getTesterStatus($library, $package))
		{
			// Break classname
			$classParts = explode("::", $class);
			
			// Get Class Name
			$className = $classParts[count($classParts)-1];
			unset($classParts[count($classParts)-1]);
			
			// Get namespace
			$namespace = implode("/", $classParts);
			
			// Form nspath
			$nspath = self::$vcsPath.$library."/".$package."/".$namespace.$className.self::$innerClassPath;
		}
		
		self::req($nspath, TRUE, TRUE);
		
		// Set Class as Loaded
		self::setLoaded($library, $package, $class);
	}
	
	/**
	 * Checks if a class has already been loaded.
	 * 
	 * @param	string	$library
	 * 		The object's library name.
	 * 
	 * @param	string	$package
	 * 		The object's package name.
	 * 
	 * @param	string	$class
	 * 		The object's full name (including namespace).
	 * 
	 * @return	boolean
	 * 		{description}
	 */
	private static function checkLoaded($library, $package, $class)
	{
		$fullName = $library."::".$package."::".$class;
		return in_array($fullName, self::$loaded);
	}
	
	/**
	 * Sets a object as loaded.
	 * 
	 * @param	string	$library
	 * 		The object's library name.
	 * 
	 * @param	string	$package
	 * 		The object's package name.
	 * 
	 * @param	string	$class
	 * 		The object's full name (including namespace).
	 * 
	 * @return	void
	 */
	private static function setLoaded($library, $package, $class)
	{
		$fullName = $library."::".$package."::".$class;
		self::$loaded[] = $fullName;
	}
	
	/**
	 * Include file (doesn't throw exception...)
	 * 
	 * @param	string	$path
	 * 		The filepath
	 * 
	 * @param	boolean	$root
	 * 		Indicator that defines whether the path will be normalized to system's root
	 * 
	 * @param	boolean	$once
	 * 		Include once or not
	 * 
	 * @return	boolean
	 * 		{description}
	 */
	public static function incl($path, $root = TRUE, $once = FALSE)
	{
		// Normalize path
		$nspath = ($root ? systemRoot : "").$path;

		// Include File
		if (file_exists($nspath))
			return ($once ? include_once($nspath) : include($nspath));
		else
			self::log("File '".$path."' doesn't exist for inclusion...", logger::ERROR);
	}
	
	/**
	 * Require file (throws exception...)
	 * 
	 * @param	string	$path
	 * 		The filepath
	 * 
	 * @param	boolean	$root
	 * 		Indicator that defines whether the path will be normalized to system's root
	 * 
	 * @param	boolean	$once
	 * 		Require once or not
	 * 
	 * @return	boolean
	 * 		{description}
	 */
	public static function req($path, $root = TRUE, $once = FALSE)
	{
		// Normalize path
		$nspath = ($root ? systemRoot : "").$path;

		// Include File
		if (file_exists($nspath))
			return ($once ? require_once($nspath) : require($nspath));
		else
		{
			self::log("File '".$path."' doesn't exist to be imported. Throwing exception...", logger::ERROR);
			throw new Exception("File '".$path."' doesn't exist for inclusion...", 2);
		}
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
	 * 		{description}
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
	 * 		{description}
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
	 * 		{description}
	 */
	private static function getTesterPackages()
	{
		// Get Package List
		$list = self::getCookie("wecsdkTester");
		
		// Search if package exists
		return explode(":", $list);
	}
	
	/**
	 * Returns the global tester's status.
	 * 
	 * @return	boolean
	 * 		{description}
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
	 * @return	mixed
	 * 		{description}
	 */
	private static function getCookie($name)
	{
		return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL);
	}
}
//#section_end#
?>