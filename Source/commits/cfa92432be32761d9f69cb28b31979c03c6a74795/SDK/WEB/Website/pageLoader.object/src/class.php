<?php
//#section#[header]
// Namespace
namespace WEB\Website;


//#section_end#
//#section#[class]
/**
 * @library	WEB
 * @package	Website
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
 * @version	1.0-1
 * @created	October 1, 2014, 18:13 (EEST)
 * @revised	November 27, 2014, 21:31 (EET)
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
		$path_parts = pathinfo($_SERVER['REQUEST_URI']);
		$filePath = $path_parts['dirname'];
		$objectName = $path_parts['filename'];
		
		// Normalize page name
		$objectName = (empty($objectName) ? "index" : $objectName);
		
		// Build Page path
		$pagePath = $filePath."/".$objectName;
		$pagePath = str_replace(siteInnerPath."/", "", $pagePath);
		
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