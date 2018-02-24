<?php
//#section#[header]
// Namespace
namespace WAPI\Geoloc;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Geoloc
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Environment", "session");
importer::import("WUI", "Html", "HTML");

use \WAPI\Environment\session;
use \WUI\Html\HTML;

/**
 * Date time manager
 * 
 * Manages the stored date the time and handles how they will be displayed (in the proper timezone) to the user.
 * 
 * @version	0.1-2
 * @created	June 24, 2014, 16:32 (EEST)
 * @revised	November 28, 2014, 9:40 (EET)
 */
class datetimer
{
	/**
	 * The auto timezone value.
	 * 
	 * @type	string
	 */
	const AUTO = "auto";
	
	/**
	 * Inits the datetimer and sets the timezone according to user's location.
	 * 
	 * @return	void
	 */
	public static function init()
	{
		// Load timezone
		$timezone = self::loadTimezone();
		
		// Set timezone
		self::setTimezone($timezone);
	}
	
	/**
	 * Sets the current timezone for the system and for the user.
	 * 
	 * @param	string	$timezone
	 * 		The timezone to be set.
	 * 
	 * @return	void
	 */
	public static function setTimezone($timezone)
	{
		// Check empty timezone
		if (empty($timezone))
			return;
			
		// Set php timezone
		date_default_timezone_set($timezone);
		
		// Session set
		session::set("timezone", $timezone, "geoloc");
	}
	
	/**
	 * The time format to display on hover (php time format).
	 * If timezone is set to auto, the timezone will be auto determined from the user's location.
	 * 
	 * @return	void
	 */
	private static function loadTimezone()
	{
		// Get from session
		$timezone = session::get("timezone", NULL, "geoloc");
		if (!is_null($timezone))
			return $timezone;
		
		// Get user's location and set default timezone for display time
		//$ip = $_SERVER;
		
		// This is TEMP
		return "Europe/Athens";
	}
	
	/**
	 * Creates an element that displays a live timestamp (updates with an interval of 30 seconds).
	 * 
	 * @param	integer	$time
	 * 		The unix timestamp.
	 * 
	 * @param	string	$format
	 * 		The time format to display on hover (php time format).
	 * 
	 * @return	DOMElement
	 * 		The abbr element.
	 */
	public static function live($time = "", $format = 'd F, Y \a\t H:i')
	{
		// Get time
		$time = (empty($time) ? time() : $time);
		
		// Get title
		$title = date($format, $time);
		
		// Create timespan
		$timespan = HTML::create("abbr", "", "", "timestamp live");
		HTML::attr($timespan, "data-utime", $time);
		HTML::attr($timespan, "data-format", $format);
		HTML::attr($timespan, "title", $title);
		
		return $timespan;
	}
}
//#section_end#
?>