<?php
//#section#[header]
// Namespace
namespace WDEV\Tester\core;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WDEV
 * @package	Tester
 * @namespace	\core
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WDEV", "Tester", "tester");

use \WDEV\Tester\tester;

/**
 * SDK Package Tester Manager
 * 
 * Manages sdk packages tester mode.
 * 
 * @version	0.1-2
 * @created	December 29, 2014, 18:36 (EET)
 * @revised	December 30, 2014, 10:24 (EET)
 */
class sdkTester extends tester
{
	/**
	 * Activates the sdk tester mode for the given sdk package.
	 * 
	 * @param	array	$packages
	 * 		An array of all packages with the mixed package name or empty ("all") for all packages.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function activate($packages = "all")
	{
		if (empty($packages))
			return self::deactivate();
		
		// Set New Package List
		if (is_array($packages))
		{
			$pkgList = implode(":", $packages);
			return parent::activate("wsdkTester", $pkgList);
		}
		else
			return parent::activate("wsdkTester", "all");
	}
	
	/**
	 * Deactivates the sdk tester mode for the given sdk package.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function deactivate()
	{
		// Deactivate packages
		return parent::deactivate("wsdkTester");
	}
	
	/**
	 * Gets the tester status for a given sdk package.
	 * 
	 * @param	string	$package
	 * 		The mixed package name to check.
	 * 
	 * @return	boolean
	 * 		True if package is on tester mode, false otherwise.
	 */
	public static function status($package)
	{
		// Get Packages
		$pkgList = self::getPackages();
		
		// Return if exists
		return (in_array($package, $pkgList));
	}
	
	/**
	 * Gets the tester status for a given sdk package.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @return	boolean
	 * 		True if package is on tester mode, false otherwise.
	 */
	public static function libPackageStatus($library, $package)
	{
		$packageName = $library."_".$package;
		return self::status($packageName);
	}
	
	/**
	 * Get all packages on tester mode.
	 * 
	 * @return	array
	 * 		An array of all active mixed package names.
	 */
	private static function getPackages()
	{
		// Get Package List
		$list = parent::status("wsdkTester");
		
		if (empty($list))
			return array();
		
		// Search if package exists
		return explode(":", $list);
	}
}
//#section_end#
?>