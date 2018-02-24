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
 * @copyright	Copyright (C) 2015 Redback. All rights reserved.
 */

importer::import("WAPI", "Environment", "cookies");
importer::import("WAPI", "Environment", "url");
importer::import("WAPI", "Geoloc", "locale");
importer::import("WAPI", "Prototype", "sourceMap");
importer::import("WAPI", "Resources", "paths");
importer::import("WUI", "Prototype", "HTMLPagePrototype");
importer::import("WUI", "Html", "HTML");
importer::import("WEB", "Platform", "website");
importer::import("WEB", "Website", "settings/pageSettings");
importer::import("WEB", "Website", "settings/metaSettings");

use \WAPI\Environment\url;
use \WAPI\Environment\cookies;
use \WAPI\Geoloc\locale;
use \WAPI\Prototype\sourceMap;
use \WAPI\Resources\paths;
use \WUI\Prototype\HTMLPagePrototype;
use \WUI\Html\HTML;
use \WEB\Platform\website;
use \WEB\Website\settings\pageSettings;
use \WEB\Website\settings\metaSettings;

/**
 * Website Core Page Builder
 * 
 * It's a singleton pattern implementation for Website Page Builder.
 * Builds the website page loading the page view from the given path.
 * 
 * @version	0.3-4
 * @created	October 1, 2014, 12:39 (EEST)
 * @updated	January 2, 2015, 20:16 (EET)
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
		
		// Get page info/settings
		$pageSettings = new pageSettings();
		
		// Get website meta settings
		$metaSettings = new metaSettings();
		
		// Build PagePrototype with settings Meta Content
		$title = $pageSettings->get('title');
		
		// Include meta
		if ($this->pageAttributes['meta'])
		{
			$metaDescription = $pageSettings->get("meta_description");
			if (empty($metaDescription))
				$metaDescription = $metaSettings->get("meta_description");
			$metaKeywords = $pageSettings->get("meta_keywords");
			if (empty($metaKeywords))
				$metaKeywords = $metaSettings->get("meta_keywords");
		}
		parent::build($title, $metaDescription, $metaKeywords);
		
		// Set HTML Attributes
		$HTMLTag = $this->get();
		HTML::attr($HTMLTag, "lang", $localeInfo['languageCode_ISO1_A2']);
		
		// Set Head Content
		$robots = $pageSettings->get("robots");
		$this->setHead($robots);
		
		// Add initial resources (styles and scripts)
		$this->addResources($pagePath);

		// Populate Body
		$this->populateBody($pagePath);

		return $this;
	}
	
	/**
	 * Adds the initial static resources to the page header (including initial packages for styles and javascript).
	 * 
	 * @param	string	$pagePath
	 * 		The page being loaded path.
	 * 
	 * @return	void
	 */
	private function addResources($pagePath)
	{
		// Add primary dependencies (jQuery and others)
		$this->addScript(url::resource(wlibRoot."/js/q/jq.jquery.js"));
		$this->addScript(url::resource(wlibRoot."/js/q/jq.jquery.easing.js"));
		$this->addScript(url::resource(wlibRoot."/js/q/jq.jquery.ba-dotimeout.min.js"));
		
		// Add Web Core SDK Package Resources
		$sourceMap = new sourceMap(wsystemRoot.wsdkRoot);
		$libraries = $sourceMap->getLibraryList();
		foreach ($libraries as $library)
		{
			// Get packages
			$packages = $sourceMap->getPackageList($library);
			foreach ($packages as $package)
			{
				// Get resource id/filename
				$resourceID = $this->getResourceID($library, $package);
				
				// Check if there is a css file and load
				$cssResource = "/css/c/".$resourceID.".css";
				if (file_exists(wsystemRoot.wlibRoot.$cssResource))
					$this->addStyle(url::resource(wlibRoot.$cssResource));
				
				// Check if there is a javascript file and load
				$jsResource = "/js/c/".$resourceID.".js";
				if (file_exists(wsystemRoot.wlibRoot.$jsResource))
					$this->addScript(url::resource(wlibRoot.$jsResource));
			}
		}
		
		// Add website source resources
		$sourceMap = new sourceMap(wsystemRoot.paths::getMapFolder(), "source.xml");
		$libraries = $sourceMap->getLibraryList();
		foreach ($libraries as $library)
		{
			// Get packages
			$packages = $sourceMap->getPackageList($library);
			foreach ($packages as $package)
			{
				// Get resource id/filename
				$resourceID = $this->getResourceID($library, $package);
			
				// Check if there is a css file and load
				$cssResource = "/css/s/".$resourceID.".css";
				if (file_exists(wsystemRoot.wlibRoot.$cssResource))
					$this->addStyle(url::resource(wlibRoot.$cssResource));
			
				// Check if there is a javascript file and load
				$jsResource = "/js/s/".$resourceID.".js";
				if (file_exists(wsystemRoot.wlibRoot.$jsResource))
					$this->addScript(url::resource(wlibRoot.$jsResource));
			}
		}
		
		// Add website page resources
		// Get resource id/filename
		$pagePath = trim($pagePath, "/");
		$resourceID = $this->getResourceID("Pages", $pagePath);
		
		// Check if there is a css file and load
		$cssResource = "/css/p/".$resourceID.".css";
		if (file_exists(wsystemRoot.wlibRoot.$cssResource))
			$this->addStyle(url::resource(wlibRoot.$cssResource));
		
		// Check if there is a javascript file and load
		$jsResource = "/js/p/".$resourceID.".js";
		if (file_exists(wsystemRoot.wlibRoot.$jsResource))
			$this->addScript(url::resource(wlibRoot.$jsResource));
	}
	
	/**
	 * Get a resource id given a library and a package.
	 * 
	 * @param	string	$library
	 * 		The resource library.
	 * 
	 * @param	string	$package
	 * 		The resource package.
	 * 
	 * @return	string
	 * 		The resource id string.
	 */
	private function getResourceID($library, $package)
	{
		return hash("md5", "rsrcID_".$library."_".$package);
	}
	
	/**
	 * Builds the body container.
	 * 
	 * @param	string	$pagePath
	 * 		The page path to load the page view.
	 * 
	 * @return	void
	 */
	private function populateBody($pagePath)
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
	 * @param	string	$robots
	 * 		The robots meta context.
	 * 
	 * @return	void
	 */
	private function setHead($robots = "")
	{
		// Robots Indexing
		if (!empty($robots))
			$this->addMeta($name = "robots", $robots, $httpEquiv = "", $charset = "");
		
		// Generator
		$this->addMeta($name = "generator", $content = "Redback Web Engine", $httpEquiv = "", $charset = "");
		
		// Redback Icon
		$this->addIcon(url::resource(wrsrcRoot."favicon.ico"));
		
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