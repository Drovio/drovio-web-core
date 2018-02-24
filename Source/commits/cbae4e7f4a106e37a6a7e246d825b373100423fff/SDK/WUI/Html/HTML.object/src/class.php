<?php
//#section#[header]
// Namespace
namespace WUI\Html;


//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Html
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Prototype", "DOMPrototype");

use \DOMXPath;
use \DOMDocument;
use \WAPI\Prototype\DOMPrototype;

/**
 * HTML Handler
 * 
 * HTML extends DOMPrototype for DOM and html specific functions.
 * 
 * @version	0.1-2
 * @created	June 21, 2014, 13:19 (EEST)
 * @revised	September 29, 2014, 17:02 (EEST)
 */
class HTML extends DOMPrototype
{
	/**
	 * The page document
	 * 
	 * @type	DOMDocument
	 */
	protected static $document;
	
	/**
	 * Creates and returns a DOMElement with the specified tagName and the given attributes
	 * 
	 * @param	string	$tag
	 * 		The tag of the element.
	 * 
	 * @param	mixed	$content
	 * 		The content of the element. It can be a string or a DOMElement.
	 * 
	 * @param	string	$id
	 * 		The id attribute
	 * 
	 * @param	string	$class
	 * 		The class attribute
	 * 
	 * @return	DOMElement
	 * 		The DOMElement
	 */
	public static function create($tag = "div", $content = "", $id = "", $class = "")
	{
		// Check if the content is string or a DOMElement
		if (gettype($content) == "string")
		{
			$elem = self::$document->createElement($tag);
			$txtNode = self::$document->createTextNode($content);
			$elem->appendChild($txtNode);
		}
		else
		{
			$elem = self::$document->createElement($tag);
			if (gettype($content) == "object")
				$elem->appendChild($content);
		}
		

		if (!is_null($id) && !empty($id))
			self::attr($elem, "id", $id);
			
		if (!is_null($class) && !empty($class))
			self::attr($elem, "class", $class);
		
		return $elem;
	}
	
	/**
	 * Evaluate an XPath Query
	 * 
	 * @param	string	$query
	 * 		The XPath query to be evaluated
	 * 
	 * @param	DOMElement	$context
	 * 		if content is not NULL the query will be relative to it.
	 * 
	 * @return	DOMNodeList
	 * 		Returns the node list that matches the given XPath Query.
	 */
	public static function evaluate($query, $context = NULL)
	{
		$xpath = new DOMXPath(self::$document);
		return $xpath->evaluate($query, $context);
	}
	
	/**
	 * Find an element by id (using the evaluate function).
	 * 
	 * @param	string	$id
	 * 		The id of the elemeThe node name of the element.
	 * 
	 * @param	string	$nodeName
	 * 		The node name of the element. If not set, it searches for all nodes (*).
	 * 
	 * @return	mixed
	 * 		Returns the DOMElement or NULL if it doesn't exist.
	 */
	public static function find($id, $nodeName = "*")
	{
		$nodeName = (empty($nodeName) ? "*" : $nodeName);
		$q = "//".$nodeName."[@id='$id']";
		$list = self::evaluate($q);
		
		if ($list->length > 0)
			return $list->item(0);
			
		return NULL;
	}
	
	/**
	 * Create an html comment.
	 * 
	 * @param	string	$content
	 * 		The comment content.
	 * 
	 * @return	DOMNode
	 * 		Returns the comment element with the given content.
	 */
	public static function comment($content)
	{
		return self::$document->createComment($content);
	}
	
	/**
	 * Imports a node to this document.
	 * 
	 * @param	DOMNode	$node
	 * 		The node to be imported
	 * 
	 * @param	boolean	$deep
	 * 		Defines whether all the children of this node will be imported.
	 * 
	 * @return	DOMNode
	 * 		Returns the new node.
	 */
	public static function import($node, $deep = TRUE)
	{
		if (empty($node))
			return NULL;
		return self::$document->importNode($node, $deep);
	}
	
	/**
	 * Returns the HTML form of the document
	 * 
	 * @param	boolean	$format
	 * 		Indicates whether to format the output.
	 * 
	 * @return	string
	 * 		Returns the HTML form of the document
	 */
	public static function getHTML($format = FALSE)
	{
		self::$document->formatOutput = $format;
		return self::$document->saveHTML();
	}
	
	/**
	 * Returns the XML form of the document
	 * 
	 * @param	boolean	$format
	 * 		Indicates whether to format the output.
	 * 
	 * @return	string
	 * 		Returns the XML form of the document
	 */
	public static function getXML($format = FALSE)
	{
		self::$document->formatOutput = $format;
		return self::$document->saveXML();
	}
	
	/**
	 * Initializes and clears the  DOMDocument
	 * 
	 * @return	void
	 */
	public static function initialize()
	{
		self::$document = new DOMDocument("1.0", "UTF-8");
	}
	
	/**
	 * Clears the DOMDocument
	 * 
	 * @return	void
	 */
	public static function clear()
	{
		$root = self::evaluate("/")->item(0);
		foreach ($root->childNodes as $child)
			self::replace($child, NULL);
	}
	
	/**
	 * Get the DOMDocument
	 * 
	 * @return	DOMDocument
	 * 		Returns the DOMDocument
	 */
	public static function document()
	{
		return self::$document;
	}
	
	/**
	 * Adds a class to the given DOMElement.
	 * 
	 * @param	DOMElement	$elem
	 * 		The element to add the class.
	 * 
	 * @param	string	$class
	 * 		The class name.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure or if the class already exists.
	 */
	public static function addClass($elem, $class)
	{
		// Get current class
		$currentClass = trim(parent::attr($elem, "class"));
		
		// Check if class already exists
		$classes = explode(" ", $currentClass);
		if (in_array($class, $classes))
			return FALSE;
		
		// Append new class
		return parent::appendAttr($elem, "class", $class);
	}
	
	/**
	 * Removes a class from a given DOMElement.
	 * 
	 * @param	DOMElement	$elem
	 * 		The element to remove the class.
	 * 
	 * @param	string	$class
	 * 		The class name.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure or if the class already exists.
	 */
	public static function removeClass($elem, $class)
	{
		// Get current class
		$currentClass = trim(parent::attr($elem, "class"));
		
		// Check if class doesn't exists
		$classes = explode(" ", $currentClass);
		$classKey = array_search($class, $classes);
		if (!$classKey)
			return FALSE;
		
		// Remove class
		unset($classes[$classKey]);
		
		// Set new class
		$newClass = implode(" ", $classes);
		return parent::attr($elem, "class", $newClass);
	}
	
	/**
	 * Selects nodes in the html document that match a given css selector.
	 * 
	 * @param	string	$css
	 * 		The css selector to search for in the html document.It does not support pseudo-* for the moment and only supports simple equality attribute-wise.
	 * 
	 * @param	mixed	$context
	 * 		Can either be a DOMElement as the context of the search, or a css selector.
	 * 
	 * @return	mixed
	 * 		Returns the node list that matches the given css selector, or FALSE on malformed input.
	 */
	public static function select($css, $context = NULL)
	{
		// _xpcm_ -> ',' {comma}
		// _xpsp_ -> ' ' {space}
		// _xpor_ -> ' or ' {xpath or clause}
	
		// Prepare css selector
		$css = preg_replace("/[ \t\r\n\s]+/", " ", $css);
		
		// Transform css to xpath
		$xpath = $css;
		
		// Identify Attributes
		$xpath = str_replace("[", "[@", $xpath);
		
		// Identify IDs
		$xpath = preg_replace("/\#(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/", "[@id='$1']", $xpath);
		
		// Identify Classes
		$xpath = preg_replace("/\.(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/", "[contains(concat('_xpsp_'_xpcm_@class_xpcm_'_xpsp_')_xpcm_'_xpsp_$1_xpsp_')]", $xpath);
		//$xpath = preg_replace("/\.(-?[_a-zA-Z]+[_a-zA-Z0-9-]*)/", "[contains(@class_xpcm_'$1')]", $xpath);
		
		// Identify root
		if (empty($context))
			$xpath = preg_replace("/[^,]+/", "//$0", $xpath);
			
		// Identify Descendants
		$xpath = preg_replace("/([^>~+])([ ])([^>~+])/", "$1//$2$3", $xpath);
		
		// Identify Children
		$xpath = str_replace(">", "/", $xpath);
		
		// Identify Direct Next siblings
		//$xpath = preg_replace("/\+[ ]+([^ >+~,\/]*(\[.*?\])?[^ >+~,\/]*)/", "/following-sibling::$1[1]", $xpath);
		// Identify Next siblings
		//$xpath = preg_replace("/\~[ ]+([^ >+~,\/]*(\[.*?\])?[^ >+~,\/]*)/", "/following-sibling::$1", $xpath);
	
		// Identify multiple selectors
		$xpath = str_replace(" ", "", $xpath);
		$xpath = str_replace(",", " | ", $xpath);
		
		// Identify "orphans" [no element, just attributes]
		$xpath = str_replace("/[", "/*[", $xpath);
		
		// Restore commas, spaces and or in functions
		$xpath = str_replace("_xpcm_", ",", $xpath);
		$xpath = str_replace("_xpsp_", " ", $xpath);
		//$xpath = str_replace("_xpor_", " or ", $xpath);

		// Get the context node if css context
		if (!empty($context) && is_string($context))
		{
			$ctxList = self::select($context);
			if (empty($ctxList) || empty($ctxList->length))
			{
				logger::log("HTML::select(). Context node is not found.", logger::ERROR);
				return FALSE;
			}
			
			$context = $ctxList->item(0);
		}	
		
		// Evaluate xpath and return the node list
		return self::evaluate($xpath, $context);
	}
}
//#section_end#
?>