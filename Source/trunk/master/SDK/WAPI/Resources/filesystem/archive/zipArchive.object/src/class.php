<?php
//#section#[header]
// Namespace
namespace WAPI\Resources\filesystem\archive;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Resources
 * @namespace	\filesystem\archive
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Resources", "filesystem::folderManager");
importer::import("WAPI", "Resources", "filesystem::fileManager");
importer::import("WAPI", "Resources", "filesystem::directory");

use \ZipArchive;
use \API\Resources\filesystem\folderManager;
use \API\Resources\filesystem\fileManager;
use \API\Resources\filesystem\directory;

/**
 * Zip Archive
 * 
 * System's zip archive manager
 * 
 * @version	0.1-1
 * @created	June 24, 2014, 11:05 (EEST)
 * @revised	October 1, 2014, 16:22 (EEST)
 */
class zipManager
{
	/**
	 * Used in zipArchive::read() to get resource's location inside the zip file
	 * 
	 * @type	integer
	 */
	const LOCATION = 0;
	/**
	 * Used in zipArchive::read() to get resource from a zip file
	 * 
	 * @type	integer
	 */
	const RESOURCE = 1;
	/**
	 * Used in zipArchive::read() to get resource's contents from the zip file
	 * 
	 * @type	integer
	 */
	const CONTENTS = 2;

	/**
	 * Checks if a file exists and is a zip archive
	 * 
	 * @param	string	$archive
	 * 		The path of the archive
	 * 
	 * @return	boolean
	 * 		True if file exists and is a zip archive, false otherwise
	 */
	public static function exists($archive)
	{
		$archive = directory::normalize($archive);
		
		// Open zip
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;
			
		$zip->close();
		return TRUE;
	}

	/**
	 * Create a zip archive that contains the desired files/folders.
	 * 
	 * @param	string	$archive
	 * 		The path of the new archive
	 * 
	 * @param	array	$contents
	 * 		An array that holds the paths of the files/folders to include in the zip:
	 * 		['dirs'] for directories
	 * 		['files'] for files
	 * 
	 * @param	boolean	$recursive
	 * 		If set to TRUE, all necessary parent folders of the archive will be created as well.
	 * 
	 * @param	boolean	$includeHidden
	 * 		If set to TRUE, all hidden folders of the archive will be created as well.
	 * 
	 * @return	voidboolean
	 * 		Status of the process
	 */
	public static function create($archive, $contents = array(), $recursive = FALSE, $includeHidden = FALSE)
	{
		$archive = directory::normalize($archive);
		
		// Create Directory
		if (!is_dir(dirname($archive)))
		{
			if ($recursive)
				folderManager::create(dirname($archive), "", 0777, TRUE);
			else
				return FALSE;
		}
		
		if (empty($contents))
			return FALSE;
		
		// Check if $contents has proper format
		if (!is_array($contents))
			return FALSE;
		
		$allowed = array('dirs', 'files'); 
		$contents = array_intersect_key($contents, array_flip($allowed));
		if (empty($contents))
			return FALSE;
		
		foreach ($contents as $array)
			if (!is_array($array))
				return FALSE;

		// Create zip
		$zip = new ZipArchive();
		if ($zip->open($archive, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE ) !== TRUE)
			return FALSE;

		$status = self::zipFiles($zip, $contents, "", "", $includeHidden);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Status of the process
	 * 
	 * @param	string	$archive
	 * 		The path of the archive.
	 * 
	 * @param	string	$destination
	 * 		The location where the extracted files will be placed into.
	 * 
	 * @param	boolean	$recursive
	 * 		If set to TRUE, all necessary parent folders of the destination folder will be created as well.
	 * 
	 * @param	mixed	$entries
	 * 		A (list of) file(s) / index(es) inside the zip that represents the file(s) to be extracted. If left empty, the whole archive is extracted.
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	public static function extract($archive, $destination, $recursive = FALSE, $entries = NULL)
	{
		$archive = directory::normalize($archive);
		
		// Check Directories
		if (!file_exists($archive) || (!$recursive && !is_dir($destination."/")))
			return FALSE;
		
		// Create $destination folder if needed and if 'recursive'
		if ($recursive && (!is_dir($destination."/")))
			folderManager::create($destination."/", "", 0777, TRUE);
		
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;
		
		// Flip index entries to names
		if (is_int($entries))
			$entries = self::identify($zip, $entries);
		
		if (is_array($entries))
			foreach ($entries as $key => $e)
				if (is_int($e))
					$entries[$key] = self::identify($zip, $e);
		
		// Create zip
		$status = $zip->extractTo($destination, $entries);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Returns the details of a zip archive in an array.
	 * 
	 * @param	string	$archive
	 * 		The path of the archive.
	 * 
	 * @param	mixed	$entry
	 * 		Get specific details for an entry by giving its name or index in the archive
	 * 
	 * @param	boolean	$byName
	 * 		Request result array keys to be the entries indicies, or names (if set to TRUE)
	 * 
	 * @return	array
	 * 		The details of a zip archive:
	 * 		['archive'] for general details
	 * 		['entries'] for specific entry details
	 * 		
	 * 		The general details include the following (keys):
	 * 		length		-> Number of entries in the archive
	 * 		status		-> The status of the archive in readable form
	 * 		_status		-> The archive status code
	 * 		_systemStatus	-> The system's status code
	 * 		file		-> Name of the archive file
	 * 		comment		-> Archive's comment
	 * 		
	 * 		The entry details include the following (keys):
	 * 		name		-> Entry's name in the archive
	 * 		index		-> Entry's index in the archive
	 * 		crc		-> Entry's crc code
	 * 		size		-> Entry's uncompressed size in bytes
	 * 		lastModified	-> Last modification time of the file that represents the entry
	 * 		compressedSize	-> Entry's compressed size in bytes
	 * 		compressionMethod	-> The amount (code) of compression in the archive
	 */
	public static function getDetails($archive, $entry = NULL, $byName = FALSE)
	{
		$archive = directory::normalize($archive);
		
		// Check source
		if (!file_exists($archive))
			return FALSE;
			
		// Open zip
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;

		$details = self::acquireDetails($zip, $entry, $byName);
		$zip->close();
		
		return $details;
	}
	
	/**
	 * Appends files/folders in the archive
	 * 
	 * @param	string	$archive
	 * 		The path of the archive.
	 * 
	 * @param	array	$files
	 * 		An array that holds the paths of the files/folders to append in the zip:
	 * 		['dirs'] for directories
	 * 		['files'] for files
	 * 
	 * @param	string	$innerDirectory
	 * 		A directory inside the archive where the appended files will be placed. If empty, all files will be appended in the archive's root
	 * 
	 * @param	boolean	$includeHidden
	 * 		if true includes hidden files
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	public static function append($archive, $files = array(), $innerDirectory = "", $includeHidden = FALSE)
	{
		$archive = directory::normalize($archive);
		
		// Check Source
		if (!file_exists($archive))
			return FALSE;
		
		// Check if $files has proper format
		if (!is_array($files))
			return FALSE;
		
		$allowed = array('dirs', 'files'); 
		$files = array_intersect_key($files, array_flip($allowed));
		if (empty($files))
			return FALSE;
		
		foreach ($files as $array)
			if (!is_array($array))
				return FALSE;
		
		// Append to zip
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;

		if (!empty($innerDirectory))
			$innerDirectory = trim($innerDirectory, "/")."/";
		$status = self::zipFiles($zip, $files, "", $innerDirectory, $includeHidden);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Creates an empty dir in an archive
	 * 
	 * @param	string	$archive
	 * 		The path of the archive
	 * 
	 * @param	string	$innerDirectoryName
	 * 		Name of the new folder
	 * 
	 * @param	string	$innerParentDirectory
	 * 		Path inside the archive
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	public static function createInnerDirectory($archive, $innerDirectoryName, $innerParentDirectory = "")
	{
		$archive = directory::normalize($archive);
		
		// Check Source and append to archive
		$zip = new ZipArchive();
		$openStatus = FALSE;
		if (!file_exists($archive))
			$openStatus = $zip->open($archive, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
		else
			$openStatus = $zip->open($archive);
		
		if ($openStatus !== TRUE)
			return FALSE;

		$innerDirectoryName = trim($innerDirectoryName, "/")."/";
		if (!empty($innerParentDirectory) && is_string($innerParentDirectory))
			$innerParentDirectory = trim(directory::normalize($innerParentDirectory), "/")."/";
		
		$status = $zip->addEmptyDir($innerParentDirectory.$innerDirectoryName);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Appends "on the fly" the $fileContents in the specified archive
	 * 
	 * @param	string	$archive
	 * 		The path of the archive
	 * 
	 * @param	string	$fileContents
	 * 		Contents for the file in the archive
	 * 
	 * @param	string	$fileName
	 * 		Name of the file in the archive
	 * 
	 * @param	string	$innerDirectory
	 * 		Inner archive directory where the file will be placed
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	public static function createInnerFile($archive, $fileContents, $fileName, $innerDirectory = "")
	{
		$archive = directory::normalize($archive);
		
		if (empty($fileName))
			return FALSE;
		
		// Check Source and append to archive
		$zip = new ZipArchive();
		$openStatus = FALSE;
		if (!file_exists($archive))
			$openStatus = $zip->open($archive, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
		else
			$openStatus = $zip->open($archive);
		
		if ($openStatus !== TRUE)
			return FALSE;

		$fileName = trim($fileName, "/");
		if (!empty($innerDirectory))
			$innerDirectory = trim(directory::normalize($innerDirectory), "/")."/";
		
		$status = $zip->addFromString($innerDirectory.$fileName, $fileContents);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Removes files/folders from the archive
	 * 
	 * @param	string	$archive
	 * 		The path of the archive.
	 * 
	 * @param	mixed	$contents
	 * 		A (list of) file(s) / index(es) inside the zip that represents the file(s) to be removed.
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	public static function remove($archive, $contents)
	{
		$archive = directory::normalize($archive);
		
		// Check Source
		if (!file_exists($archive))
			return FALSE;
		
		// Check if $files has proper format
		if (!is_array($contents))
			$contents = array($contents);
		
		// Remove from zip
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;

		$status = self::removeFiles($zip, $contents);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Renames the contents of the archive
	 * 
	 * @param	string	$archive
	 * 		The path of the archive.
	 * 
	 * @param	mixed	$contents
	 * 		A (list of) file(s) / index(es) inside the zip that represents the file(s) to be renamed.
	 * 
	 * @param	mixed	$newNames
	 * 		A (list of) new name(s) for the renamed entries.
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	public static function rename($archive, $contents, $newNames)
	{
		$archive = directory::normalize($archive);
		
		// Check Source
		if (!file_exists($archive))
			return FALSE;
		
		// Check if $contents has proper format
		if (!is_array($contents))
			$contents = array( $contents );
			
		// Check if $newNames has proper format
		if (!is_array($newNames))
			$newNames = array( $newNames );
		
		// Rename in zip
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;

		$status = self::renameFiles($zip, $contents, $newNames);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Relocates contents inside the archive (NOT YET IMPLEMENTED)
	 * 
	 * @param	string	$archive
	 * 		The path of the archive.
	 * 
	 * @param	mixed	$origins
	 * 		A (list of) file(s) / index(es) inside the zip that represents the file(s) to be relocated.
	 * 
	 * @param	mixed	$destinations
	 * 		A (list of) destination(s) for the relocated entries.
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	public static function copy($archive, $origins, $destinations)
	{
		$archive = directory::normalize($archive);
		
		return FALSE;
		
		// Check Source
		if (!file_exists($archive))
			return FALSE;
		
		// Check if $origins has proper format
		if (!is_array($origins))
			$origins = array( $origins );
			
		// Check if $destinations has proper format
		if (!is_array($destinations))
			$destinations = array( $destinations );
		
		// Copy in zip
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;

		$status = self::copyFiles($zip, $origins, $destinations);
		$zip->close();
		
		return $status;
	}
	
	/**
	 * Reads a source from the archive.
	 * 
	 * @param	string	$archive
	 * 		The path of the archive.
	 * 
	 * @param	mixed	$identifier
	 * 		A file / index inside the zip that represent the file to be read.
	 * 
	 * @param	integer	$typeOfResponse
	 * 		The type of the returned value. Use zipArchive::LOCATION for the location of the resource, zipArchive::CONTENTS for the contents of the resource, and zipArchive::RESOURCE for the resource itself.
	 * 
	 * @return	mixed
	 * 		Returns either the location of the resource, the contents of the resource, or the resource itself.
	 */
	public static function read($archive, $identifier, $typeOfResponse = self::CONTENTS)
	{
		$archive = directory::normalize($archive);
		
		// Check Source
		if (!file_exists($archive))
			return FALSE;
		
		// Check if $identifier has proper format
		if (!is_int($identifier) && !is_string($identifier))
			return FALSE;
		
		// Read from zip
		$zip = new ZipArchive();
		if ($zip->open($archive) !== TRUE)
			return FALSE;

		if (is_int($identifier))
			$identifier = self::identify($zip, $identifier);
		
		if ($typeOfResponse == self::LOCATION)
			return "zip://".dirname($archive)."/".basename($archive)."#".$identifier;
		
		if ($typeOfResponse == self::RESOURCE)
			return $zip->getStream($identifier);
			
		$contents = self::readFile($zip, $identifier);
		$zip->close();
		
		return $contents;
	}
	
	/**
	 * Reads the contents of an entry
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	string	$identifier
	 * 		The name of an entry inside the archive
	 * 
	 * @return	string
	 * 		The contents of a file inside the archive.
	 */
	private static function readFile($zip, $identifier)
	{
		$contents = '';
		$fp = $zip->getStream($identifier);
		if (!$fp)
			return FALSE;
			
		while (!feof($fp))
		{
			$contents .= fread($fp, 2);
		}
		
		fclose($fp);
		return $contents;
	}
	
	/**
	 * Acquires the details of an archive and some or all of the entries of the archive
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	mixed	$entry
	 * 		A name or index of an entry to acquire details for. If empty the details of the whole archive are acquired.
	 * 
	 * @param	boolean	$byName
	 * 		Request result array keys to be the entries indicies, or names (if set to TRUE)
	 * 
	 * @return	array
	 * 		An array holding the details:
	 * 		archive ->
	 * 		length : number of entries in the archive,
	 * 		status : status of the archive,
	 * 		_status : status code of the archive,
	 * 		_status : status code of the archive,
	 * 		_systemStatus : status system code of the archive,
	 * 		file : name of the archive,
	 * 		comment : comment of the archive
	 * 		
	 * 		entries -> index ->
	 * 		crc : entry's crc code,
	 * 		lastModified : entry's last date of modification,
	 * 		compressedSize : entry's compressed size,
	 * 		compressionMethod : entry's compression method
	 */
	private static function acquireDetails($zip, $entry = "", $byName = FALSE)
	{
		$details = array();
		
		// General file details
		$details['archive']['length'] = $zip->numFiles;
		$details['archive']['status'] = $zip->getStatusString();
		$details['archive']['_status'] = $zip->status;
		$details['archive']['_systemStatus'] = $zip->statusSys;
		$details['archive']['file'] = $zip->filename;
		$details['archive']['comment'] = $zip->comment;
	
		// Details for zip contents / needed entries
		$details['entries'] = array();
		if (empty($entry))
			for ($i = 0; $i < $zip->numFiles; $i++)
			{
				$stat = $zip->statIndex($i);
				$idx = ($byName ? self::identify($zip, $i) : $i);
				// Fix negative crc from PHP
				if ($stat['crc'] < 0)
				    $stat['crc'] += 0x100000000;
				
				// Change keys
				$stat['lastModified'] = $stat['mtime'];
				$stat['compressedSize'] = $stat['comp_size'];
				$stat['compressionMethod'] = $stat['comp_method'];
				unset($stat['mtime']);
				unset($stat['comp_size']);
				unset($stat['comp_method']);
				$details['entries'][$idx] = $stat;
			}
		else
		{
			if (!is_string($entry) && !is_int($entry))
				return FALSE;
			
			if (is_string($entry))
				$entry = self::locate($zip, $entry);
				
			$stat = $zip->statIndex($entry);
			$entry = ($byName ? self::identify($zip, $entry) : $entry);
			// Fix negative crc from PHP
			if ($stat['crc'] < 0)
			    $stat['crc'] += 0x100000000;
			
			// Change keys
			$stat['lastModified'] = $stat['mtime'];
			$stat['compressedSize'] = $stat['comp_size'];
			$stat['compressionMethod'] = $stat['comp_method'];
			unset($stat['mtime']);
			unset($stat['comp_size']);
			unset($stat['comp_method']);
			$details['entries'][$entry] = $stat;
		}
		
		
		return $details;
	}
	
	/**
	 * Returns the name of an entry
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	integer	$index
	 * 		The index of the entry
	 * 
	 * @return	string
	 * 		Name of an entry inside an archive
	 */
	private static function identify($zip, $index)
	{
		return $zip->getNameIndex($index);
	}
	
	/**
	 * Returns the index of an entry
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	string	$name
	 * 		The name of the entry
	 * 
	 * @return	integer
	 * 		Index of an entry inside an archive
	 */
	private static function locate($zip, $name)
	{
		return $zip->locateName($name);
	}
	
	/**
	 * Sets / Gets comments inside the archive
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	string	$comment
	 * 		The comment to set in the archive. If empty, the already set comment is acquired, instead
	 * 
	 * @param	mixed	$identifier
	 * 		The name or index of an entry to get or set a comment for
	 * 
	 * @return	mixed
	 * 		Either the comment of an entry or the status of the commenting process
	 */
	private static function comment($zip, $comment = "", $identifier = "")
	{
		if (empty($comment))
		{
			// Get comment
			if (empty($identifier))
				return $zip->getArchiveComment();
			if (is_int($identifier))
				return $zip->getCommentIndex($identifier);
			return $zip->getCommentName($identifier);
		}
		
		// Set comment
		if (empty($identifier))
			return $zip->setArchiveComment($comment);
		if (is_int($identifier))
			return $zip->setCommentIndex($identifier, $comment);
		return $zip->setCommentName($identifier, $comment);
	}
	
	/**
	 * Packs the specified files in a zip archive
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	array	$contents
	 * 		An array that holds the paths of the files/folders to include in the zip:
	 * 		['dirs'] for directories
	 * 		['files'] for files
	 * 
	 * @param	string	$localname
	 * 		This is used to adjust the inner path of a file in the zip when packing folder's contents recursively.
	 * 
	 * @param	string	$innerDirectory
	 * 		The directory where the files will be added in the zip.
	 * 
	 * @param	boolean	$includeHidden
	 * 		if True includes hidden files
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	private static function zipFiles($zip, $contents, $localname = "", $innerDirectory = "", $includeHidden)
	{	
		$status = FALSE;
		foreach ((array)$contents['dirs'] as $dir)
		{
			$zip->addEmptyDir($innerDirectory.$localname.basename($dir));
			// For this dir, pack all inner files
			$c = directory::getContentList($dir."/", $includeHidden);
			
			self::zipFiles($zip, $c, basename($dir)."/", $innerDirectory.$localname, $includeHidden);
		}
		foreach ((array)$contents['files'] as $file)
		{
			$zip->addFile($file, $innerDirectory.$localname.basename($file));
		}
		$status = TRUE;
		return $status;
	}
	
	/**
	 * Removes files from an archive
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	array	$contents
	 * 		List of contents to be removed, in the form of name or index
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	private static function removeFiles($zip, $contents)
	{
		foreach ($contents as $file)
		{
			if (!is_string($file) && !is_int($file))
			{
				$zip->unchangeAll();
				return FALSE;
			}
			
			if (is_int($file))
			{
				$file = self::identify($zip, $file);
			}
			
			$zipInfo = self::acquireDetails($zip, "");
			// Closure needs PHP > 5.3.0
			$children = array_filter($zipInfo['entries'], function($var) use ($file)
			{
				return !strncmp($var['name'], $file, strlen($file));
			});
			
			foreach ((array)$children as $details)
				$zip->deleteName($details['name']);
		}
		
		return TRUE;
	}
	
	/**
	 * Renames files in an archive
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	array	$contents
	 * 		A list of contents to be renamed, in the form of names or indexes
	 * 
	 * @param	array	$newNames
	 * 		A list of new names for the contents
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	private static function renameFiles($zip, $contents, $newNames)
	{
		// Check Equal contents / newNames entries
		if (count($contents) != count($newNames))
			return FALSE;
	
		foreach ($contents as $key => $file)
		{
			if (!is_string($file) && !is_int($file))
			{
				$zip->unchangeAll();
				return FALSE;
			}
			
			$identifier = $file;
			if (is_int($file))
				$file = self::identify($zip, $file);
			
			if ($file === FALSE)
				continue;
			
			$zipInfo = self::acquireDetails($zip, "");
			// Closure needs PHP > 5.3.0
			$children = array_filter($zipInfo['entries'], function($var) use ($file)
			{
				return !strncmp($var['name'], $file, strlen($file));
			});
			
			if (count($children) == 0)
				continue;
			
			foreach ((array)$children as $details)
				$zip->renameName($details['name'], preg_replace('/'.preg_quote($file, '/').'/', $newNames[$key], $details['name'], 1));
		}
		
		return TRUE;
	}
	
	/**
	 * Relocates files in an archive. (NOT IMPLEMENTED YET)
	 * 
	 * @param	ZipArchive	$zip
	 * 		A ZipArchive object that handles a zip file
	 * 
	 * @param	array	$origins
	 * 		A list of origin files in the archive to be relocated.
	 * 
	 * @param	array	$destinations
	 * 		A list of destinations in the archive.
	 * 
	 * @return	boolean
	 * 		Status of the process
	 */
	private static function copyFiles($zip, $origins, $destinations)
	{
		// Check Equal origins / destinations entries
		if (count($origins) != count($destinations))
			return FALSE;

		foreach ($origins as $key => $file)
		{
			if (!is_string($file) && !is_int($file))
			{
				$zip->unchangeAll();
				return FALSE;
			}
			
			$identifier = $file;
			if (is_int($file))
			{
				$file = self::identify($zip, $file);
			}
			
			if ($file === FALSE)
				continue;
			
			$zipInfo = self::acquireDetails($zip, "");
			// Closure needs PHP > 5.3.0
			$children = array_filter($zipInfo['entries'], function($var) use ($file)
			{
				return !strncmp($var['name'], $file, strlen($file));
			});
			
			if (count($children) == 0)
				continue;

			/*
			foreach ((array)$children as $details)
				$zip->renameName($details['name'], preg_replace('/'.preg_quote($file, '/').'/', $destinations[$key], $details['name'], 1));
			*/
		}
		
		return TRUE;
	}
}
//#section_end#
?>