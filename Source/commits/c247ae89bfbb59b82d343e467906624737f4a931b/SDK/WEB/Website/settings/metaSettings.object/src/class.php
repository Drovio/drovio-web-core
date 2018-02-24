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
 * Meta Website settings manager
 * 
 * Gets all website default meta information including open graph.
 * 
 * @version	0.1-2
 * @created	January 2, 2015, 19:26 (EET)
 * @updated	January 2, 2015, 19:36 (EET)
 */
class metaSettings extends settingsManager
{
	/**
	 * Initialize the settings file manager.
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		// Initialize settings manager
		$settingsFolder = paths::getSettingsFolder();
		parent::__construct($settingsFolder, "meta_settings");
	}
}
//#section_end#
?>