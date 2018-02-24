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

importer::import("WAPI", "Geoloc", "datetimer");
importer::import("WAPI", "Resources", "storage::session");

use \WAPI\Geoloc\datetimer;
use \WAPI\Resources\storage\session;

/**
 * Website Engine
 * 
 * Class responsible for starting and pausing the website engine.
 * 
 * @version	0.1-1
 * @created	October 1, 2014, 13:52 (EEST)
 * @revised	October 1, 2014, 13:52 (EEST)
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
}
//#section_end#
?>