<?php
//#section#[header]
// Namespace
namespace WAPI\Literals;


//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Literals
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Resources", "DOMParser");

use \WAPI\Resources\DOMParser;

/**
 * Website Literal Manager
 * 
 * Get all website literals.
 * 
 * @version	0.1-1
 * @created	November 27, 2014, 21:23 (EET)
 * @revised	November 27, 2014, 21:23 (EET)
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
		// Check if literals are already loaded
		if (!isset(self::$literals[$locale][$scope]))
			self::$literals[$locale][$scope] = self::getLiterals($scope, $locale);
		
		// If name not given, return all the scope literals
		if ($name == "")
			return self::$literals[$locale][$scope];
		
		// Get literal value
		$value = self::$literals[$locale][$scope][$name];
		
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
		// Get locale
		$defaultLocale = locale::getDefault();
		if (empty($locale))
			$locale = locale::get();
			
		// Get literals from content xml file
		$parser = new DOMParser();
		$literalsFile = paths::getWebsiteFolder()."/literals/".$locale.".xml";
		try
		{
			$parser->load($literalsFile);
		}
		catch (Exception $ex)
		{
			// Check if scope exists in default locale
			if ($locale != $defaultLocale)
				return self::getLiterals($scope, $defaultLocale);
			
			// Return an empty array otherwise
			return array();
		}
		
		// Parse scope literals
		$scopeRoot = $parser->evaluate("//scope[@name='".$scope."']")->item(0);
		if (empty($scopeRoot))
			if ($locale != $defaultLocale)
				return self::getLiterals($scope, $defaultLocale);
			else
				return array();
		
		// Get literals
		$literals = array();
		$literalElements = $parser->evaluate("/literal", $scopeRoot);
		foreach ($literalElements as $literal
		{
			$literalName = $parser->attr($literal, "name");
			$literals[$literalName] = $parser->nodeValue($literal);
		}
		
		// Return array of literals
		return $literals;
	}
}
//#section_end#
?>