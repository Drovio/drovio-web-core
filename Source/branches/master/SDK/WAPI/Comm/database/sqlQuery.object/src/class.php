<?php
//#section#[header]
// Namespace
namespace WAPI\Comm\database;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
importer::import("WAPI", "Resources", "DOMParser");
importer::import("WAPI", "Resources", "filesystem::fileManager");

use \WAPI\Resources\DOMParser;
use \WAPI\Resources\filesystem\fileManager;


class sqlQuery
{
	/**
	 * Constructor function. Initializes the query variables.
	 * 
	 * @param	string	$id
	 * 		The query id.
	 * 
	 * @param	string	$domain
	 * 		The query domain.
	 * 
	 * @param	boolean	$forceDeployed
	 * 		Setting this variable to true, the system will load the query from the deployed sql library overriding the sql tester mode status.
	 * 
	 * @return	void
	 */
	public function __construct()
	{
	}
	
	/**
	 * Returns the executable query from the library.
	 * 
	 * @param	array	$attr
	 * 		An associative array of the query attributes.
	 * 
	 * @return	string
	 * 		The executable query.
	 */
	public function getQuery($attr = array())
	{
		// Get executable filename
		$fileName = $this->getFileName();
		$query = "";

		// Acquire executable query file
		$nsdomain = str_replace('.', '/', $this->domain);
		if (file_exists(systemRoot.$this->directory."/".$nsdomain."/".$fileName.".sql"))
		{
			$query = fileManager::get(systemRoot.$this->directory."/".$nsdomain."/".$fileName.".sql");
			$query = trim($query);
			
			// Replace Attributes
			foreach ($attr as $key => $value)
			{
				$query = str_replace("$".$key, $value, $query);
				$query = str_replace("{".$key."}", $value, $query);
			}
		}
		else
		{
			throw new Exception("Database Query '$this->domain -> $this->id' not found.");
		}
		return $query;	
	}
}
//#section_end#
?>