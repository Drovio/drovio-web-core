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

importer::import("WUI", "Prototype", "UIObjectPrototype");
importer::import("WUI", "Html", "DOM");

use \WUI\Prototype\UIObjectPrototype;
use \WUI\Html\DOM;

/**
 * Form Builder Prototype
 * 
 * It's the prototype for building every form in the system.
 * All the form objects must inherit this FormPrototype in order to build the spine of a form well-formed.
 * 
 * @version	0.1-1
 * @created	November 28, 2014, 10:18 (EET)
 * @revised	November 28, 2014, 10:18 (EET)
 */
class FormPrototype extends UIObjectPrototype
{
	/**
	 * The form's id.
	 * 
	 * @type	string
	 */
	private $formID;
	
	/**
	 * The form report element container.
	 * 
	 * @type	DOMElement
	 */
	private $formReport;
	
	/**
	 * The form body element container.
	 * 
	 * @type	DOMElement
	 */
	private $formBody;
	
	/**
	 * Defines whether the form will prevent page unload on edit.
	 * 
	 * @type	boolean
	 */
	private $pu = FALSE;
	
	/**
	 * Constructor Method. Defines the form ID.
	 * 
	 * @param	string	$id
	 * 		The form id
	 * 
	 * @param	boolean	$preventUnload
	 * 		If set to true and an input has been changed, the form will prevent the page unload with a message.
	 * 
	 * @return	void
	 */
	public function __construct($id = "", $preventUnload = FALSE)
	{
		// Set the formID
		$this->formID = ($id == "" ? "f".mt_rand() : $id);
		$this->pu = $preventUnload;
		
		// Register the form
		//FormProtocol::register($this->formID);
	}
	
	/**
	 * Builds the form spine and sets the UIObject
	 * 
	 * @param	string	$action
	 * 		The action of the form (if any).
	 * 
	 * @return	FormPrototype
	 * 		The FormPrototype object.
	 */
	public function build($action = "")
	{		
		// Create form element
		$form = DOM::create("form", "", $this->formID);
		DOM::attr($form, "method", "post");
		DOM::attr($form, "action", $action);
		
		// Create form report element
		$this->formReport = DOM::create("div", "", "", "formReport");
		DOM::append($form, $this->formReport);
		
		// Create form body element
		$this->formBody = DOM::create("div", "", "", "formBody");
		DOM::append($form, $this->formBody);
		
		// Set before unload parameter
		//if ($this->pu)
		//	FormProtocol::setPreventUnload($form);
		
		// Set Object
		$this->set($form);
		
		return $this;
	}
	
	/**
	 * Appends a DOMElement to the form body.
	 * 
	 * @param	DOMElement	$element
	 * 		The element to be appended.
	 * 
	 * @return	FormPrototype
	 * 		The FormPrototype object.
	 */
	public function append($element)
	{
		if (is_null($element))
			return FALSE;
		
		DOM::append($this->formBody, $element);
		
		return $this;
	}
	
	/**
	 * Appends a DOMElement to the form report container.
	 * 
	 * @param	DOMElement	$element
	 * 		The element to be appended.
	 * 
	 * @return	FormPrototype
	 * 		The FormPrototype object.
	 */
	public function appendReport($element)
	{
		if (is_null($element))
			return FALSE;
		
		DOM::append($this->formReport, $element);
		
		return $this;
	}
	
	/**
	 * Sets the async attribute to the given form element.
	 * 
	 * @return	FormPrototype
	 * 		The FormPrototype object.
	 */
	public function setAsync()
	{
		// Get form element
		$form = $this->get();
		
		// Set async action
		//FormProtocol::setAsync($form);
		
		return $this;
	}
	
	/**
	 * Store a hidden input's value for later validation.
	 * 
	 * @param	string	$name
	 * 		The input name.
	 * 
	 * @param	string	$value
	 * 		The input value.
	 * 
	 * @return	void
	 */
	public function setHiddenValue($name, $value)
	{
		//FormProtocol::registerVal($this->formID, $name, $value);
	}
	
	/**
	 * Get the form's id.
	 * 
	 * @return	string
	 * 		The form id.
	 */
	public function getFormID()
	{
		return $this->formID;
	}
}
//#section_end#
?>