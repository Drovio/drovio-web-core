<?php
//#section#[header]
// Namespace
namespace WDEV\Tester;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WDEV
 * @package	Tester
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Environment", "cookies");

use \WAPI\Environment\cookies;

/**
 * Abstract Tester Profile
 * 
 * Manages all the testing configuration.
 * It works only under the Redback development platform.
 * 
 * @version	0.1-1
 * @created	December 29, 2014, 18:10 (EET)
 * @revised	December 29, 2014, 18:10 (EET)
 */
abstract class tester
{
	/**
	 * Inner status value holder.
	 * 
	 * @type	array
	 */
	protected static $status = array();
	
	/**
	 * Activate the tester mode for the given mode.
	 * 
	 * @param	string	$name
	 * 		The tester mode name.
	 * 
	 * @param	mixed	$value
	 * 		The value to store as activated (True or another value).
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function activate($name = "", $value = TRUE)
	{
		if (!empty($name))
		{
			self::$status[$name] = $value;
			return cookies::set($name, $value, $expiration = 0);
		}
		
		return FALSE;
	}
	
	/**
	 * Deactivate the tester mode for the given mode.
	 * 
	 * @param	string	$name
	 * 		The tester mode name.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function deactivate($name = "")
	{
		if (!empty($name))
		{
			unset(self::$status[$name]);
			return cookies::set($name, FALSE, $expiration = -1);
		}
		
		return FALSE;
	}
	
	/**
	 * Gets the tester mode status.
	 * 
	 * @param	string	$name
	 * 		The tester mode name.
	 * 
	 * @return	mixed
	 * 		Returns the tester mode status value.
	 */
	public static function status($name = "")
	{
		if (!empty($name))
			if (isset(self::$status[$name]))
				return self::$status[$name];
			else
				return (is_null(cookies::get($name)) ? FALSE : cookies::get($name));
			
		return FALSE;
	}
}
//#section_end#
?>