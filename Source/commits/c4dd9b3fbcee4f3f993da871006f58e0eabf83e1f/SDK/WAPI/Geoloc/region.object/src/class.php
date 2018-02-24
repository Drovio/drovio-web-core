<?php
//#section#[header]
// Namespace
namespace WAPI\Geoloc;


//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Geoloc
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Resources", "storage::session");
importer::import("WAPI", "Resources", "storage::cookies");

use \WAPI\Resources\storage\session;
use \WAPI\Resources\storage\cookies;
 
/**
 * Region Manager
 * 
 * Gets all the information about the user's region.
 * 
 * @version	0.1-1
 * @created	October 3, 2014, 20:19 (EEST)
 * @revised	October 3, 2014, 20:19 (EEST)
 */
class region
{
	/**
	 * The default system region.
	 * 
	 * @type	string
	 */
	private static $defaultRegion = "";
	
	/**
	 * Get the user's current region
	 * 
	 * @return	string
	 * 		It is the countryCode_ISO2A field, so the ISO2A country code.
	 */
	public static function get()
	{
		// Get cookie
		$region = cookies::get("region");
		
		// If not set as cookie, check session
		if (!isset($region) || $region == NULL)
		{
			// If not set as session, set default
			$region = session::get("region", NULL, $namespace = "geoloc");
			if (!isset($region))
				$region = self::getDefault();
				
			// Set Cookie for a year
			cookies::set("region", $region, (365 * 24 * 60 * 60));
		}
		session::set("region", $region, "geoloc");
		return $region;
	}
	
	/**
	 * Gets the default region by locale.
	 * 
	 * @return	string
	 * 		It is the countryCode_ISO2A field, so the ISO2A country code.
	 */
	public static function getDefault()
	{
		return NULL;
	}
}
//#section_end#
?>