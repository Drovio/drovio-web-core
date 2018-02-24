<?php
//#section#[header]
// Namespace
namespace WAPI\Comm\database;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Comm
 * @namespace	\database
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Comm", "database::connectors::mysql_dbConnector");

use \WAPI\Comm\database\connectors\mysql_dbConnector;

/**
 * Database Connection
 * 
 * Connects to any database with the proper database connector.
 * 
 * @version	0.1-1
 * @created	June 22, 2014, 23:37 (EEST)
 * @revised	October 1, 2014, 16:35 (EEST)
 */
class dbConnection
{
	/**
	 * Database connector
	 * 
	 * @type	iDbConnector
	 */
	protected $dbConnector;
	
	/**
	 * Database engine
	 * 
	 * @type	string
	 */
	protected $dbType;
	/**
	 * The database URL host
	 * 
	 * @type	string
	 */
	protected $host;
	/**
	 * The database name
	 * 
	 * @type	string
	 */
	protected $database;
	
	/**
	 * The transaction error.
	 * 
	 * @type	string
	 */
	protected $error;
	
	/**
	 * Database username
	 * 
	 * @type	string
	 */
	private $username;
	/**
	 * Database password
	 * 
	 * @type	string
	 */
	private $password;
	
	/**
	 * Set connection options
	 * 
	 * @param	string	$dbType
	 * 		The database engine type
	 * 
	 * @param	string	$host
	 * 		The database host URL
	 * 
	 * @param	string	$database
	 * 		The database name
	 * 
	 * @param	string	$username
	 * 		The user's username
	 * 
	 * @param	string	$password
	 * 		The user's password
	 * 
	 * @return	void
	 */
	public function options($dbType, $host, $database, $username, $password)
	{
		// Initialize Variables
		$this->dbType = $dbType;
		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;

		// Get db Connector according to dbType
		$dbConnectorName = '\\WAPI\\Comm\\database\\connectors\\'.strtolower($this->dbType)."_dbConnector";
		$this->dbConnector = new $dbConnectorName();
		
		// Set dbConnector Handler
		$this->dbConnector->setHandler($this->host, $this->username, $this->password, $this->database);
	}
	
	/**
	 * Executes a query to the database. It supports multiple queries separated with ";".
	 * 
	 * @param	string	$query
	 * 		The query to be executed. It supports many queries separated by ";".
	 * 
	 * @return	mixed
	 * 		Returns FALSE on failure.
	 * 		For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will return a mysqli_result object.
	 * 		For other successful queries mysqli_query() will return TRUE.
	 */
	public function execute($query)
	{
		// Clear error message
		$this->error = "";
		
		try
		{
			
			// Execute
			return $this->dbConnector->execute($query);
		}
		catch (Exception $ex)
		{
			// Store error message
			$this->error = $ex->getMessage();
			
			// Return false
			return FALSE;
		}
	}
	
	/**
	 * Gets the error generated by the previous transaction executed.
	 * 
	 * @return	string
	 * 		The error message thrown by the database connector.
	 */
	public function getError()
	{
		return $this->error;
	}
	
	/**
	 * Clears a string given and returns the cleared one.
	 * 
	 * @param	string	$resource
	 * 		The string to be cleared.
	 * 
	 * @return	string
	 * 		The cleared string.
	 */
	public function escape($resource)
	{
		// If the resource is numeric, there is no need of escaping
		if (is_numeric($resource))
			return $resource;
		
		// Return escaped resource
		return $this->dbConnector->escape($resource);
	}
	
	/**
	 * Fetch results from resource.
	 * 
	 * @param	resource	$resource
	 * 		The database results resource.
	 * 
	 * @param	boolean	$all
	 * 		Whether it will fetch the entire resource into one array.
	 * 
	 * @return	array
	 * 		An array of results.
	 */
	public function fetch($resource, $all = FALSE)
	{
		return $this->dbConnector->fetch($resource, $all);
	}
	
	/**
	 * Transform a resource to an array with the specified key value assignment
	 * 
	 * @param	resource	$resource
	 * 		The resource to parse.
	 * 
	 * @param	string	$key
	 * 		The field of the table that will act as key.
	 * 
	 * @param	string	$value
	 * 		The field of the table that will act as value
	 * 
	 * @return	array
	 * 		The associative array.
	 */
	public function toArray($resource, $key, $value)
	{
		$result = array();
		while($row = $this->fetch($resource))
			$result[$row[$key]] = $row[$value];
		
		// Reset the iterator position
		$this->seek($resource, 0);
		
		return $result;
	}
	
	/**
	 * Sets the iterator of the resource to a given position
	 * 
	 * @param	resource	$resource
	 * 		The resource given
	 * 
	 * @param	integer	$row
	 * 		The position where the iterator will be placed
	 * 
	 * @return	mixed
	 * 		Returns TRUE on success or FALSE on failure.
	 */
	public function seek($resource, $row)
	{
		return $this->dbConnector->seek($resource, $row);
	}

	/**
	 * Returns the count of rows of the given resource
	 * 
	 * @param	resource	$resource
	 * 		The given resource
	 * 
	 * @return	integer
	 * 		Returns the count of rows of the given resource
	 */
	public function get_num_rows($resource)
	{
		return $this->dbConnector->get_num_rows($resource);
	}
}
//#section_end#
?>