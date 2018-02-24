<?php
//#section#[header]
// Namespace
namespace WUI\Website;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Website
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Resources", "DOMParser");
importer::import("WAPI", "Literals", "literal");
importer::import("WUI", "Content", "HTMLContent");
importer::import("WUI", "Html", "HTML");

use \WAPI\Resources\DOMParser;
use \WAPI\Literals\literal;
use \WUI\Content\HTMLContent;
use \WUI\Html\HTML;

/**
 * Website Page Builder
 * 
 * Builds a website page with a specified container id and class.
 * It loads page's html and can parse website's literals.
 * 
 * @version	1.0-2
 * @created	October 1, 2014, 13:19 (EEST)
 * @revised	December 8, 2014, 15:23 (EET)
 */
class pageView extends HTMLContent
{
	/**
	 * Build the outer html content container.
	 * 
	 * @param	string	$id
	 * 		The element's id. Empty by default.
	 * 
	 * @param	string	$class
	 * 		The element's class. Empty by default.
	 * 
	 * @param	boolean	$loadHTML
	 * 		Indicator whether to load html from the designer file.
	 * 
	 * @return	pageView
	 * 		The pageView object.
	 */
	public function build($id = "", $class = "", $loadHTML = FALSE)
	{
		// Build HTMLContent
		parent::build($id, $class, $loadHTML);
		
		// Load page static literals
		$this->loadLiterals();
		
		// Return MContent object
		return $this;
	}
	
	/**
	 * Loads website's literals in the designer's html file.
	 * 
	 * @return	pageView
	 * 		The pageView object.
	 */
	protected function loadLiterals()
	{
		// Load application literals
		$containers = HTML::evaluate("//*[@data-literal]");
		foreach ($containers as $container)
		{
			// Get literal attributes
			$value = HTML::attr($container, "data-literal");
			$attributes = json_decode($value, TRUE);

			// Get literal
			$literal = literal::get($attributes['scope'], $attributes['name']);
			
			// If literal is valid, append to container
			if (!empty($literal))
				HTML::append($container, $literal);
			
			// Remove literal attribute
			HTML::attr($container, "data-literal", null);
		}
		
		return $this;
	}
	
	/**
	 * Get the page content object.
	 * 
	 * @return	mixed
	 * 		The page content element as a DOMElement or as HTML (depending on the call).
	 */
	public function getContent()
	{
		// Get DOMElement object as content
		$content = parent::get();
		
		// Parse and get HTML
		$parser = new DOMParser();
		$context = $parser->import($content);
		$parser->append($context);
		
		// Replace old node
		HTML::replace($content, NULL);
		
		// Return page as an Html String
		return $parser->getHTML();
	}
	
	/**
	 * Gets the parent's filename for loading the html from external file.
	 * 
	 * @return	string
	 * 		The parent script name.
	 */
	protected function getParentFilename()
	{
		$stack = debug_backtrace();
		return $stack[3]['file'];
	}
}
//#section_end#
?>