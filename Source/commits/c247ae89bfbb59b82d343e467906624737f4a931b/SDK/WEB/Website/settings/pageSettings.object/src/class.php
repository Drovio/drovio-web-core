<?php
//#section#[header]
// Namespace
namespace WEB\Website\settings;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WEB
 * @package	Website
 * @namespace	\settings
 * 
 * @copyright	Copyright (C) 2015 Redback. All rights reserved.
 */

importer::import("WAPI", "Resources", "settingsManager");
importer::import("WAPI", "Resources", "paths");

use \WAPI\Resources\settingsManager;
use \WAPI\Resources\paths;

/**
 * Website Page settings manager
 * 
 * Gets all page settings for a given website page.
 * 
 * @version	0.1-1
 * @created	January 2, 2015, 19:57 (EET)
 * @updated	January 2, 2015, 19:57 (EET)
 */
class pageSettings extends settingsManager
{
	/**
	 * Initialize the settings file manager.
	 * 
	 * @param	string	$pagePath
	 * 		The page path as taken from the request uri.
	 * 		No extension should provided.
	 * 
	 * @return	void
	 */
	public function __construct($pagePath = "")
	{
		// Initialize settings manager
		$pageSettingsFolder = paths::getPageFolder($pagePath);
		parent::__construct($pageSettingsFolder, "settings");
	}
}
//#section_end#
?>