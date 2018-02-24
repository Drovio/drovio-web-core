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
 * 
 * @copyright	Copyright (C) 2015 Drovio. All rights reserved.
 */

importer::import("WUI", "Core", "WSPage");

use \WUI\Core\WSPage;

/**
 * Webpage Loader Class
 * 
 * Gives the directives to build a Website Page and prints the constructed page to the user.
 * 
 * @version	0.1-6
 * @created	December 31, 2014, 18:36 (GMT)
 * @updated	December 12, 2015, 18:25 (GMT)
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
		$path = str_replace(siteInnerPath, '', $_SERVER['SCRIPT_NAME']);
		$path_parts = pathinfo($path);
		
		// Normalize directory and page name
		$dirname = str_replace(".", "", $path_parts['dirname']);
		$pageName = (empty($path_parts['filename']) ? "index" : $path_parts['filename']).".php";
		
		// Build Page path
		$pagePath = $dirname."/".$pageName;
		
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