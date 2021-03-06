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

importer::import("WAPI", "Environment", "cookies");
importer::import("WAPI", "Environment", "url");

use \WAPI\Environment\cookies;
use \WAPI\Environment\url;

/**
 * Website Session Manager
 * 
 * Handles all session storage data.
 * 
 * @version	0.1-1
 * @created	November 27, 2014, 20:45 (EET)
 * @revised	November 27, 2014, 20:45 (EET)
 */
class session
{
	/**
	 * The session's expiration time (in seconds)
	 * 
	 * @type	string
	 */
	const EXPIRE = 18000;
	
	/**
	 * Init session, timers, validators and set extra options.
	 * 
	 * @param	array	$options
	 * 		A set of options like the session_id etc.
	 * 
	 * @return	void
	 */
	public static function init($options = array())
	{
		// Start session
		self::start();
		
		// Initialise the session timers
		self::setTimers();
		
		// Validate this session
		self::validate();
		
		// Set session options
		self::setOptions($options);
	}
	
	/**
	 * Get a session variable value.
	 * 
	 * @param	string	$name
	 * 		The name of the variable.
	 * 
	 * @param	mixed	$default
	 * 		The value that will be returned if the variable doesn't exist.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	string
	 * 		Returns the session's variable value.
	 */
	public static function get($name, $default = NULL, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		if (isset($_SESSION[$namespace][$name]))
			return $_SESSION[$namespace][$name];
			
		return $default;
	}
	
	/**
	 * Set a session variable value.
	 * 
	 * @param	string	$name
	 * 		The name of the variable.
	 * 
	 * @param	mixed	$value
	 * 		The value with which the variable will be set.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	mixed
	 * 		The old value of the variable, or NULL if not set.
	 */
	public static function set($name, $value = NULL, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		$old = isset($_SESSION[$namespace][$name]) ? $_SESSION[$namespace][$name] : NULL;

		if (NULL === $value)
			unset($_SESSION[$namespace][$name]);
		else
			$_SESSION[$namespace][$name] = $value;

		return $old;
	}
	
	/**
	 * Check if there is a session variable.
	 * 
	 * @param	string	$name
	 * 		The variable name.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	boolean
	 * 		True if the variable exists, false otherwise.
	 */
	public static function has($name, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		return isset($_SESSION[$namespace][$name]);
	}
	
	/**
	 * Delete a set of session variables under the same namespace.
	 * 
	 * @param	string	$name
	 * 		The variable name.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function remove($name, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		$value = NULL;
		if (isset($_SESSION[$namespace][$name]))
		{
			$value = $_SESSION[$namespace][$name];
			unset($_SESSION[$namespace][$name]);
		}

		return $value;
	}
	
	/**
	 * Delete a set of session variables under the same namespace.
	 * 
	 * @param	string	$namespace
	 * 		The namespace to be cleared.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function clearSet($namespace)
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);
			
		unset($_SESSION[$namespace]);
		return TRUE;
	}
	
	/**
	 * Get session name.
	 * 
	 * @return	string
	 * 		The session name.
	 */
	public static function getName()
	{
		return session_name();
	}
	
	/**
	 * Get session id.
	 * 
	 * @return	string
	 * 		The session unique id.
	 */
	public static function getID()
	{
		return session_id();
	}
	
	/**
	 * Destroy session.
	 * 
	 * @return	boolean
	 * 		True on success, False on failure.
	 */
	public static function destroy()
	{
		$sessionCookie = cookies::get(session_name());
		if (!empty($sessionCookie))
			cookies::delete(session_name());

		session_unset();
		session_destroy();

		return TRUE;
	}
	
	/**
	 * Return the in-memory size of the session ($_SESSION) array.
	 * 
	 * @return	integer
	 * 		The memory size in length.
	 */
	public static function getSize()
	{
		return strlen(serialize($_SESSION));
	}
	
	/**
	 * Set the validation timers.
	 * 
	 * @param	boolean	$forceRegenerate
	 * 		Forces the timers to regenerate (in case of an expiration or something).
	 * 
	 * @return	void
	 */
	protected static function setTimers($forceRegenerate = FALSE)
	{
		$start = time();
		
		// If there is no starting point, restart all over again
		if (!self::has('timer.start', "session") || $forceRegenerate)
		{
			self::set('timer.start', $start, "session");
			self::set('timer.last', $start, "session");
			self::set('timer.now', $start, "session");
		}

		// Set current timers
		self::set('timer.last', self::get('timer.now', NULL, "session"), "session");
		self::set('timer.now', time(), "session");
	}
	
	/**
	 * Set the session options.
	 * It supports only the session id for now.
	 * 
	 * @param	array	$options
	 * 		An array of options for the session.
	 * 		It supports only the session id (id) for now.
	 * 
	 * @return	void
	 */
	protected static function setOptions(array $options)
	{
		// Set name
		if (isset($options['id']))
			session_id(md5($options['id']));

		// Sync the session maxlifetime
		ini_set('session.gc_maxlifetime', self::EXPIRE);
	}
	
	/**
	 * Start the session.
	 * 
	 * @return	void
	 */
	protected static function start()
	{
		register_shutdown_function('session_write_close');
		session_cache_limiter('none');
		
		// Set Session cookie params
		$sessionCookieParams = session_get_cookie_params();
		$rootDomain = url::getDomain();
		
		session_set_cookie_params(
			$sessionCookieParams["lifetime"], 
			$sessionCookieParams["path"], 
			$rootDomain, 
			$sessionCookieParams["secure"], 
			$sessionCookieParams["httponly"]
		);
		
		// Set name
		session_name("ss");

		// Session start
		session_start();
	}
	
	/**
	 * Validate the session and reset if necessary.
	 * 
	 * @return	void
	 */
	protected static function validate()
	{
		// Regenerate session if gone too long and reset timers
		if ((time() - self::get('timer.start', NULL, "session") > self::EXPIRE))
		{
			session_regenerate_id(true);
			self::setTimers(TRUE);
		}
		
		// Destroy session if expired
		if ((time() - self::get('timer.last', NULL, "session") > self::EXPIRE))
			self::destroy();
	}
	
	/**
	 * Get a namespace string for storage.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	string
	 * 		The namespace string.
	 */
	private static function getNS($namespace)
	{
		// Add prefix to namespace to avoid collisions.
		return "__".strtoupper($namespace);
	}
}
//#section_end#
?>