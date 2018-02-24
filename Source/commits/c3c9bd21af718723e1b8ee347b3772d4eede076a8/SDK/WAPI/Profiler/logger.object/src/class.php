<?php
//#section#[header]
// Namespace
namespace WAPI\Profiler;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Profiler
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

/**
 * Logger
 * 
 * A class to log errors occuring during the web core execution
 * 
 * @version	1.0-1
 * @created	December 19, 2014, 21:07 (EET)
 * @revised	December 19, 2014, 21:28 (EET)
 */
class logger
{
	/**
	 * The system is unusable.
	 * 
	 * @type	integer
	 */
	const EMERGENCY = 1;
	/**
	 * Action must be taken immediately.
	 * 
	 * @type	integer
	 */
	const ALERT = 2;
	/**
	 * Critical cCritical conditions.onditions.
	 * 
	 * @type	integer
	 */
	const CRITICAL = 4;
	/**
	 * Error conditions.
	 * 
	 * @type	integer
	 */
	const ERROR = 8;
	/**
	 * Warning conditions.
	 * 
	 * @type	integer
	 */
	const WARNING = 16;
	/**
	 * Normal, but significant condition.
	 * 
	 * @type	integer
	 */
	const NOTICE = 32;
	/**
	 * Informational message.
	 * 
	 * @type	integer
	 */
	const INFO = 64;
	/**
	 * Debugging message.
	 * 
	 * @type	integer
	 */
	const DEBUG = 128;

	/**
	 * The logger instance.
	 * 
	 * @type	logger
	 */
	private static $instance;
	
	/**
	 * The constructor Method
	 * 
	 * @return	void
	 */
	private function __construct() {}
	
	/**
	 * Get the logger instance object.
	 * 
	 * @return	logger
	 * 		The logger instance object.
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
			self::$instance = new logger();
		
		return self::$instance;
	}
	
	/**
	 * Creates a new entry log.
	 * 
	 * @param	string	$description
	 * 		The log description.
	 * 
	 * @param	integer	$level
	 * 		The log level.
	 * 		Use the class constants.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public function log($description, $level = self::DEBUG)
	{
		
		return TRUE;
	}
}
//#section_end#
?>