<?php
//#section#[header]
// Namespace
namespace WAPI\Resources\literals;


//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Resources
 * @namespace	\literals
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Geoloc", "locale");
importer::import("WUI", "Html", "HTML");
importer::import("WAPI", "Resources", "DOMParser");

use \WAPI\Resources\DOMParser;
use \API\Geoloc\locale;
use \WUI\Html\HTML;

/**
 * Literal Controller
 * 
 * A controller class. Chooses where to get the literals from.
 * 
 * @version	0.1-1
 * @created	October 3, 2014, 20:29 (EEST)
 * @revised	October 3, 2014, 20:29 (EEST)
 */
class literalController
{
	/**
	 * Get all literals from the given scope
	 * 
	 * @param	string	$scope
	 * 		The literal's scope.
	 * 
	 * @param	string	$locale
	 * 		The locale to get the literals from.
	 * 		If NULL, get the current system locale.
	 * 
	 * @return	array
	 * 		An array of all literals in the requested locale.
	 * 		They are separated in 'translated' and 'nonTranslated' groups nested in the array.
	 */
	public static function get($scope, $locale = NULL)
	{
		// Log activity
		//logger::log("Loading literals from '".$scope."' scope ...", logger::INFO);
		
		/*
		$dbq = new dbQuery("1169740592", "resources.literals");
		$dbc = new interDbConnection();
		
		// Set Query Attributes
		$attr = array();
		$attr['scope'] = $scope;
		
		// Get Literals in the default locale
		$attr['locale'] = locale::getDefault();
		$defaultResult = $dbc->execute($dbq, $attr);
		
		$defaultArray = array();
		$defaultArray = $dbc->toArray($defaultResult, "name", "value");
		
		// Get Literals in the requested locale
		$attr['locale'] = (empty($locale) ? locale::get() : $locale);
		$currentResult = $dbc->execute($dbq, $attr);
		
		$currentArray = array();
		$currentArray = $dbc->toArray($currentResult, "name", "value");
		
		$literalsArray = array();
		$literalsArray['translated'] = $currentArray;
		$literalsArray['nonTranslated'] = $defaultArray;
		
		return $literalsArray;
		*/


		return array();

	}
	
	/**
	 * Wrap a literal to span.
	 * 
	 * @param	string	$value
	 * 		The literal's value
	 * 
	 * @return	DOMElement
	 * 		The literal span.
	 */
	public static function wrap($value)
	{
		$span = HTML::create("span");
		HTML::innerHTML($span, $value);
		
		return $span;
	}
}
//#section_end#
?>