<?php
//#section#[header]
// Namespace
namespace WAPI\Prototype;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Prototype
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

use \DOMDocument;
use \DOMXPath;

/**
 * Abstract Document Object Model Prototype Class
 * 
 * It is used for every DOM operation on the server's side.
 * 
 * @version	0.1-3
 * @created	June 21, 2014, 12:47 (EEST)
 * @revised	November 28, 2014, 9:11 (EET)
 */
abstract class DOMPrototype
{
	/**
	 * Adds or gets an attribute from a DOMElement.
	 * 
	 * @param	DOMElement	$elem
	 * 		The element to insert/get the attribute.
	 * 
	 * @param	string	$name
	 * 		The name of the attribute.
	 * 
	 * @param	mixed	$val
	 * 		If the value is NULL or FALSE, the value is considered negative and the attribute will be removed.
	 * 		If the value is empty (null is not included), the attribute will be removed.
	 * 		Otherwise, the attribute will be set with the new value and the new value will be returned.
	 * 
	 * @return	mixed
	 * 		Returns FALSE if there is an error (see the log).
	 * 		Returns the attribute value otherwise.
	 */
	public static function attr($elem, $name, $val = "")
	{
		if (empty($elem))
			return FALSE;
		
		// If value is null or false, remove attribute
		if (is_null($val) || (is_bool($val) && $val === FALSE))
			return $elem->removeAttribute($name);
		
		// If val is empty (null is empty but is caught above), get attribute	
		if (empty($val))
			return $elem->getAttribute($name);
		
		// Check if id is valid
		if ($name == "id")
		{
			$match = preg_match("/^[a-zA-Z][\w\_\-\.\:]*$/i", $val);
		}
		
		// Set attribute
		if (is_bool($val) && $val === TRUE)
			$elem->setAttributeNode(new DOMAttr($name));
		else
			$elem->setAttribute($name, trim($val));
		
		return $val;
	}
	
	/**
	 * Adds or gets a series of attributes (in the form of an array) into a DOMElement
	 * 
	 * @param	DOMElement	$elem
	 * 		The element to insert the attributes.
	 * 
	 * @param	array	$val
	 * 		The array of attributes.	 The key is the name of the attribute.
	 * 
	 * @return	mixed
	 * 		The element attributes by name and value or void.
	 */
	public static function attrs($elem, $val = array())
	{
		if (empty($val))
		{
			// Get attributes
			$attrs = array();
			foreach($elem->attributes as $attr)
				$attrs[$attr->name] = $attr->value;

			return $attrs;
		}
		else if (is_array($val) && count($val) > 0)
			foreach ($val as $key => $value)
				self::attr($elem, $key, $value);
	}
	
	/**
	 * Appends the value of an attribute of a DOMElement.
	 * 
	 * @param	DOMElement	$elem
	 * 		The element to append the attribute of.
	 * 
	 * @param	string	$name
	 * 		The name of the attribute
	 * 
	 * @param	string	$val
	 * 		The value of the attribute
	 * 
	 * @return	boolean
	 * 		TRUE on success, FALSE on failure.
	 */
	public static function appendAttr($elem, $name, $val)
	{
		$val = trim($val);
		if (empty($elem))
			return FALSE;
		
		// Create new attribute value
		$old_val = $elem->getAttribute($name);
		$new_val = trim($old_val)." ".$val;
		
		// Set new attribute value
		self::attr($elem, $name, $new_val);
		
		return TRUE;
	}
	
	/**
	 * Inserts a data-[name] attribute as an array into the DOMElement
	 * 
	 * @param	DOMElement	$elem
	 * 		The element to insert the attribute.
	 * 
	 * @param	string	$name
	 * 		The data name of the attribute (data-[name])
	 * 
	 * @param	array	$val
	 * 		The set of values as key=>value
	 * 
	 * @return	boolean
	 * 		TRUE on success, FALSE on failure.
	 */
	public static function data($elem, $name, $val = array())
	{
		if (!is_array($val) || count($val) == 0)
			return FALSE;
		
		// Clear empty values
		foreach ($val as $key => $attr)
			if (empty($attr) && $attr !== 0)
				unset($val[$key]);
		
		// Encode attribute data
		$dta_attr = json_encode($val, JSON_FORCE_OBJECT);

		// Don't add anything if empty
		$dta_attr = str_replace("{}", "", $dta_attr);
		self::attr($elem, 'data-'.$name, $dta_attr);
		
		return TRUE;		
	}
	
	/**
	 * Appends a DOMElement to a parent DOMElement or to the DOMDocument
	 * 
	 * @param	DOMElement	$parent
	 * 		The parent that will receive the DOMElement.
	 * 		If no child is given, this parent will be appended to DOMDocument.
	 * 
	 * @param	DOMElement	$child
	 * 		The element to append to the parent.
	 * 
	 * @return	void
	 */
	public static function append($parent, $child = NULL)
	{
		if (empty($parent))
			return FALSE;
			
		// Get Document
		$document = $parent->ownerDocument;
		
		if (empty($child))
			return $document->appendChild($parent);

		// Import Node
		$document->importNode($child, TRUE);
		
		// Insert the Node
		$parent->appendChild($child);
		return TRUE;
	}
	
	/**
	 * Prepends (appends first in the list) a DOMElement to a parent DOMElement
	 * 
	 * @param	DOMElement	$parent
	 * 		The parent element
	 * 
	 * @param	DOMElement	$child
	 * 		The child element.
	 * 
	 * @return	boolean
	 * 		TRUE on success, FALSE on failure.
	 */
	public static function prepend($parent, $child)
	{
		if (empty($child) || empty($parent))
			return FALSE;
		
		// Import Node
		$parent->ownerDocument->importNode($child, TRUE);
		
		// Insert the Node
		$parent->insertBefore($child, $parent->childNodes->item(0));
		return TRUE;
	}
	
	/**
	 * Appends a DOMElement to a parent DOMElement, before the given child.
	 * 
	 * @param	DOMElement	$parent
	 * 		The parent element.
	 * 
	 * @param	DOMElement	$before
	 * 		The element before which the child will be appended.
	 * 
	 * @param	DOMElement	$child
	 * 		The element that will be inserted
	 * 
	 * @return	boolean
	 * 		TRUE on success, FALSE on failure.
	 */
	public static function appendBefore($parent, $before, $child)
	{
		if (empty($child) || empty($parent) || empty($before))
			return FALSE;
		
		// Import Node
		$parent->ownerDocument->importNode($child, TRUE);
		$parent->insertBefore($before, $child);
		return TRUE;
	}
	
	/**
	 * Replaces or removes the old DOMElement with the new DOMElement
	 * 
	 * @param	DOMElement	$old
	 * 		The element to be replaced.
	 * 
	 * @param	DOMElement	$new
	 * 		The element to replace the old. If NULL, the old element will be removed.
	 * 
	 * @return	boolean
	 * 		TRUE on success, FALSE on failure.
	 */
	public static function replace($old, $new)
	{
		if (empty($old))
			return FALSE;
		
		// Remove or Remove
		if (empty($new))
			$old->parentNode->removeChild($old);
		else
			$old->parentNode->replaceChild($new, $old);
		
		return TRUE;
	}
	
	/**
	 * Sets or gets the nodeValue of the given DOMElement.
	 * Returns the old value or the DOMElement if value is set.
	 * 
	 * @param	DOMElement	$element
	 * 		The element to get its value.
	 * 
	 * @param	string	$value
	 * 		The value to be set. If empty, the element's value will be returned.
	 * 
	 * @return	string
	 * 		The node value (old or new).
	 */
	public static function nodeValue($element, $value = NULL)
	{
		if (is_null($value))
			return $element->nodeValue;
		
		$element->nodeValue = $value;
		return $value;
	}
	
	/**
	 * Returns the inner html of the element if no content is given.
	 * 
	 * Sets the innerHTML of an element elsewhere.
	 * 
	 * @param	DOMElement	$element
	 * 		The reference element.
	 * 
	 * @param	string	$value
	 * 		The html value to be set. If empty, it returns the innerHTML of the element.
	 * 
	 * @param	boolen	$faultTolerant
	 * 		Indicates whenever innerHTML will try to fix (well format html) the inserted string value.
	 * 
	 * @return	mixed
	 * 		Returns the innerHTML of the element if $value is NULL
	 * 		Otherwise sets the innerHTML of an element returning false in case of error.
	 */
	public static function innerHTML($element, $value = NULL, $faultTolerant = TRUE)
	{
		// If value is null, return inner HTML
		if (is_null($value) && !empty($element))
		{
			$inner = "";
			foreach ($element->childNodes as $child)
				$inner .= $element->ownerDocument->saveXML($child);
	
			return $inner;
		}
		
		// If value not null, set inner HTML
		
		//____ Empty the element 
		for ($x = $element->childNodes->length-1; $x >= 0; $x--)
			$element->removeChild($element->childNodes->item($x));

		//_____ $value holds our new inner HTML 
		if ($value == "")
			return FALSE;
		
		$f = $element->ownerDocument->createDocumentFragment(); 
		//_____ appendXML() expects well-formed markup (XHTML)
		$result = @$f->appendXML($value);
		
		if ($result)
		{
			if ($f->hasChildNodes())
				$element->appendChild($f); 
		}
		else
		{
			//$f = $element->ownerDocument;
			$f = new DOMDocument("1.0", "UTF-8");
			// $value is probably ill-formed 
			$value = mb_convert_encoding($value, 'HTML-ENTITIES', 'UTF-8');
			// Using <htmlfragment> will generate a warning, but so will bad HTML 
			// (and by this point, bad HTML is what we've got). 
			// We use it (and suppress the warning) because an HTML fragment will  
			// be wrapped around <html><body> tags which we don't really want to keep. 
			// Note: despite the warning, if loadHTML succeeds it will return true.
			$result = @$f->loadHTML('<htmlfragment>'.$value.'</htmlfragment>');
			if ($result && $faultTolerant)
			{ 
				$import = $f->getElementsByTagName('htmlfragment')->item(0);	
				foreach ($import->childNodes as $child)
				{ 										
					$importedNode = $element->ownerDocument->importNode($child, true);
					self::append($element, $importedNode);
				}
			}
			else
			{
				//Could not fix ill-html or we don't want to.
				return FALSE;
			}
		}
	}
}
//#section_end#
?>