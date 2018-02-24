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

importer::import("WAPI", "Environment", "session");
importer::import("WAPI", "Geoloc", "datetimer");
importer::import("WAPI", "Protocol", "http/HTTPRequest");

use \WAPI\Environment\session;
use \WAPI\Geoloc\datetimer;
use \WAPI\Protocol\http\HTTPRequest;

/**
 * Website Engine
 * 
 * Class responsible for starting and pausing the website engine.
 * 
 * @version	1.0-1
 * @created	October 1, 2014, 13:52 (EEST)
 * @updated	July 16, 2015, 17:06 (EEST)
 */
class engine
{
	/**
	 * Starts debugger, session and user preferences.
	 * 
	 * @return	void
	 */
	public static function start()
	{
		// Set default max execution time limit
		set_time_limit(30);
		
		// Init HTTPRequest variables
		HTTPRequest::init();
		
		// Start Debugger
		
		// Start Session
		session::init();
		
		// Init datetimer
		datetimer::init();
		
		// Set default max execution time limit
		set_time_limit(30);
	}
	
	/**
	 * Restarts the engine. Shutdown and then start again.
	 * 
	 * @return	void
	 */
	public static function restart()
	{
		// Shutdown Engine
		self::shutdown();
		
		// Start again
		self::start();
	}
	
	/**
	 * Sets in suspension (with the user's log out or switch account) the system platform.
	 * 
	 * @return	void
	 */
	public static function shutdown()
	{
		// Destroy session
		session::destroy();
		
		// End Debugger
	}
	
	/**
	 * Get a variable from the request.
	 * It uses the HTTPRequest to get the variable.
	 * 
	 * This is based on the user request and supports GET, POST and COOKIES. It works independent and the user doesn't know (doesn't have to know) where the variable comes from.
	 * 
	 * @param	string	$name
	 * 		The variable name to get.
	 * 
	 * @return	mixed
	 * 		The variable value or NULL if the variable doesn't exist in the requested scope.
	 */
	public static function getVar($name)
	{
		return HTTPRequest::getVar($name, $name);
	}
	
	/**
	 * Checks if it is a POST request.
	 * It serves not to check implicit with the HTTPRequest.
	 * 
	 * @param	boolean	$includePUT
	 * 		Set to include check for PUT request method.
	 * 		It is FALSE by default.
	 * 
	 * @return	boolean
	 * 		True if it is a POST (or a PUT, depending on the first parameter) request, false otherwise.
	 */
	public static function isPost($includePUT = FALSE)
	{
		// If include PUT method, return TRUE if one of them is valid
		if ($includePUT)
			return self::requestMethod("POST") || self::requestMethod("PUT");
		
		// Check if the request method is POST
		return self::requestMethod("POST");
	}
	
	/**
	 * Checks if the HTTPRequest request method is the same as the variable given.
	 * 
	 * @param	string	$type
	 * 		The request method to check.
	 * 
	 * @return	boolean
	 * 		True if the variable has the same value, false otherwise.
	 */
	private static function requestMethod($type)
	{
		return (HTTPRequest::requestMethod() == strtoupper($type));
	}
}
//#section_end#
?>