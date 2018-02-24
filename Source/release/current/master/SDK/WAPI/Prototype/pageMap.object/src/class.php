<?php
//#section#[header]
// Namespace
namespace WAPI\Prototype;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Prototype
 * 
 * @copyright	Copyright (C) 2015 DrovIO. All rights reserved.
 */

importer::import("WAPI", "Resources", "DOMParser");
importer::import("WAPI", "Resources", "filesystem/fileManager");

use \WAPI\Resources\DOMParser;
use \WAPI\Resources\filesystem\fileManager;

/**
 * Page map index manager.
 * 
 * Loads a page map index file and reads all the folders and pages.
 * 
 * @version	0.1-3
 * @created	January 2, 2015, 17:58 (EET)
 * @updated	September 20, 2015, 11:52 (EEST)
 */
class pageMap
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
	 * 		The map file name.
	 * 		By default the MAP_FILE constant is used.
	 * 
	 * @return	void
	 */
	public function __construct($folderPath, $mapFile = self::MAP_FILE)
	{
		$this->folderPath = $folderPath;
		$this->mapFile = $mapFile;
	}
	
	/**
	 * Get an array of all the folders under the given path.
	 * 
	 * @param	string	$parent
	 * 		The folder to look down from.
	 * 		The default value is empty, for the root.
	 * 		Separate each folder with "/".
	 * 
	 * @param	boolean	$compact
	 * 		Whether to return a single compact array with folders separated by "/" or a nested array.
	 * 
	 * @return	array
	 * 		A nested array of all the folders under the given path.
	 */
	public function getFolders($parent = "", $compact = FALSE)
	{
		// Get index path
		$filePath = $this->folderPath."/".$this->mapFile;
		
		// Load file
		$parser = new DOMParser();
		$parser->load($filePath, FALSE);
		
		// Initialize parent
		$parent = trim($parent);
		$parent = trim($parent, "/");
		
		$expression = "/pages";
		if (!empty($parent))
			$expression .= "/folder[@name='".str_replace("/", "']/folder[@name='", $parent)."']";
		$expression .= "/folder";
		
		$folders = array();
		$fes = $parser->evaluate($expression);
		foreach ($fes as $folderElement)
		{
			$folderName = $parser->attr($folderElement, "name");
			$newParent = (empty($parent) ? "" : $parent."/").$folderName;
			$libFolders = $this->getFolders($newParent, $compact);
			if ($compact)
			{
				$folders[] = $newParent;
				foreach ($libFolders as $lf)
					$folders[] = $lf;
			}
			else
				$folders[$folderName] = $libFolders;
		}
		
		return $folders;
	}
	
	/**
	 * Get all pages in a given folder.
	 * 
	 * @param	string	$parent
	 * 		The folder to look down from.
	 * 		The default value is empty, for the root.
	 * 		Separate each folder with "/".
	 * 
	 * @return	array
	 * 		An array of all pages.
	 */
	public function getFolderPages($parent = "")
	{
		// Get index path
		$filePath = $this->folderPath."/".$this->mapFile;
		
		// Load file
		$parser = new DOMParser();
		$parser->load($filePath, FALSE);
		
		// Initialize parent
		$parent = trim($parent);
		$parent = trim($parent, "/");
		
		$expression = "/pages";
		if (!empty($parent))
			$expression .= "/folder[@name='".str_replace("/", "']/folder[@name='", $parent)."']";
		$expression .= "/page";
		
		$pages = array();
		// Get document parent
		$pgs = $parser->evaluate($expression);
		foreach ($pgs as $pgElement)
			$pages[] = $parser->attr($pgElement, "name");
		
		return $pages;
	}
}
//#section_end#
?>