<?php
//#section#[header]
// Namespace
namespace WUI\Prototype;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Prototype
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WUI", "Prototype", "UIObjectPrototype");
importer::import("WUI", "Html", "HTML");

use \WUI\Prototype\UIObjectPrototype;
use \WUI\Html\HTML;

/**
 * HTML Page Prototype/Builder
 * 
 * Helps building HTML Pages in HTML5 format
 * 
 * @version	1.0-1
 * @created	June 21, 2014, 18:31 (EEST)
 * @revised	October 1, 2014, 0:04 (EEST)
 */
class HTMLPagePrototype extends UIObjectPrototype
{
	/**
	 * The head tag object
	 * 
	 * @type	DOMElement
	 */
	protected $HTMLHead;
	
	/**
	 * The body tag object
	 * 
	 * @type	DOMElement
	 */
	protected $HTMLBody;
	
	/**
	 * Keeps the scripts to be inserted in the bottom of the page before exporting
	 * 
	 * @type	array
	 */
	private $bottomScripts;
	
	/**
	 * Constructor Method
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		// Initialize-Clear Bottom Scripts
		$this->bottomScripts = array();
		
		// Initialize DOM
		HTML::initialize();
	}
	
	/**
	 * Builds the spine of the page
	 * 
	 * @param	string	$title
	 * 		The title tag of the page. It is a required field for the document to be valid
	 * 
	 * @param	string	$description
	 * 		The description meta value
	 * 
	 * @param	string	$keywords
	 * 		The keywords meta value
	 * 
	 * @return	void
	 */
	public function build($title = "", $description = "", $keywords = "")
	{
		// Build HTML
		$HTML = HTML::create('html');
		$this->set($HTML);
		HTML::append($HTML);
		
		// Build HEAD
		$HTMLHead = HTML::create('head');
		HTML::append($HTML, $HTMLHead);
		$this->HTMLHead = $HTMLHead;
		
		// Build META
		$this->buildMeta($title, $description, $keywords);
		
		// Build BODY
		$HTMLBody = HTML::create('body');
		HTML::append($HTML, $HTMLBody);
		$this->HTMLBody = $HTMLBody;
		
		return $this;
	}
	
	/**
	 * Returns the entire HTML page in HTML5 format.
	 * 
	 * @return	string
	 * 		The html output.
	 */
	public function getHTML()
	{
		// Insert Bottom Scripts (if any)
		$this->flushBottomScripts();
		
		// Return text/html
		$output = '';
		$output .= "<!DOCTYPE html>";
		$output .= HTML::getHTML();
		return $output;
	}
	
	/**
	 * Returns the head tag object
	 * 
	 * @return	DOMElement
	 * 		The head tag object.
	 */
	public function getHead()
	{
		return $this->HTMLHead;
	}
	
	/**
	 * Returns the body tag object.
	 * 
	 * @return	DOMElement
	 * 		The body tag object.
	 */
	public function getBody()
	{
		return $this->HTMLBody;
	}
	
	/**
	 * Append element to head
	 * 
	 * @param	DOMElement	$element
	 * 		The element to be appended
	 * 
	 * @return	void
	 */
	protected function appendToHead($element)
	{
		if (is_null($element))
			return;
			
		HTML::append($this->HTMLHead, $element);
	}
	
	/**
	 * Append element to body
	 * 
	 * @param	DOMElement	$element
	 * 		The element to be appended
	 * 
	 * @return	void
	 */
	protected function appendToBody($element)
	{
		if (is_null($element))
			return;
			
		HTML::append($this->HTMLBody, $element);
	}
	
	/**
	 * Add a meta description to head
	 * 
	 * @param	string	$name
	 * 		The meta name attribute
	 * 
	 * @param	string	$content
	 * 		The meta content attribute
	 * 
	 * @param	string	$httpEquiv
	 * 		The meta http-equiv attribute
	 * 
	 * @param	string	$charset
	 * 		The meta charset attribute
	 * 
	 * @return	DOMElement
	 * 		Returns a meta element.
	 */
	protected function addMeta($name = "", $content = "", $httpEquiv = "", $charset = "")
	{
		$meta = HTML::create('meta');
		HTML::attr($meta, "name", $name);
		HTML::attr($meta, "http-equiv", $httpEquiv);
		HTML::attr($meta, "content", htmlspecialchars($content));
		HTML::attr($meta, "charset", $charset);
		
		$this->appendToHead($meta);
		
		return $meta;
	}
	
	/**
	 * Inserts a css line
	 * 
	 * @param	string	$href
	 * 		The href attribute of the link
	 * 
	 * @return	DOMElement
	 * 		Returns a css element
	 */
	protected function addStyle($href)
	{
		$css = $this->getLink("stylesheet", $href);
		
		$this->appendToHead($css);
		
		return $css;
	}
	
	/**
	 * Inserts a script line
	 * 
	 * @param	string	$src
	 * 		The URL source file of the script
	 * 
	 * @param	boolean	$bottom
	 * 		Indicator whether the script tag will be placed at the bottom of the page.
	 * 		The default value is FALSE.
	 * 
	 * @return	DOMElement
	 * 		Returns a script element
	 */
	protected function addScript($src, $bottom = FALSE)
	{
		$script = HTML::create("script");
		HTML::attr($script, "src", $src);
		
		if ($bottom)
			$this->addToBottomScripts($script);
		else
			$this->appendToHead($script);
		
		return $script;
	}
	
	/**
	 * Inserts a page icon
	 * 
	 * @param	string	$href
	 * 		The icon URL
	 * 
	 * @return	void
	 */
	protected function addIcon($href)
	{
		$icon = $this->getLink("icon", $href);
		$this->appendToHead($icon);
		
		$shortIcon = $this->getLink("shortcut icon", $href);
		$this->appendToHead($shortIcon);
	}
	
	/**
	 * Sets the page title.
	 * 
	 * @param	string	$title
	 * 		The new page title.
	 * 
	 * @return	void
	 */
	protected function setTitle($title)
	{
		// Check if title already exists
		$headTitle = HTML::evaluate("//title")->item(0);
		if (!is_null($headTitle))
		{
			$new_headTitle = HTML::create("title", $title);
			HTML::replace($headTitle, $new_headTitle);
		}
		else
		{
			$headTitle = HTML::create("title", $title);
			$this->appendToHead($headTitle);
		}
	}
	
	/**
	 * Creates and returns a link tag object
	 * 
	 * @param	string	$rel
	 * 		The rel attribute of the link.
	 * 
	 * @param	string	$href
	 * 		The href URL of the link
	 * 
	 * @return	DOMElement
	 * 		Returns a link tag object
	 */
	private function getLink($rel, $href)
	{
		$link = HTML::create("link");
		HTML::attr($link, "rel", $rel);
		HTML::attr($link, "href", $href);
		
		return $link;
	}
	
	/**
	 * Builds all the meta tags along with the document title tag
	 * 
	 * @param	string	$title
	 * 		The title of the document
	 * 
	 * @param	string	$description
	 * 		The description meta
	 * 
	 * @param	string	$keywords
	 * 		The keywords meta
	 * 
	 * @return	void
	 */
	private function buildMeta($title, $description, $keywords)
	{
		// Create title tag
		$this->setTitle($title);
		
		// Create meta tags
		$this->addMeta($name = "", $content = "", $httpEquiv = "", $charset = "UTF-8");
		if (!empty($description))
			$this->addMeta($name = "description", $description);
		if (!empty($keywords))
			$this->addMeta($name = "keywords", $keywords);
	}
	
	/**
	 * Insert the given script tag to stack, in order to be inserted at the bottom of the page right before delivering the page
	 * 
	 * @param	DOMElement	$script
	 * 		The script tag element
	 * 
	 * @return	void
	 */
	private function addToBottomScripts($script)
	{
		$this->bottomScripts[] = $script;
	}
	
	/**
	 * Appends all scripts to the body
	 * 
	 * @return	void
	 */
	private function flushBottomScripts()
	{
		foreach ($this->bottomScripts as $script)
			$this->appendToBody($script);
	}
}
//#section_end#
?>