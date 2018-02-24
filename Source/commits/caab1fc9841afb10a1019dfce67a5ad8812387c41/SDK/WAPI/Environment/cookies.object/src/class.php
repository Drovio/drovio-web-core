<?php
//#section#[header]
// Namespace
namespace WAPI\Environment;


//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Environment
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Environment", "url");

use \WAPI\Environment\url;

/**
 * Environment Cookie's Manager
 * 
 * This is the system's cookie manager.
 * Creates, deletes and reads website cookies.
 * 
 * @version	0.1-2
 * @created	November 27, 2014, 20:39 (EET)
 * @revised	November 27, 2014, 20:39 (EET)
 */
class cookies
{
	/**
	 * Create a new cookie or update an existing one.
	 * It uses the php's setcookie function with preset values for domain and paths.
	 * 
	 * @param	string	$name
	 * 		The cookie's name.
	 * 
	 * @param	string	$value
	 * 		The cookie's value.
	 * 
	 * @param	integer	$expiration
	 * 		The expiration of the cookie in seconds.
	 * 		If set to 0, the cookie will expire at the end of the session.
	 * 
	 * @param	boolean	$secure
	 * 		Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client.
	 * 
	 * @param	boolean	$httpOly
	 * 		When TRUE the cookie will be made accessible only through the HTTP protocol.
	 * 		This means that the cookie won't be accessible by scripting languages, such as JavaScript.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function set($name, $value, $expiration = 0, $secure = FALSE, $httpOly = FALSE)
	{
		// Set cookie params
		$expiration = ($expiration == 0 ? $expiration : time() + $expiration);
		$path = "/";
		$domain = ".".url::getDomain();
		
		// Set cookie
		if (setcookie($name, $value, $expiration, $path, $domain, ($secure ? 1 : 0), ($httpOly ? 1 : 0)))
			return TRUE;
		
		return FALSE;
	}
	
	/**
	 * Get the value of a cookie.
	 * 
	 * @param	string	$name
	 * 		The cookie's name.
	 * 
	 * @return	mixed
	 * 		The cookie value or NULL if cookie doesn't exist.
	 */
	public static function get($name)
	{
		return (isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL);
	}
	
	/**
	 * Remove a cookie.
	 * 
	 * @param	string	$name
	 * 		The cookie's name.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function remove($name)
	{
		// Set cookie and return TRUE
		if (self::set($name, NULL, - 3600))
			return TRUE;
		
		// Return FALSE
		return FALSE;
	}	
}
//#section_end#
?>