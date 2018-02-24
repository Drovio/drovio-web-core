<?php
//#section#[header]
// Namespace
namespace WUI\Content;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Content
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Environment", "url");
importer::import("WAPI", "Resources", "filesystem::fileManager");
importer::import("WUI", "Prototype", "UIObjectPrototype");
importer::import("WUI", "Html", "HTML");
importer::import("WUI", "Html", "components::weblink");

use \WAPI\Environment\url;
use \WAPI\Resources\filesystem\fileManager;
use \WUI\Prototype\UIObjectPrototype;
use \WUI\Html\HTML;
use \WUI\Html\components\weblink;

/**
 * HTML Content Object
 * 
 * Creates an html content object for generic use.
 * Provides the right tools to enrich this object.
 * 
 * @version	1.0-2
 * @created	October 1, 2014, 0:44 (EEST)
 * @revised	November 28, 2014, 9:14 (EET)
 */
class HTMLContent extends UIObjectPrototype
{
	/**
	 * Build the outer html content container.
	 * 
	 * @param	string	$id
	 * 		The element's id.
	 * 
	 * @param	string	$class
	 * 		The element's class.
	 * 
	 * @param	boolean	$loadHTML
	 * 		Indicator whether to load html from the designer file.
	 * 
	 * @return	HTMLContent
	 * 		The HTMLContent object.
	 */
	public function build($id = "", $class = "", $loadHTML = FALSE)
	{
		// Initialize UI Element
		$element = HTML::create("div", "", $id, $class);
		$this->set($element);
		
		// Append object to DOM
		HTML::append($this->get());
		
		// Load html and return element
		if ($loadHTML)
			return $this->loadHTML();
		
		return $this;
	}
	
	/**
	 * Build the HTMLContent element with an element that the user built.
	 * 
	 * @param	DOMElement	$element
	 * 		The user's element.
	 * 
	 * @return	HTMLContent
	 * 		The HTMLContent object.
	 */
	public function buildElement($element)
	{
		$this->set($element);
		return $this;
	}
	
	/**
	 * Loads the html content from the script's designer file.
	 * 
	 * @return	HTMLContent
	 * 		The HTMLContent object.
	 */
	private function loadHTML()
	{
		// Get html file
		$parentFilename = $this->getParentFilename();
		$htmlDirectory = dirname($parentFilename);
		$htmlFileName = str_replace(".php", ".html", basename($parentFilename));
		$htmlFileName = $htmlDirectory."/".$htmlFileName;

		// Return html content
		$htmlContent = fileManager::get($htmlFileName);
		
		// Append to root element if not empty
		if (!empty($htmlContent))
			HTML::innerHTML($this->get(), $htmlContent);
		
		return $this;
	}
	
	/**
	 * Appends an element to the HTMLContent root element.
	 * 
	 * @param	DOMElement	$element
	 * 		The element to be appended.
	 * 
	 * @return	mixed
	 * 		Returns NULL if the element given is empty. Returns the HTMLContent object otherwise.
	 */
	public function append($element)
	{
		if (!empty($element))
			HTML::append($this->get(), $element);
		
		return $this;
	}
	
	/**
	 * Builds an html weblink.
	 * 
	 * @param	string	$href
	 * 		The weblink href attribute.
	 * 
	 * @param	mixed	$content
	 * 		The weblink content. It can be text or DOMElement.
	 * 
	 * @param	string	$target
	 * 		The target attribute. It is "_self" by default.
	 * 
	 * @return	DOMElement
	 * 		The weblink DOMElement object.
	 */
	public function getWeblink($href, $content = "", $target = "_self")
	{
		// Build the weblink
		$weblink = new weblink();
		return $weblink->build($href, $target, $content)->get();
	}
	
	/**
	 * Gets the parent's file name.
	 * 
	 * @return	string
	 * 		The parent's script name.
	 */
	protected function getParentFilename()
	{
		$stack = debug_backtrace();
		return $stack[2]['file'];
	}
}
//#section_end#
?>