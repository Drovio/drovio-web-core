<?php
//#section#[header]
// Namespace
namespace WUI\Core;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Core
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Environment", "cookies");
importer::import("WAPI", "Geoloc", "locale");
importer::import("WUI", "Prototype", "HTMLPagePrototype");
importer::import("WUI", "Html", "HTML");
importer::import("WAPI", "Environment", "url");
importer::import("WEB", "Website", "website");

use \WEB\Website\website;
use \WAPI\Environment\url;
use \WAPI\Environment\cookies;
use \WAPI\Geoloc\locale;
use \WUI\Prototype\HTMLPagePrototype;
use \WUI\Html\HTML;

/**
 * Website Core Page Builder
 * 
 * It's a singleton pattern implementation for Website Page Builder.
 * Builds the website page loading the page view from the given path.
 * 
 * @version	0.2-3
 * @created	October 1, 2014, 12:39 (EEST)
 * @revised	December 8, 2014, 15:30 (EET)
 */
class WSPage extends HTMLPagePrototype
{
	/**
	 * The main page holder to load the page view.
	 * 
	 * @type	string
	 */
	const HOLDER = "#wsPageContainer";
	
	/**
	 * The page report holder, in case of errors.
	 * 
	 * @type	string
	 */
	const REPORT = "#wsPageReport";
	
	/**
	 * A general helper holder.
	 * 
	 * @type	string
	 */
	const HELPER = "#wsPageHelper";
	
	/**
	 * The singleton's instance.
	 * 
	 * @type	WSPage
	 */
	private static $instance;
	
	/**
	 * Page attributes for building the ui object. Includes title, meta and more.
	 * 
	 * @type	array
	 */
	private $pageAttributes;
	
	/**
	 * Initializes the Website Page with the page attributes.
	 * 
	 * @param	array	$pageAttributes
	 * 		An array of page attributes, including title and meta information.
	 * 
	 * @return	void
	 */
	protected function __construct($pageAttributes = array())
	{
		// Put your constructor method code here.
		parent::__construct();
		
		// Set page attributes
		$this->pageAttributes = $pageAttributes;
	}
	
	/**
	 * Gets the instance of the WSPage.
	 * 
	 * @param	array	$pageAttributes
	 * 		The page attributes.
	 * 		It includes the page title, whether it will have meta information and more.
	 * 
	 * @return	WSPage
	 * 		The WSPage unique instance.
	 */
	public static function getInstance($pageAttributes = array())
	{
		if (!isset(self::$instance))
			self::$instance = new WSPage($pageAttributes);
		
		return self::$instance;
	}
	
	/**
	 * Builds the spine of the page.
	 * 
	 * @param	string	$pagePath
	 * 		The page path to load the page view.
	 * 
	 * @return	WSPage
	 * 		The WSPage object.
	 */
	public function build($pagePath = "")
	{
		// Build the PageTemplate 
		$localeInfo = locale::info();
		
		// Build PagePrototype with settings Meta Content
		$title = $this->pageAttributes['title'];
		
		// Include meta
		if ($this->pageAttributes['meta'])
		{
			$metaDescription = "metaDescription";
			$metaKeywords = "metaDescription";
		}
		parent::build($title, $metaDescription, $metaKeywords);
		
		// Set HTML Attributes
		$HTMLTag = $this->get();
		HTML::attr($HTMLTag, "id", "#website_name");
		HTML::attr($HTMLTag, "lang", $localeInfo['languageCode_ISO1_A2']);
		
		// Set Head Content
		$this->setHead();
		
		// Add initial resources (styles and scripts)
		$this->addResources();

		// Populate Body
		$this->populateBody($pagePath);

		return $this;
	}
	
	/**
	 * Adds the initial static resources to the page header (including initial packages for styles and javascript).
	 * 
	 * @return	void
	 */
	private function addResources()
	{
		// Add primary dependencies (jQuery and others)
		$this->addScript(url::resource("/q/jq.jquery.js"));
		$this->addScript(url::resource("/q/jq.jquery.ba-dotimeout.min.js"));
		
		// Add WSDK Resources
		
		// TODO
		// Include all WSDK resources, js and css
	}
	
	/**
	 * Builds the body container.
	 * 
	 * @param	string	$pagePath
	 * 		The page path to load the page view.
	 * 
	 * @return	void
	 */
	private function populateBody($pagePath = "")
	{
		// Populate Body Base
		$mainContainer = HTML::create("div", "", "wsPageContainer");
		$this->appendToBody($mainContainer);
		
		// Report Container
		$pageReport = HTML::create("div", "", "wsPageReport");
		HTML::append($mainContainer, $pageReport);
		
		// Helper Container
		$pageHelper = HTML::create("div", "", "wsPageHelper");
		HTML::append($mainContainer, $pageHelper);
		
		// Create page container
		$pageViewContainer = HTML::create("div", "", "viewContainer", "uiPageContainer");
		HTML::append($mainContainer, $pageViewContainer);
		
		// Load page view
		$viewContent = website::loadPage($pagePath);
		if (is_string($viewContent))
			HTML::innerHTML($pageViewContainer, $viewContent);
		else
			HTML::append($pageViewContainer, $viewContent);
		
		// If there is cookie or _GET variable for noscript, insert Notification div
		if (cookies::get("noscript") || isset($_GET['wsnoscript']))
		{
			/*
			// Create Notification
			$notification = new notification();
			$ntf = $notification->build($type = "warning", $header = TRUE, $timeout = FALSE, $disposable = FALSE)->get();
			DOM::append($pageReport, $ntf);
			
			// Load Warning Message
			$javascript_warning = $notification->getMessage("warning", "wrn.javascript");
			$notification->appendCustomMessage($javascript_warning);
			*/
		}
	}
	
	/**
	 * Inserts all the meta tags, scripts and styles to head.
	 * 
	 * @return	void
	 */
	private function setHead()
	{
		// Create extra meta content (Identity)
		//$this->addMeta($name = "author", literal::get("global.meta", "author", array(), FALSE), $httpEquiv = "", $charset = "");
		
		// Robots Indexing
		$this->addMeta($name = "robots", $content = "FOLLOW, NOODP, NOYDIR", $httpEquiv = "", $charset = "");
		
		// Generator
		$this->addMeta($name = "generator", $content = "Redback Web Engine", $httpEquiv = "", $charset = "");
		
		// Redback Icon
		$this->addIcon(url::resource("favicon.ico"));
		
		// No Javascript
		$this->addNoScript();
	}
	
	/**
	 * Inserts the noscript tag.
	 * 
	 * @return	void
	 */
	private function addNoScript()
	{
		if (!cookies::get("noscript") && !isset($_GET['wsnoscript']))
		{
			// noscript tag
			$noScriptTag = HTML::create("noscript");
			$this->appendToHead($noScriptTag);
			
			// Meta Refresh Tag
			$sep = (strpos($_SERVER['REQUEST_URI'], "?") && strpos($_SERVER['REQUEST_URI'], "?") >= 0);		
			$content = "0; URL=".$_SERVER['REQUEST_URI'].($sep ? "&" : "?")."wsnoscript=1";
			$noscriptMeta = $this->addMeta($name = "", $content, $httpEquiv = "refresh", $charset = "");
			HTML::append($noScriptTag, $noscriptMeta);
		}
		else if (isset($_GET['wsnoscript']))
			cookies::set("noscript", 1);
	}
}
//#section_end#
?>