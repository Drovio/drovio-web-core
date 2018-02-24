<?php
//#section#[header]
// Namespace
namespace WUI\Website;


//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Website
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Geoloc", "locale");
importer::import("WAPI", "Resources", "storage::cookies");
importer::import("WUI", "Prototype", "HTMLPagePrototype");
importer::import("WUI", "Html", "HTML");

use \WAPI\Geoloc\locale;
use \WAPI\Resources\storage\cookies;
use \WUI\Prototype\HTMLPagePrototype;
use \WUI\Html\HTML;

/**
 * Website Core Page Builder
 * 
 * It's a singleton pattern implementation for Website Page Builder.
 * Builds the website page loading the page view from the given path.
 * 
 * @version	0.1-1
 * @created	October 1, 2014, 0:24 (EEST)
 * @revised	October 1, 2014, 0:24 (EEST)
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
	 * Builds the spine of the page
	 * 
	 * @return	WSPage
	 * 		The WSPage object.
	 */
	public function build()
	{
		// Build the PageTemplate 
		$localeInfo = locale::info();
		
		// Build PagePrototype with settings Meta Content
		$title = $this->pageAttributes['title'];
		$title = (empty($title) ? "Redback Web" : $title);
		
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
		$this->populateBody();

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
		$resource = $this->addScript(Url::resource("/Library/Resources/js/q/jq.jquery.js"));
		$resource = $this->addScript(Url::resource("/Library/Resources/js/q/jq.jquery.ba-dotimeout.min.js"));
		
		// Add WSDK Resources
		
		// TODO
		// Include all WSDK resources, js and css
		
	}
	
	/**
	 * Builds the body container.
	 * 
	 * @param	string	$title
	 * 		The page title.
	 * 
	 * @return	void
	 */
	private function populateBody($title)
	{
		// Populate Body Base
		$mainContainer = HTML::create("div", "", "", "wsPageContainer");
		$this->appendToBody($mainContainer);
		
		// Report Container
		$pageReport = HTML::create("div", "", "pageReport");
		HTML::append($mainContainer, $pageReport);
		
		// Helper Container
		$pageHelper = HTML::create("div", "", "pageHelper");
		HTML::append($mainContainer, $pageHelper);
		
		// Create page container
		$modulePageContainer = HTML::create("div", "", "pageContainer", "uiPageContainer");
		HTML::append($mainContainer, $modulePageContainer);
		
		// Set page title
		$this->setTitle($title);
		
		// Load page view
		
		
		// If there is cookie or _GET variable for noscript, insert Notification div
		if (cookies::get("noscript") || isset($_GET['_rb_noscript']))
		{
			/*
			// Create Notification
			$notification = new notification();
			$ntf = $notification->build($type = "warning", $header = TRUE, $footer = TRUE)->get();
			HTML::append($pageReport, $ntf);
			
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
		$this->addIcon(Url::resource("/Library/Media/c/favicon.ico"));
		
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