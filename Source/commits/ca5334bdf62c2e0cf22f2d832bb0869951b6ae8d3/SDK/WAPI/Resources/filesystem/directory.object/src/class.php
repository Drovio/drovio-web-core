<?php
//#section#[header]
// Namespace
namespace WAPI\Resources\filesystem;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Resources
 * @namespace	\filesystem
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

/**
 * Directory
 * 
 * System's directory object. Used to acquire a directory's details
 * 
 * @version	0.1-1
 * @created	June 23, 2014, 12:29 (EEST)
 * @revised	October 1, 2014, 16:11 (EEST)
 */
class directory
{
	/**
	 * Returns all the contents of a folder in an array.
	 * ['dirs'] for directories
	 * ['files'] for files
	 * 
	 * @param	string	$directory
	 * 		The directory we are searching
	 * 
	 * @param	boolean	$includeHidden
	 * 		Include hidden files (files that start with a dot) in the results
	 * 
	 * @param	boolean	$includeDotFolders
	 * 		Include dot folders ('.', '..') in the results
	 * 
	 * @param	boolean	$relativeNames
	 * 		Return content names, instead of paths
	 * 
	 * @return	array
	 * 		Returns all the contents of a folder in an array.
	 * 		['dirs'] for directories
	 * 		['files'] for files
	 */
	public static function getContentList($directory, $includeHidden = FALSE, $includeDotFolders = FALSE, $relativeNames = FALSE)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		$directory = self::normalize($directory."/");
		
		// Check directory existance
		if (!is_dir($directory))
			return FALSE;
		
		$contents = array();
		$iterator = new RecursiveDirectoryIterator($directory);
		
		// Inner directories first
		foreach ($iterator as $path)
		{		
			// Full path or relative name
			$p = ($relativeNames ? $path->getBasename() : $path->__toString());
		
			if ($path->isDir())
			{
				// Filter dots
				$basename = $path->getBasename();
				
				if (!$includeDotFolders && ($basename == "." || $basename == ".."))
					continue;
					
				if (!$includeHidden && (!strncmp($basename, ".", strlen("."))))
					continue;
				
				$contents['dirs'][] = $p;
			}
			else
			{
				// Filter hidden
				if (!$includeHidden && (!strncmp($path->getBasename(), ".", strlen("."))))
					continue;
				
			 	$contents['files'][] = $p;
			}
		}
		
		return $contents;
	}
	
	/**
	 * Returns all the content details of a folder in an array:
	 * ['dirs'] for directories
	 * ['files'] for files
	 * 
	 * Each elements holds the following details (keys):
	 * name		-> File's name
	 * path		-> File's path
	 * extension	-> File's Extnsion
	 * lastModified	-> Last Modified Date (unformated)
	 * size		-> File's size
	 * type		-> File's type
	 * 
	 * @param	string	$directory
	 * 		The directory we are searching
	 * 
	 * @param	boolean	$includeHidden
	 * 		Include hidden files (files that start with a dot) in the results
	 * 
	 * @return	array
	 * 		Returns all the content details of a folder in an array
	 */
	public static function getContentDetails($directory, $includeHidden = FALSE)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		$directory = self::normalize($directory."/");
		
		// Check directory existance
		if (!is_dir($directory))
			return FALSE;
		
		$contents = array();
		$iterator = new RecursiveDirectoryIterator($directory);
		
		// Inner directories first
		foreach ($iterator as $path)
		{
			// Filter hidden
			if (!$includeHidden && (!strncmp($path->getBasename(), ".", strlen("."))))
				continue;
		
			$details = array();
			$details['name'] = $path->getBasename();
			$details['path'] = $path->__toString();
			$details['extension'] = $path->getExtension();
			$details['lastModified'] = $path->getMTime();
			$details['size'] = $path->getSize();
			$details['type'] = $path->getType();
			/*$details['isExecutable'] = $path->isExecutable();
			$details['isReadable'] = $path->isReadable();
			$details['isWritable'] = $path->isWritable();*/
			
			$basename = $path->getBasename();
			if ($path->isDir())
			{
				// Filter dots
				if ($basename == "." || $basename == "..")
					continue;
				$contents['dirs'][$basename] = $details;
			}
			else
			 	$contents['files'][$basename] = $details;
		}
		
		return $contents;
	}
	
	/**
	 * Checks if a directory is empty.
	 * 
	 * @param	string	$path
	 * 		The path of the folder
	 * 
	 * @param	string	$name
	 * 		The name of the folder
	 * 
	 * @return	mixed
	 * 		Returns if the given directory is empty [TRUE] or not [FALSE]. Returns NULL if an error occurs.
	 */
	public static function isEmpty($path, $name = "")
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return NULL;
		
		// Remove Directory
		$directory = ($name = "" ? $path."/" : $path."/".$name."/");
		
		// Collapse redundant slashes
		$directory = self::normalize($directory);
		
		if (!is_dir($directory))
			return NULL;
		
		$handle = opendir($directory);
		while ($entry = readdir($handle))
			if ($entry != "." && $entry != "..")
				return FALSE;


		return TRUE;
	}
	
	/**
	 * Normalizes a path by collapsing redundant slashes.
	 * 
	 * @param	string	$path
	 * 		The path to be normalized.
	 * 
	 * @return	string
	 * 		Returns the normalized path
	 */
	public static function normalize($path)
	{
		return preg_replace("/\/{2,}/", "/", $path);
	}
	
}
//#section_end#
?>