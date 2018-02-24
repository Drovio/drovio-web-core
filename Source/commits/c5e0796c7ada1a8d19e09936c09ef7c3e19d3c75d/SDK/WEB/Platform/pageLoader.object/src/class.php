<?php
//#section#[header]
// Namespace
namespace WEB\Platform;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WEB
 * @package	Platform
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WUI", "Core", "WSPage");

use \WUI\Core\WSPage;

/**
 * Webpage Loader Class
 * 
 * Gives the directives to build a Website Page and prints the constructed page to the user.
 * 
 * @version	0.1-1
 * @created	December 31, 2014, 20:36 (EET)
 * @revised	December 31, 2014, 20:36 (EET)
 */
class pageLoader
{
	/**
	 * Loads a page with the defined website page content.
	 * 
	 * @return	void
	 */
	public static function load()
	{
		// Calculate the path for the page to load
		$path = preg_replace('/'.siteInnerPath.'\//', '', $_SERVER['REQUEST_URI'], 1);
		$path_parts = pathinfo($path);
		$filePath = $path_parts['dirname'];
		$objectName = $path_parts['filename'];
		
		// Normalize page name
		$objectName = (empty($objectName) ? "index" : $objectName);
		
		// Build Page path
		$pagePath = $filePath."/".$objectName;
		
		// Get Website Page attributes
		$pageAttributes = array();
		
		// Build HTML Page
		$htmlPage = WSPage::getInstance($pageAttributes);
		$htmlPage->build($pagePath);
		
		echo $htmlPage->getHTML();
	}
}
//#section_end#
?>