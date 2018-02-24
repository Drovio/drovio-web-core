<?php
//#section#[header]
// Namespace
namespace WAPI\Environment;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Environment
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Resources", "filesystem/directory");

use \WAPI\Resources\filesystem\directory;

/**
 * URL Helper and Resolver
 * 
 * This class is a helper class for handling urls.
 * It is used for resolving urls for resources and redirections.
 * 
 * @version	0.1-3
 * @created	November 27, 2014, 20:33 (EET)
 * @revised	December 30, 2014, 14:41 (EET)
 */
class url
{
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
	 * Get current domain.
	 * 
	 * @return	string
	 * 		The host domain.
	 */
	public static function getDomain()
	{
		$urlInfo = self::info();
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
		$urlInfo = self::info();
		if (isset($urlInfo['referer']))
			$urlInfo = self::info($urlInfo['referer']);
		return $urlInfo['sub'];
	}
	
	/**
	 * Resolves a given URL given a subdomain and a page url in the framework.
	 * 
	 * @param	string	$sub
	 * 		The subdomain name.
	 * 		It must be a valid name.
	 * 
	 * @param	string	$url
	 * 		The page url.
	 * 		By default it's the root url ("/").
	 * 
	 * @param	string	$protocol
	 * 		The protocol to resolve the url to.
	 * 		Leave empty to decide based on the server request protocol.
	 * 
	 * @return	string
	 * 		The resolved url.
	 */
	public static function resolve($sub, $url = "/", $protocol = NULL)
	{
		// Set protocol
		if (empty($protocol))
		{
			$info = self::info();
			$protocol = $info['protocol'];
		}
		
		// Resolve URL according to system configuration
		$domain = self::getDomain()."/".siteInnerPath;
		$resolved_URL = directory::normalize($sub.".".$domain."/".$url);
		return $protocol."://".$resolved_URL;
	}
	
	/**
	 * Resolves a resource's URL for reference to the resource's domain.
	 * The resource domain is usually the 'www'. So it is equivalent to resolve('www',...);
	 * 
	 * @param	string	$url
	 * 		The resource's URL to be resolved.
	 * 
	 * @param	string	$protocol
	 * 		The protocol to resolve the url to.
	 * 		The default protocol is "http".
	 * 
	 * @return	string
	 * 		The resolved resource url.
	 */
	public static function resource($url, $protocol = "http")
	{
		// Check server protocol
		$info = self::info();
		$serverProtocol = $info['protocol'];
		if ($protocol == "https" && $serverProtocol == "http")
			$protocol = "http";
		
		// Resolve url
		return self::resolve("www", $url, $protocol);
	}
	
	/**
	 * Resolves the given url and returns a redirect url after check that the url is valid.
	 * 
	 * @param	string	$url
	 * 		The url to redirect.
	 * 
	 * @return	string
	 * 		The redirected url.
	 */
	public static function redirect($url)
	{
		// Check valid url
		
		// Set header location to redirect
		header('Location: '.$url);
		
		// Return the url
		return $url;
	}
	
	/**
	 * Gets the info of the url in an array.
	 * 
	 * @param	string	$url
	 * 		The url to get the information from.
	 * 		If the url is empty, get the current request url.
	 * 
	 * @param	string	$domain
	 * 		The url domain.
	 * 		This is given to distinguish the subdomains on the front.
	 * 		The default value is empty to get the website domain.
	 * 
	 * @return	array
	 * 		The url info as follows:
	 * 		['url'] = The full url page.
	 * 		['protocol'] = The server protocol.
	 * 		['sub'] = The navigation subdomain.
	 * 		['domain'] = The host domain.
	 * 		['params'] = An array of all url parameters by name and value.
	 * 		['referer'] = The referer value, if exists.
	 */
	public static function info($url = "", $domain = "")
	{
		$info = array();
		
		// Get protocol from given url
		$protocol = (strpos($url, "https") === 0 ? "https" : "http");
		
		// If no given url, get current
		if (empty($url))
		{
			// Get protocol
			$protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
			$url = $protocol."://".$_SERVER['HTTP_HOST']."/".$_SERVER['REQUEST_URI'];
			$info['referer'] = $_SERVER['HTTP_REFERER'];
		}
		
		// Normalize url
		$url = str_replace($protocol."://", "", $url);
		$url = directory::normalize($url);
		$info['url'] = $protocol."://".$url;
		
		// Split for domain and subdomain
		list ($path, $params) = explode("?", $url);
		
		// Get domain and subdomain
		$hostParts = explode("/", $path);
		$host = $hostParts[0];
		$parts = explode(".", $host);

		// Get website's domain if empty
		if (empty($domain))
			$domain = $parts[0];
		
		// Some part of this must be the website domain
		// If the first part is website, then subdomain is www,
		// otherwise the first part is the subdomain.
		if ($parts[0] == $domain)
			$sub = "www";
		else
		{
			$sub = $parts[0];
			unset($parts[0]);
		}
		
		// Set info
		$info['protocol'] = $protocol;
		$info['sub'] = $sub;
		$info['domain'] = implode(".", $parts);
		
		// Get parameters
		$urlParams = explode("&", $params);
		foreach ($urlParams as $up)
		{
			$pparts = explode("=", $up);
			if (!empty($pparts) && !empty($pparts[0]))
				$info['params'][$pparts[0]] = $pparts[1];
		}
		
		return $info;
	}
}
//#section_end#
?>