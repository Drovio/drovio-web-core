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

importer::import("WUI", "Website", "WSPage");

use \WUI\Website\WSPage;

/**
 * Webpage Loader Class
 * 
 * Gives the directives to build a Website Page and prints the constracted page to the user.
 * 
 * @version	0.1-1
 * @created	October 1, 2014, 18:13 (EEST)
 * @revised	October 1, 2014, 18:13 (EEST)
 */
class pageLoader
{
	/**
	 * Loads a page with the defined website page content
	 * 
	 * @param	string	$path
	 * 		The loacation (path) of the current page.
	 * 
	 * @return	void
	 */
	public static function load()
	{
		// Calculate the path for the
		// pageView to load		
		$path_parts = pathinfo($_SERVER['REQUEST_URI']);
		$filePath = $path_parts['dirname'];
		$objectName = $path_parts['filename'];
		
		// Normalize page name
		$objectName = (empty($objectName) ? "index" : $objectName);
		$objectName .= ".page";
		
		// Build Page path
		$pagePath = $filePath.$objectName;
		
		// Get Website Page attributes
		$pageAttributes = array();
		
		// Build HTML Page
		$startTime = microtime(TRUE);
		$htmlPage = WSPage::getInstance($pageAttributes);
		$htmlPage->build($pagePath);
		$endTime = microtime(TRUE);
		
		
		echo $htmlPage->getHTML();
	}
}
//#section_end#
?>