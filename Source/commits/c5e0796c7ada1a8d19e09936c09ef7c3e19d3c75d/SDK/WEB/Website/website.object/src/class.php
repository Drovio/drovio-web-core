<?php
//#section#[header]
// Namespace
namespace WEB\Website;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WEB
 * @package	Website
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WEB", "Platform", "website");

use \WEB\Platform\website as platformWebsite;

/**
 * Website Manager
 * 
 * This is the website manager class.
 * It imports website source objects and load website pages.
 * 
 * @version	1.0-1
 * @created	November 27, 2014, 21:57 (EET)
 * @revised	December 31, 2014, 20:11 (EET)
 * 
 * @deprecated	Use \WEB\Platform\website instead.
 */
class website extends platformWebsite {}
//#section_end#
?>