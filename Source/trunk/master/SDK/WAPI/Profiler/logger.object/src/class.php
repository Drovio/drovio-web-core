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
 * @copyright	Copyright (C) 2015 Redback. All rights reserved.
 */

importer::import("WAPI", "Resources", "filesystem/fileManager");
importer::import("WAPI", "Resources", "filesystem/directory");

use \WAPI\Resources\filesystem\fileManager;
use \WAPI\Resources\filesystem\directory;

/**
 * Logger
 * 
 * A class to log errors occuring during the web core execution
 * 
 * @version	2.0-1
 * @created	December 19, 2014, 21:07 (EET)
 * @updated	January 13, 2015, 21:32 (EET)
 */
abstract class logger
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
		// Get txt log file
		$logFile = $this->getLogFileByTime();
		if (empty($logFile))
			return FALSE;
		
		// Check if file exists and create it
		if (!file_exists(systemRoot.$logFile))
			fileManager::create(systemRoot.$logFile, "", TRUE);
		
		// Set timestamp
		$timestamp = date('D M d, Y, H:i:s', time());
		
		// Get log type
		$type = self::getLevelName($level);
		if (empty($type))
			$type = self::getLevelName(self::DEBUG);
		
		// Set full description
		$description = trim($description);
		$descriptionFull = "[".$timestamp."] [".$type."] ".$description."\n";
		
		// Add new log entry
		return fileManager::put(systemRoot.$logFile, $descriptionFull, FILE_APPEND | LOCK_EX);
	}
	
	/**
	 * Gets the level name given the level code.
	 * 
	 * @param	integer	$level
	 * 		The log level code.
	 * 
	 * @return	string
	 * 		The level name or NULL if given level is not valid.
	 */
	public static function getLevelName($level)
	{
		switch ($level)
		{
			case self::EMERGENCY:
				return "emergency";
				break;
			case self::ALERT:
				return "alert";
				break;
			case self::CRITICAL:
				return "critical";
				break;
			case self::ERROR:
				return "error";
				break;
			case self::WARNING:
				return "warning";
				break;
			case self::NOTICE:
				return "notice";
				break;
			case self::INFO:
				return "info";
				break;
			case self::DEBUG:
				return "debug";
				break;
		}
		
		// No valid level
		return NULL;
	}
	
	/**
	 * Get the website's log folder.
	 * 
	 * @return	string
	 * 		The website's log folder.
	 */
	protected function getLogFolder();
}
//#section_end#
?>