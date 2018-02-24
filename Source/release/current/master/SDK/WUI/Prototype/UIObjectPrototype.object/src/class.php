<?php
//#section#[header]
// Namespace
namespace WUI\Prototype;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Prototype
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

/**
 * UI Object Prototype
 * 
 * It is the prototype for all ui objects.
 * All ui objects must inherit this class and implement the "build" method to build the object.
 * 
 * @version	0.1-2
 * @created	June 17, 2014, 20:26 (EEST)
 * @revised	September 30, 2014, 23:53 (EEST)
 */
abstract class UIObjectPrototype
{
	/**
	 * The UI Object Holder
	 * 
	 * @type	DOMElement
	 */
	protected $UIObjectHolder;
	
	/**
	 * It's the abstract Object Builder Function
	 * 
	 * @return	mixed
	 * 		The UIObject reference.
	 */
	abstract public function build();
	
	/**
	 * Gets the UI Object Holder
	 * 
	 * @return	DOMElement
	 * 		The UI Object Holder.
	 */
	public function get()
	{
		return $this->UIObjectHolder;
	}
	
	/**
	 * Sets the UI Object Holder that the inherited class has created.
	 * 
	 * @param	DOMElement	$object
	 * 		The object holder.
	 * 
	 * @return	void
	 */
	protected function set($object = NULL)
	{
		$this->UIObjectHolder = $object;
	}
}
//#section_end#
?>