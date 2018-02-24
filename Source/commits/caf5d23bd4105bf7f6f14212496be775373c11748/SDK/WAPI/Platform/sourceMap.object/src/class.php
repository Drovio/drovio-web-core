<?php
//#section#[header]
// Namespace
namespace WAPI\Platform;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Platform
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */


importer::import("WAPI", "Resources", "DOMParser");

use \WAPI\Resources\DOMParser;

/**
 * {title}
 * 
 * {description}
 * 
 * @version	0.1-1
 * @created	December 18, 2014, 21:38 (EET)
 * @revised	December 18, 2014, 21:38 (EET)
 */
class sourceMap
{
	/**
	 * The default map file name.
	 * 
	 * @type	string
	 */
	const MAP_FILE = "map.xml";
	
	/**
	 * The folder path of the file.
	 * 
	 * @type	string
	 */
	private $folderPath;
	
	/**
	 * The map file name.
	 * 
	 * @type	string
	 */
	private $mapFile;
	
	/**
	 * Constructor method for object initialization.
	 * 
	 * @param	string	$folderPath
	 * 		The folder path for the map file.
	 * 
	 * @param	string	$mapFile
	 * 		The map file name. By default the MAP_FILE constant is used.
	 * 
	 * @return	void
	 */
	public function __construct($folderPath, $mapFile = self::MAP_FILE)
	{
		$this->folderPath = $folderPath;
		$this->mapFile = $mapFile;
	}
	
	/**
	 * Gets all libraries in the map file.
	 * 
	 * @return	array
	 * 		An array of all libraries by key and value.
	 */
	public function getLibraryList()
	{
		// Get map file path
		$filePath = $this->folderPath."/".$this->mapFile;
		
		// Load Library File
		$parser = new DOMParser();
		$parser->load($filePath, FALSE);
		
		$libArray = array();		
		$libraries = $parser->evaluate("//library");
		foreach ($libraries as $lib)
			$libArray[$parser->attr($lib, "name")] = $parser->attr($lib, "name");

		return $libArray;
	}
	
	/**
	 * Get all packages in the given library from the map file.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @return	array
	 * 		An array of all packages in the library.
	 */
	public function getPackageList($library)
	{
		// Get map file path
		$filePath = $this->folderPath."/".$this->mapFile;
		
		// Load Library File
		$parser = new DOMParser();
		$parser->load($filePath, FALSE);
		
		$packages = $parser->evaluate("//library[@name='".$library."']/package");
		$pkgArray = array();
		foreach ($packages as $pkg)
			$pkgArray[$parser->attr($pkg, "name")] = $parser->attr($pkg, "name");

		return $pkgArray;
	}
	
	/**
	 * Get all namespaces in the given package.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @param	string	$parentNs
	 * 		The parent namespace (separated by "::", if any).
	 * 
	 * @return	array
	 * 		A nested array of all namespaces.
	 */
	public function getNSList($library, $package, $parentNs = "")
	{
		// Get map file path
		$filePath = $this->folderPath."/".$this->mapFile;
		
		// Load Library File
		$parser = new DOMParser();
		$parser->load($filePath, FALSE);
		
		// Get Package
		$packageEntry = $parser->evaluate("//library[@name='".$library."']/package[@name='".$package."']")->item(0);
		if (empty($parentNs))
			$parent = $packageEntry;
		else
		{
			// If namespace given, search for namespace
			$nss = explode("::", $parentNs);
			$q_nss = "namespace[@name='".implode("']/namespace[@name='", $nss)."']";
			$parent = $parser->evaluate($q_nss, $packageEntry)->item(0);
			if (is_null($parent))
				throw new Exception("Namespace '$parentNs' doesn't exist inside package '$package'.");
		}
		
		// Get Children namespaces
		$namespaces = $parser->evaluate("namespace", $parent);
		
		// Create array
		$nsArray = array();
		foreach ($namespaces as $ns)
		{
			$nsName = $parser->attr($ns, "name");
			if (empty($nsName))
				continue;
			$tempParent = ($parentNs == "" ? "" : $parentNs."::").$nsName;
			$nsArray[$nsName] = $this->getNSList($library, $package, $tempParent);
		}
		
		return $nsArray;
	}
	
	/**
	 * Get all objects in the map, in the given library, package and namespace.
	 * 
	 * @param	string	$library
	 * 		The library name.
	 * 
	 * @param	string	$package
	 * 		The package name.
	 * 
	 * @param	string	$parentNs
	 * 		The namespace (separated by "::", if any).
	 * 		The default value is null, which will select all objects in the package at any depth. If is set to an empty string (""), it will select all objects as children of the package at depth 1.
	 * 
	 * @return	array
	 * 		An array of all items. An item is an array of object information, including title, name, library, package and namespace.
	 */
	public function getObjectList($library, $package = "", $parentNs = NULL)
	{
		// Get map file path
		$filePath = $this->folderPath."/".$this->mapFile;
		
		// Load Library index file
		$parser = new DOMParser();
		$parser->load($filePath, FALSE);
		
		// Get Objects
		$libraryObjects = array();
		if (empty($package) && empty($parentNs))
			$objects = $parser->evaluate("//library[@name='".$library."']/object | //library[@name='".$library."']//object");
		else if (is_null($parentNs))
			$objects = $parser->evaluate("//library[@name='".$library."']/package[@name='".$package."']//object");
		else if (empty($parentNs))
			$objects = $parser->evaluate("//library[@name='".$library."']/package[@name='".$package."']/object");
		else
		{
			$nss = explode("::", $parentNs);
			$q_nss = "//library[@name='".$library."']/package[@name='".$package."']/namespace[@name='".implode("']/namespace[@name='", $nss)."']";
			$parent = $parser->evaluate($q_nss)->item(0);
			if (is_null($parent))
				throw new Exception("Namespace '$parentNs' doesn't exist inside package '$package'.");
			$objects = $parser->evaluate("object", $parent);
		}
		
		// Get objects
		foreach ($objects as $obj)
		{
			// Build Object
			$info = array();
			$info['title'] = $parser->attr($obj, "title");
			$info['name'] = $parser->attr($obj, "name");
			
			// Set library
			$info['library'] = $library;
			
			// Get Namespace
			$nsArray = array();
			$parentNode = $obj->parentNode;
			while ($parentNode->tagName == "namespace")
			{
				$nsArray[] = $parser->attr($parentNode, "name");
				$parentNode = $parentNode->parentNode;
			}
			
			// Set namespace
			$nsArray = array_reverse($nsArray);
			$namespace = implode("::", $nsArray);
			$info['namespace'] = $namespace;
			
			// Get package (if any)
			if ($parentNode->tagName == "package")
				$info['package'] = $parser->attr($parentNode, "name");
			
			// Add info
			$libraryObjects[] = $info;
		}

		return $libraryObjects;
	}
}
//#section_end#
?>