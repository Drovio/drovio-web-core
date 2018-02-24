<?php
//#section#[header]
// Namespace
namespace WUI\Html\components;


//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Html
 * @namespace	\components
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WUI", "Prototype", "UIObjectPrototype");
importer::import("WUI", "Html", "HTML");

use \WUI\Prototype\UIObjectPrototype;
use \WUI\Html\HTML;

/**
 * Hyperlink Builder
 * 
 * Builds in one line a weblink with all the attributes.
 * 
 * @version	0.1-1
 * @created	October 1, 2014, 0:33 (EEST)
 * @revised	October 1, 2014, 0:33 (EEST)
 */
class weblink extends UIObjectPrototype
{
	/**
	 * Builds the weblink DOMElement
	 * 
	 * @param	string	$href
	 * 		The href attribute value.
	 * 
	 * @param	string	$target
	 * 		The target value.
	 * 
	 * @param	mixed	$content
	 * 		The content of the hyperlink.
	 * 		It can be text or a DOMElement.
	 * 
	 * @return	weblink
	 * 		The weblink object.
	 */
	public function build($href = "", $target = "_self", $content = "")
	{
		// Normalize content
		if (!empty($content) && gettype($content) == "string")
			$content = HTML::create("span", $content);
			
		// Create element
		$element = HTML::create("a", $content);
		$this->set($element);
		
		// Set Navigation
		HTML::attr($element, "href", $href);
		HTML::attr($element, "target", $target);
		
		// Return weblink
		return $this;
	}
}
//#section_end#
?>