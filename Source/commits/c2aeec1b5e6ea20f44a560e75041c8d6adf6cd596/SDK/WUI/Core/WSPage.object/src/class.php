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
 * 
 * @copyright	Copyright (C) 2015 DrovIO. All rights reserved.
 */

importer::import("WAPI", "Environment", "cookies");
importer::import("WAPI", "Environment", "url");
importer::import("WAPI", "Geoloc", "locale");
importer::import("WAPI", "Prototype", "sourceMap");
importer::import("WAPI", "Resources", "paths");
importer::import("WUI", "Prototype", "HTMLPagePrototype");
importer::import("WUI", "Html", "HTML");
importer::import("WEB", "Platform", "website");
importer::import("WEB", "Website", "settings/globalSettings");
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
use \WEB\Website\settings\globalSettings;
use \WEB\Website\settings\pageSettings;
use \WEB\Website\settings\metaSettings;

/**
 * Website Core Page Builder
 * 
 * It's a singleton pattern implementation for Website Page Builder.
 * Builds the website page loading the page view from the given path.
 * 
 * @version	0.6-4
 * @created	October 1, 2014, 12:39 (EEST)
 * @updated	September 18, 2015, 23:44 (EEST)
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
		$title = (empty($title) ? "Drovio Website" : $title);
		
		// Include meta
		$metaDescription = $this->getPageSettingsValue($pageSettings, $metaSettings, "meta_description");
		$metaKeywords = $this->getPageSettingsValue($pageSettings, $metaSettings, "meta_keywords");
		parent::build($title, $metaDescription, $metaKeywords);
		
		// Set HTML Attributes
		$HTMLTag = $this->get();
		HTML::attr($HTMLTag, "lang", $localeInfo['languageCode_ISO1_A2']);
		
		// Set Head Content
		$robots = $pageSettings->get("robots");
		$this->setHead($robots);
		
		// Populate Body
		try
		{
			$this->populateBody($pagePath);
		}
		catch (Exception $ex)
		{
			$pagePath = rtrim($pagePath, ".php");
			$this->populateBody($pagePath);
		}
		
		// Add initial resources (styles and scripts)
		$this->addResources($pagePath);

		return $this;
	}
	
	/**
	 * Get a page settings value.
	 * Given a pageSettings manager and a website settings manager, get from page and then from website.
	 * 
	 * @param	settingsManager	$pageSettings
	 * 		The pageSettings manager.
	 * 
	 * @param	settingsManager	$wsSettings
	 * 		The websiteSettings manager.
	 * 
	 * @param	string	$name
	 * 		The settings name.
	 * 
	 * @return	string
	 * 		The settings value.
	 */
	private function getPageSettingsValue($pageSettings, $wsSettings, $name)
	{
		// Get page settings value
		$value = $pageSettings->get($name);
		
		// If value is empty, get from website
		if (empty($value))
			$value = $wsSettings->get($name);
		
		// Return value
		return $value;
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
		$this->addMeta($name = "generator", $content = "Drovio Web Engine", $httpEquiv = "", $charset = "");
		
		// Add open graph meta
		$this->addOpenGraphMeta();
		
		// Redback Icon
		$this->addIcon(url::resource(wrsrcRoot."/ws/favicon.ico"));
		
		// No Javascript
		$this->addNoScript();
	}
	
	/**
	 * Adds open graph meta elements to the page.
	 * 
	 * @return	void
	 */
	private function addOpenGraphMeta()
	{
		// Get page info/settings
		$pageSettings = new pageSettings();
		
		// Get website meta settings
		$metaSettings = new metaSettings();
		
		// Add open graph settings
		if ($metaSettings->get("meta_og_enabled"))
		{
			// Site name
			$content = $this->getPageSettingsValue($pageSettings, $metaSettings, "meta_og_sitename");
			if (!empty($content))
				$this->addMeta($name = "og:site_name", $content, $httpEquiv = "", $charset = "");
			
			// Site type
			$content = $this->getPageSettingsValue($pageSettings, $metaSettings, "meta_og_type");
			if (!empty($content))
				$this->addMeta($name = "og:type", $content, $httpEquiv = "", $charset = "");
			
			// Image
			$content = $this->getPageSettingsValue($pageSettings, $metaSettings, "meta_og_image");
			if (!empty($content))
				$this->addMeta($name = "og:image", $content, $httpEquiv = "", $charset = "");
			
			// Page title
			$content = $this->getPageSettingsValue($pageSettings, $metaSettings, "meta_og_title");
			if (!empty($content))
				$this->addMeta($name = "og:title", $content, $httpEquiv = "", $charset = "");
			
			// Website locale
			$this->addMeta($name = "og:locale", locale::get(), $httpEquiv = "", $charset = "");
		}
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
		$this->addScript("//cdn.drov.io/js/jquery/jquery-1.11.2.min.js");
		$this->addScript("//cdn.drov.io/js/jquery/plugins/jquery.easing-1.3.js");
		$this->addScript("//cdn.drov.io/js/jquery/plugins/jquery.ba-dotimeout-1.0.min.js");
		
		// Add reset css
		$this->addStyle("//cdn.drov.io/css/reset.css");
		
		// Add Web Core SDK Package Resources
		$sourceMap = new sourceMap(wsystemRoot.wsdkRoot);
		$libraries = $sourceMap->getLibraryList();
		foreach ($libraries as $library)
		{
			// Get packages
			$packages = $sourceMap->getPackageList($library);
			foreach ($packages as $package)
			{
				// Check tester status (for development only)
				if (importer::getTesterStatus($library, $package))
				{
					// Set script parameters
					$params = array();
					$params['library'] = $library;
					$params['package'] = $package;
					
					// Add css
					$url = url::resolve("www", "/ajax/web/wc/css.php", $params);
					$this->addStyle($url);
					
					// Add js
					$url = url::resolve("www", "/ajax/web/wc/js.php", $params);
					$this->addScript($url);
				}
				else
				{
					// Check if there is a css file and load
					$cssResource = "/css/".$this->getFileName("css", $library, $package, "css").".css";
					if (file_exists(wsystemRoot.wclibRoot.$cssResource))
						$this->addStyle(url::resource(wclibRoot.$cssResource));
					
					// Check if there is a javascript file and load
					$jsResource = "/js/".$this->getFileName("js", $library, $package, "js").".js";
					if (file_exists(wsystemRoot.wclibRoot.$jsResource))
						$this->addScript(url::resource(wclibRoot.$jsResource));
				}
			}
		}
		
		// Get website template
		$glbSettings = new globalSettings();
		$templateName = $glbSettings->get("template_name");
		$templateName = (empty($templateName) ? "MainTemplate" : $templateName);
		$themeName = $glbSettings->get("template_theme_name");
		$themeName = (empty($themeName) ? "MainTheme" : $themeName);
		// Add website template resources
		if (importer::onDevelopment())
		{
			// Set script parameters
			$params = array();
			$params['wsid'] = websiteID;
			$params['tname'] = $templateName;
			$params['thname'] = $themeName;
			
			// Add css
			$url = url::resolve("www", "/ajax/web/ws/t/css.php", $params);
			$this->addStyle($url);
			
			// Add js
			$url = url::resolve("www", "/ajax/web/ws/t/js.php", $params);
			$this->addScript($url);
		}
		else
		{
			// Check if there is a css file and load
			$cssResource = "/css/t/".$this->getFileName("css", $library, $package, "css").".css";
			if (file_exists(wsystemRoot.wslibRoot.$cssResource))
				$this->addStyle(url::resource(wslibRoot.$cssResource));
		
			// Check if there is a javascript file and load
			$jsResource = "/js/t/".$this->getFileName("js", $library, $package, "js").".js";
			if (file_exists(wsystemRoot.wslibRoot.$jsResource))
				$this->addScript(url::resource(wslibRoot.$jsResource));
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
				if (importer::onDevelopment())
				{
					// Set script parameters
					$params = array();
					$params['wsid'] = websiteID;
					$params['library'] = $library;
					$params['package'] = $package;
					
					// Add css
					$url = url::resolve("www", "/ajax/web/ws/s/css.php", $params);
					$this->addStyle($url);
					
					// Add js
					$url = url::resolve("www", "/ajax/web/ws/s/js.php", $params);
					$this->addScript($url);
				}
				else
				{
					// Check if there is a css file and load
					$cssResource = "/css/s/".$this->getFileName("css", $library, $package, "css").".css";
					if (file_exists(wsystemRoot.wslibRoot.$cssResource))
						$this->addStyle(url::resource(wslibRoot.$cssResource));
				
					// Check if there is a javascript file and load
					$jsResource = "/js/s/".$this->getFileName("js", $library, $package, "js").".js";
					if (file_exists(wsystemRoot.wslibRoot.$jsResource))
						$this->addScript(url::resource(wslibRoot.$jsResource));
				}
			}
		}
		
		// Add website page resources
		// Get resource id/filename
		$pagePath = trim($pagePath, "/");
		
		if (importer::onDevelopment())
		{
			// Set script parameters
			$params = array();
			$params['wsid'] = websiteID;
			$params['page'] = $pagePath;
			
			// Add css
			$url = url::resolve("www", "/ajax/web/ws/p/css.php", $params);
			$this->addStyle($url);
			
			// Add js
			$url = url::resolve("www", "/ajax/web/ws/p/js.php", $params);
			$this->addScript($url);
		}
		else
		{
			// Check if there is a css file and load
			$cssResource = "/css/p/".$this->getFileName("css", "Pages", $pagePath, "css").".css";
			if (file_exists(wsystemRoot.wslibRoot.$cssResource))
				$this->addStyle(url::resource(wslibRoot.$cssResource));
			
			// Check if there is a javascript file and load
			$jsResource = "/js/p/".$this->getFileName("js", "Pages", $pagePath, "js").".js";
			if (file_exists(wsystemRoot.wslibRoot.$jsResource))
				$this->addScript(url::resource(wslibRoot.$jsResource));
		}
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
	 * Get the resource file name as it is exported.
	 * 
	 * @param	string	$prefix
	 * 		The file prefix.
	 * 
	 * @param	string	$library
	 * 		The resource library.
	 * 
	 * @param	string	$package
	 * 		The resource package.
	 * 
	 * @param	string	$type
	 * 		The resource type.
	 * 
	 * @return	string
	 * 		The resource file name.
	 */
	private function getFileName($prefix, $library, $package, $type)
	{
		return $prefix.hash("md5", "rsrc_".$library."_".$package."_".$type);
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
		// Try to load page view
		// This can throw exception so we can catch and fix
		$viewContent = website::loadPage($pagePath);
		
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
		
		// Append page view content to main body
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