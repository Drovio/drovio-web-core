<?php
//#section#[header]
// Namespace
namespace WAPI\Literals;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Literals
 * 
 * @copyright	Copyright (C) 2015 DrovIO. All rights reserved.
 */

importer::import("WAPI", "Resources", "DOMParser");

use \WAPI\Resources\DOMParser;

/**
 * Website Literal Manager
 * 
 * Get all website literals.
 * 
 * @version	0.1-2
 * @created	November 27, 2014, 21:23 (EET)
 * @updated	July 14, 2015, 17:09 (EEST)
 */
class literal
{
	/**
	 * An array of all literals already loaded by locale and by scope.
	 * 
	 * @type	array
	 */
	protected static $literals;
	
	/**
	 * Get the literal(s) in the given scope.
	 * 
	 * @param	string	$scope
	 * 		The requested literal scope.
	 * 
	 * @param	string	$name
	 * 		The literal name.
	 * 		Leave empty to get all scope literals in an array.
	 * 		It is empty by default.
	 * 
	 * @param	array	$attributes
	 * 		An array of attributes to pass to the literal.
	 * 
	 * @param	string	$locale
	 * 		The locale to get the literals from.
	 * 		If NULL, get the current locale.
	 * 		It is NULL by default.
	 * 
	 * @return	mixed
	 * 		The literal value or all the literals of the given scope.
	 * 		If the literal doesn't exist in the current or given locale, it will try to return the default value.
	 * 		It will return an empty string if the literal doesn't exist in any locale.
	 */
	public static function get($scope, $name = "", $attributes = array(), $locale = NULL)
	{
		// Normalize literal
		$locale = (empty($locale) ? locale::get() : $locale);
		
		// Check if literals are already loaded
		if (!isset(self::$literals[$locale][$scope][$locale]))
			self::$literals[$locale][$scope][$locale] = self::getLiterals($scope, $locale);
		
		// If name not given, return all the scope literals
		if ($name == "")
			return self::$literals[$locale][$scope][$locale];
		
		// Get literal value
		$value = self::$literals[$locale][$scope][$locale][$name];
		
		// Pass attributes values to literal
		foreach ($attributes as $key => $attrValue)
			$value = str_replace("{".$key."}", $attrValue, $value);
		
		// Return value
		return $value;
			
	}
	
	/**
	 * Get all literals by locale in the given scope.
	 * 
	 * @param	string	$scope
	 * 		The literal scope.
	 * 
	 * @param	string	$locale
	 * 		The locale to get the literals from.
	 * 		If NULL, get the current locale.
	 * 		It is NULL by default.
	 * 
	 * @return	array
	 * 		An array of all literals in the given scope and locale.
	 */
	private static function getLiterals($scope, $locale = NULL)
	{
		// Normalize locale
		$locale = (empty($locale) ? locale::get() : $locale);
		
		// Get literals json from file
		$literals_json = fileManager::get(systemRoot.paths::getWebsiteFolder()."/literals/".$locale."/".$scope.".json");
		
		// Decode and update cache
		return json_decode($literals_json, TRUE);
	}
}
//#section_end#
?>