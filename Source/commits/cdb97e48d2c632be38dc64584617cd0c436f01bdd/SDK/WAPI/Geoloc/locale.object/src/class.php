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
 * 
 * @copyright	Copyright (C) 2015 DrovIO. All rights reserved.
 */

importer::import("WAPI", "Environment", "session");
importer::import("WAPI", "Environment", "cookies");
importer::import("WAPI", "Resources", "DOMParser");
importer::import("WAPI", "Platform", "engine");

use \WAPI\Environment\session;
use \WAPI\Environment\cookies;
use \WAPI\Platform\engine;
use \WAPI\Resources\DOMParser;

/**
 * Website Locale Manager
 * 
 * Handles all about languages and locales
 * 
 * @version	2.0-1
 * @created	October 1, 2014, 15:02 (EEST)
 * @updated	July 16, 2015, 17:07 (EEST)
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
	 * The current locale as class cache.
	 * 
	 * @type	string
	 */
	private static $currentLocale = "";
	
	/**
	 * Whether locale is already initiated.
	 * 
	 * @type	boolean
	 */
	private static $initiated = FALSE;
	
	/**
	 * Initialize locale for the current script.
	 * 
	 * @return	void
	 */
	public static function init()
	{
		// Check if locale is already initiated
		if (self::$initiated)
			return;
		
		// Check if there is a locale cookie already
		$lc_cookie = cookies::get("lc");
		if (!empty($lc_cookie))
			return;
			
		// Get browser accept locale (get first available)
		$browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		$browserLang = str_replace("-", "_", $browserLang);
		
		// Get all active locale to validate
		$activeLocale = self::active();
		$allLocale = array();
		foreach ($activeLocale as $localeInfo)
		{
			$locale = $localeInfo['locale'];
			$short = substr($locale, 0, 2);
			$allLocale[$locale] = $locale;
			$allLocale[$short] = $locale;
		}
		$browserLocale = $allLocale[$browserLang];
		$currentLocale = (empty($browserLocale) ? locale::getDefault() : $browserLocale);
		
		// Set current locale
		self::$currentLocale = $currentLocale;
		self::$initiated = TRUE;
	}
	
	/**
	 * Get the website base locale.
	 * 
	 * @return	string
	 * 		The website's base locale.
	 */
	public static function getDefault()
	{
		return "en_US";
	}
	
	/**
	 * Set the website's current locale for the user.
	 * 
	 * @param	string	$locale
	 * 		The locale to set.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function set($locale)
	{
		// Set Cookies for a year
		cookies::set("lc", $locale, (365 * 24 * 60 * 60));
		
		// Set class cache
		self::$currentLocale = $locale;
	}
	
	/**
	 * Get the current active locale.
	 * 
	 * @return	string
	 * 		The locale string.
	 */
	public static function get()
	{
		// Get cache
		if (!empty(self::$currentLocale))
			return self::$currentLocale;
			
		// Get locale
		$locale = engine::getVar("lc");

		// If not set as cookie, check session
		if (!isset($locale) || $locale == NULL || $locale == 'deleted')
			return self::getDefault();
		
		return $locale;
	}
	
	/**
	 * Get all locale that are supported by the website.
	 * 
	 * @return	array
	 * 		An array of all supported locale.
	 */
	public static function active()
	{
		// Check session
		$activeLocale = session::get("active_locale", NULL, $namespace = "geoloc");
		if (empty($activeLocale))
		{
			// Load active locale from locale.xml
			$parser = new DOMParser();
			try
			{
				$parser->load();
			}
			catch (Exception $ex)
			{
				return array();
			}
			
			$activeLocale = array();
			session::set("active_locale", $activeLocale, $namespace = "geoloc");
		}
		
		return $activeLocale;
	}
	
	/**
	 * Gets the locale's info.
	 * 
	 * @param	string	$locale
	 * 		The requested locale.
	 * 		If left empty, get the current locale.
	 * 
	 * @return	array
	 * 		An array of information about the current locale, the country and the language of this locale.
	 */
	public static function info($locale = "")
	{
		return array();
	}
}
//#section_end#
?>