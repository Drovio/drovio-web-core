<?php
//#section#[header]
// Namespace
namespace WAPI\Resources\filesystem;


//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Resources
 * @namespace	\filesystem
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Resources", "filesystem::folderManager");
importer::import("WAPI", "Resources", "filesystem::directory");

use \WAPI\Resources\filesystem\folderManager;
use \WAPI\Resources\filesystem\directory;

/**
 * File Manager
 * 
 * Website's file manager.
 * 
 * @version	0.1-1
 * @created	June 23, 2014, 11:58 (EEST)
 * @revised	October 1, 2014, 16:13 (EEST)
 */
class fileManager
{
	/**
	 * Creates a new text file
	 * 
	 * @param	string	$file
	 * 		The file path
	 * 
	 * @param	string	$contents
	 * 		The text file contents
	 * 
	 * @param	boolean	$recursive
	 * 		Indicates whether the file will create the path's folders if don't exist.
	 * 
	 * @return	boolean
	 * 		Returns True on success, False on failure
	 */
	public static function create($file, $contents = "", $recursive = TRUE)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		$file = directory::normalize($file);
		
		// Create directories if don't exist
		if (!file_exists(dirname($file)))
		{
			if ($recursive)
				folderManager::create(dirname($file), "", 0777, TRUE);
			else
				return FALSE;
		}
			
		// Create File
		return self::put($file, $contents);
			
		return $status;
	}
	
	/**
	 * Remove a file
	 * 
	 * @param	string	$file
	 * 		The file path
	 * 
	 * @return	boolean
	 * 		Returns True on success, False on failure
	 */
	public static function remove($file)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		$file = directory::normalize($file);

		if (file_exists($file))
			return unlink($file);
		
		return FALSE;
	}
	
	/**
	 * Returns the contents of a given file.
	 * 
	 * @param	string	$file
	 * 		The file path.
	 * 
	 * @return	string
	 * 		Returns the contents of a given file.
	 */
	public static function get($file)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;

		$file = directory::normalize($file);
		
		if (file_exists($file))
			return file_get_contents($file);
		
		return "";
	}
	
	/**
	 * Puts contents to the given file.
	 * 
	 * @param	string	$path
	 * 		The file path.
	 * 
	 * @param	string	$contents
	 * 		The contents to be written.
	 * 
	 * @return	boolean
	 * 		True on success
	 * 		False on failure
	 */
	public static function put($path, $contents = "")
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		// Normalize path
		$path = directory::normalize($path);
		
		// Log and write
		$saveFlag = file_put_contents($path, $contents);
		if (is_bool($saveFlag))
			return $saveFlag;
		else
			return TRUE;
	}
	
	/**
	 * Copies a file.
	 * 
	 * @param	string	$from_file
	 * 		The source file path.
	 * 
	 * @param	string	$to_file
	 * 		The destination file path.
	 * 
	 * @param	boolean	$preventOverwrite
	 * 		If set to TRUE the destination file will not be overwritten.
	 * 
	 * @return	boolean
	 * 		True on success
	 * 		False on failure
	 */
	public static function copy($from_file, $to_file, $preventOverwrite = FALSE)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		$from_file = directory::normalize($from_file);
		$to_file = directory::normalize($to_file);
		
		if ($preventOverwrite && file_exists($to_file))
			return FALSE;
		else if (file_exists($from_file))
			return copy($from_file, $to_file);
		
		return FALSE;
	}
	
	/**
	 * Moves a file
	 * 
	 * @param	string	$from_file
	 * 		The source file path.
	 * 
	 * @param	string	$to_file
	 * 		The destination file path.
	 * 
	 * @param	boolean	$preventOverwrite
	 * 		If set to TRUE the destination file will not be overwritten.
	 * 
	 * @return	boolean
	 * 		True on success
	 * 		False on failure
	 */
	public static function move($from_file, $to_file, $preventOverwrite = FALSE)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		$from_file = directory::normalize($from_file);
		$to_file = directory::normalize($to_file);
		
		if ($preventOverwrite && file_exists($to_file))
			return FALSE;
		else if (file_exists($from_file))
			return rename($from_file, $to_file);
		
		return FALSE;
	}
	
		
	
	/**
	 * Returns the size of a file, or FALSE in case of an error.
	 * 
	 * @param	string	$file
	 * 		The path to the file.
	 * 
	 * @param	boolean	$formated
	 * 		If set to TRUE, then the size will be formated.
	 * 
	 * @return	mixed
	 * 		The file size, formated [not in bytes] or not [in bytes], or FALSE in case of an error.
	 */
	public static function getSize($file, $formated = FALSE)
	{
		// Check core functionality
		//if (!accessControl::internalCall())
		//	return FALSE;
		
		if (!file_exists($file))
			return FALSE;
		
		$size = filesize($file);
		if ($formated)
			$size = self::formatBytes($size, $precision = 2);
		
		return $size;
	}
	
	/**
	 * Takes and formats a size in bytes.
	 * 
	 * @param	integer	$bytes
	 * 		The size in bytes
	 * 
	 * @param	integer	$precision
	 * 		The precision of the rounded sizes in digits.
	 * 
	 * @return	integer
	 * 		The size formated in the highest value possible.
	 */
	private function formatBytes($bytes, $precision = 2) { 
		$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
		$bytes = max($bytes, 0); 
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
		$pow = min($pow, count($units) - 1); 
	
		$bytes /= (1 << (10 * $pow)); 
	
		return round($bytes, $precision).' '.$units[$pow]; 
	} 
}
//#section_end#
?>