<?php
//#section#[header]
// Namespace
namespace WDEV\Profiler\log;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WDEV
 * @package	Profiler
 * @namespace	\log
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Profiler", "logger");

use \WAPI\Profiler\logger;

/**
 * Web SDK Logger
 * 
 * Used by Web SDK classes to log their activity, errors and debugging messages
 * 
 * @version	0.2-1
 * @created	December 27, 2014, 14:54 (EET)
 * @revised	December 27, 2014, 14:56 (EET)
 */
class wsdkLogger  extends logger
{
	/**
	 * Constructor Method
	 * 
	 * @return	void
	 */
	public function __construct()
	{
		// Put your constructor method code here.
	}
	
	/**
	 * Get the web sdk log folder.
	 * 
	 * @return	string
	 * 		The website's log folder.
	 */
	protected function getLogFolder()
	{		
		return systemRoot."/.wec/data/Logs/wsdk";
	}
}
//#section_end#
?>