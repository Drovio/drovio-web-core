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

importer::import("WAPI", "Resources", "DOMParser");
importer::import("WAPI", "Environment", "session");
importer::import("WAPI", "Environment", "cookies");

use \WAPI\Resources\DOMParser;
use \WAPI\Environment\session;
use \WAPI\Environment\cookies;

/**
 * Website Locale Manager
 * 
 * Handles all about languages and locales
 * 
 * @version	0.1-2
 * @created	October 1, 2014, 15:02 (EEST)
 * @revised	November 27, 2014, 21:24 (EET)
 */
class locale
{
	/**
	 * The website base locale.
	 * 
	 * @type	string
	 */
	private static $defaultLocale = "";
	
	/**
	 * Get the website base locale.
	 * 
	 * @return	string
	 * 		The website's base locale.
	 */
	public static function getDefault()
	{
		if (empty(self::$defaultLocale))
			self::$defaultLocale = "en_us";
			
		return self::$defaultLocale;
	}
	
	
	/**
	 * Set the website's current region and locale for the user.
	 * 
	 * @param	string	$locale_mixed
	 * 		The locale mixed as [region]:[locale].
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function set($locale_mixed)
	{
		list($region, $locale) = explode(":", $locale_mixed);

		// Set Cookies for a year
		cookies::set("region", $region, (365 * 24 * 60 * 60));
		cookies::set("locale", $locale, (365 * 24 * 60 * 60));
		
		session::set("locale", $locale, $namespace = "geoloc");
		session::set("region", $region, $namespace = "geoloc");
	
		if (session::get("locale", NULL, $namespace = "geoloc") == $locale && session::get("region", NULL, $namespace = "geoloc") == $region)
			return TRUE;
		return FALSE;
	}
	
	/**
	 * Get the current active locale.
	 * 
	 * @return	string
	 * 		The locale string.
	 */
	public static function get()
	{
		// Get cookie
		$locale = cookies::get("locale");
		
		// If not set as cookie, check session
		if (!isset($locale) || $locale == NULL)
		{
			// Get from session
			$locale = session::get("locale", NULL, $namespace = "geoloc");
			
			// If not set as session, set default
			if (!isset($locale))
				$locale = self::getDefault();
				
			// Set Cookie for a year
			cookies::set("locale", $locale, (365 * 24 * 60 * 60));
		}
		
		session::set("locale", $locale, "geoloc");
		return $locale;
	}
}
//#section_end#
?>