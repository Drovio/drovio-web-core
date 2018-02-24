<?php
//#section#[header]
// Namespace
namespace WAPI\Protocol\http;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Protocol
 * @namespace	\http
 * 
 * @copyright	Copyright (C) 2015 DrovIO. All rights reserved.
 */

/**
 * HTTP Request Manager
 * 
 * Manages all the information that the HTTP request transfers from the client to the server.
 * 
 * It is designed to provide data independent of the kind of the request (stateless or stateful).
 * 
 * @version	0.1-1
 * @created	July 16, 2015, 17:05 (EEST)
 * @updated	July 16, 2015, 17:05 (EEST)
 */
class HTTPRequest
{
	/**
	 * An array containing all the request variables (name and value).
	 * 
	 * @type	array
	 */
	private static $request;
	
	/**
	 * An array containing all the client cookies (name and value), for web access only.
	 * 
	 * @type	array
	 */
	private static $cookies;
	
	/**
	 * Initializes the request with all the data.
	 * 
	 * @return	void
	 */
	public static function init()
	{
		// Get current url info
		self::$request = $_REQUEST;
		
		// Get cookies
		self::$cookies = $_COOKIE;
	}
	
	/**
	 * Get the request method string.
	 * 
	 * @return	string
	 * 		The request method string in uppercase.
	 */
	public static function requestMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}
	
	/**
	 * Get a variable from the request.
	 * It can include cookies for web content.
	 * 
	 * It works independently and can get a variable from the url or from a cookie without the user knowing.
	 * 
	 * @param	string	$name
	 * 		The variable name.
	 * 
	 * @param	string	$cookieName
	 * 		The cookie name.
	 * 		If this is set, the function will search for the cookie variable if not found in the request variables.
	 * 		It is empty by default.
	 * 
	 * @return	mixed
	 * 		The variable value or NULL if the variable doesn't exist in the requested scope.
	 */
	public static function getVar($name, $cookieName = "")
	{
		// Get value from url variables
		if (!empty($name))
			$value = self::$request[$name];
		
		// Check if empty and get from cookie (if declared)
		if (empty($value) && !empty($cookieName))
			$value = self::$cookies[$cookieName];
		
		// Check if empty and return null
		if (empty($value))
			return NULL;
			
		// Else return the actual value
		return $value;
	}
}
//#section_end#
?>