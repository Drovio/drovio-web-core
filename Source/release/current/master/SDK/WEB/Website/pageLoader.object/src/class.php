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

importer::import("WEB", "Platform", "pageLoader");

use \WEB\Platform\pageLoader as platformPageLoader;

/**
 * Webpage Loader Class
 * 
 * Gives the directives to build a Website Page and prints the constructed page to the user.
 * 
 * @version	2.0-1
 * @created	October 1, 2014, 18:13 (EEST)
 * @revised	December 31, 2014, 20:37 (EET)
 * 
 * @deprecated	Use \WEB\Platform\pageLoader instead.
 */
class pageLoader extends platformPageLoader {}
//#section_end#
?>