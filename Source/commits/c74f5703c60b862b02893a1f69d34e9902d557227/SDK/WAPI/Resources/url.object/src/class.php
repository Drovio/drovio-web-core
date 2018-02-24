<?php
//#section#[header]
// Namespace
namespace WAPI\Resources;


//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Resources
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

/**
 * URL Resolver
 * 
 * This class is responsible for url resolving.
 * It is used for resolving urls for resources and redirections.
 * 
 * @version	0.1-1
 * @created	October 1, 2014, 13:41 (EEST)
 * @revised	October 1, 2014, 13:41 (EEST)
 */
class url
{
	/**
	 * Get current domain.
	 * 
	 * @return	string
	 * 		The host domain.
	 */
	public static function getDomain()
	{
		$urlInfo = self::urlInfo();
		return $urlInfo['domain'];
	}
	
	/**
	 * Gets the current navigation subdomain.
	 * 
	 * @return	string
	 * 		Returns the active navigation subdomain.
	 */
	public static function getSubDomain()
	{
		$urlInfo = self::urlInfo();
		return $urlInfo['sub'];
	}
	
	/**
	 * Creates and returns a url with parameters in url encoding.
	 * 
	 * @param	string	$url
	 * 		The base url.
	 * 
	 * @param	array	$params
	 * 		An array of parameters as key => value.
	 * 
	 * @return	string
	 * 		A well formed url.
	 */
	public static function get($url, $params = array())
	{
		$result = $url;
		
		if (count($params) > 0)
			$result .= "?";
		
		// Normalize Parameters
		$arguments = array();
		foreach ($params as $key => $value)
			$arguments[] = $key."=". urlencode($value);
		
		// Merge Parameters
		$result .= implode("&", $arguments);
		
		// Normalize and return
		return $result;
	}
	
	/**
	 * Gets the info of the url in an array.
	 * 
	 * @return	array
	 * 		The url info as follows:
	 * 		['sub'] = The navigation subdomain.
	 * 		['domain'] = The host domain.
	 */
	private static function urlInfo()
	{
		// Get host and split into parts
		$origin = (empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_HOST'] : $_SERVER['HTTP_ORIGIN']);
		$origin = str_replace("http://", "", $origin);
		$origin = str_replace("https://", "", $origin);
		
		// Get website domain
		$wsDomain = "";
		
		// Remove domain from origin and get subdomain
		$origin = str_replace($wsDomain, "", $origin);
		$origin = trim($origin, ".");
		
		// Create url info and return
		$info = array();
		$info['sub'] = $origin;
		$info['domain'] = $wsDomain;
		return $info;
	}
}
//#section_end#
?>