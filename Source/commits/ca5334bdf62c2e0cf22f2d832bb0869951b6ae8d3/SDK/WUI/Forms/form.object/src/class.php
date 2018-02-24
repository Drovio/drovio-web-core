<?php
//#section#[header]
// Namespace
namespace WUI\Forms;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Forms
 * @namespace	{empty}
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WUI", "Prototype", "FormPrototype");
importer::import("WUI", "Forms", "formControls::formItem");
importer::import("WUI", "Forms", "formControls::formInput");
importer::import("WUI", "Forms", "formControls::formButton");
importer::import("WUI", "Forms", "formControls::formLabel");
importer::import("WUI", "Html", "DOM");

use \WUI\Prototype\FormPrototype;
use \WUI\Forms\formControls\formItem;
use \WUI\Forms\formControls\formInput;
use \WUI\Forms\formControls\formButton;
use \WUI\Forms\formControls\formLabel;
use \WUI\Html\DOM;

/**
 * Form Item Factory
 * 
 * Builds a form and provides a "factory" for building all the necessary form items.
 * It implements the FormProtocol.
 * 
 * @version	{empty}
 * @created	April 18, 2013, 11:06 (EEST)
 * @revised	April 25, 2014, 17:20 (EEST)
 */
class form extends FormPrototype
{
	/**
	 * Creates a form and returns the element.
	 * If the moduleID is not given, it creates a simple form with action attribute.
	 * Otherwise it creates a module async form.
	 * 
	 * @param	string	$action
	 * 		The form action url string.
	 * 
	 * @param	boolean	$async
	 * 		Sets the async attribute for simple forms.
	 * 
	 * @return	DOMElement
	 * 		The form element.
	 */
	public function build($action = "", $async = TRUE)
	{
		// Create simple action form
		parent::build($action);
		if ($async)
			parent::setAsync();
		
		// Insert hidden form id
		$hiddenFormID = $this->getInput($type = "hidden", $name = "__fid", $value = $this->getFormID());
		$this->append($hiddenFormID);
		
		return $this;
	}
	
	/**
	 * Gets the form ID from the form that performed the post action.
	 * 
	 * @return	string
	 * 		The form id.
	 */
	public static function getPostedFormID()
	{
		return $_POST['__fid'];
	}
	
	/**
	 * Builds and returns an input item.
	 * 
	 * @param	string	$type
	 * 		The input's type. This must abide by the rules of the possible input types.
	 * 
	 * @param	string	$name
	 * 		The input's name.
	 * 
	 * @param	string	$value
	 * 		The input's default value.
	 * 
	 * @param	string	$class
	 * 		The extra class for the input.
	 * 
	 * @param	boolean	$autofocus
	 * 		Inserts the autofocus attribute to the input.
	 * 
	 * @param	boolean	$required
	 * 		Indicates this input as required.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getInput($type = "text", $name = "", $value = "", $class = "", $autofocus = FALSE, $required = FALSE)
	{
		$fi = new formInput();
		$input = $fi->build($type, $name, $this->getInputID($name), $value, $required)->get();
		DOM::appendAttr($input, "class", $class);
		
		if ($autofocus)
			DOM::attr($input, 'autofocus', "autofocus");
		
		// Register hidden value
		if ($type == "hidden")
			parent::setHiddenValue($name, $value);
		
		return $input;
	}
	
	/**
	 * Builds and returns a form label.
	 * 
	 * @param	string	$text
	 * 		The label's text.
	 * 
	 * @param	string	$for
	 * 		The input's id where this label is pointing to.
	 * 
	 * @param	string	$class
	 * 		The extra class for the label.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getLabel($text, $for = "", $class = "")
	{
		$fl = new formLabel();
		$label = $fl->build($text, $for)->get();
		DOM::appendAttr($label, "class", $class);
		
		return $label;
	}
	
	/**
	 * Builds and returns a button (as an input).
	 * 
	 * @param	string	$title
	 * 		The button's title.
	 * 
	 * @param	string	$name
	 * 		The button's name.
	 * 
	 * @param	string	$class
	 * 		The extra class of the button.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getButton($title, $name = "", $class = "")
	{
		$fb = new formButton();
		$button = $fb->build($title, "button", $this->getInputID($name), $name)->get();
		DOM::appendAttr($button, "class", $class);
		
		return $button;
	}
	
	/**
	 * Builds and returns a specific submit button.
	 * 
	 * @param	string	$title
	 * 		The button's title.
	 * 
	 * @param	string	$id
	 * 		The button's id.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getSubmitButton($title, $id = "")
	{
		$fb = new formButton();
		return $fb->build($title, "submit", $id, "", TRUE)->get();
	}
	
	/**
	 * Builds and returns a specific reset button.
	 * 
	 * @param	string	$title
	 * 		The button's title.
	 * 
	 * @param	string	$id
	 * 		The button's id.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getResetButton($title, $id = "")
	{
		$fb = new formButton();
		return $fb->build($title, "reset", $id)->get();
	}
	
	/**
	 * Builds and returns a form textarea.
	 * 
	 * @param	string	$name
	 * 		The textarea's name.
	 * 
	 * @param	string	$value
	 * 		The textarea's default value.
	 * 
	 * @param	string	$class
	 * 		The extra class for the textarea.
	 * 
	 * @param	boolean	$autofocus
	 * 		Inserts the autofocus attribute to the input.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getTextarea($name = "", $value = "", $class = "", $autofocus = FALSE)
	{
		// Create Item
		$fi = new formItem();
		$textarea = $fi->build("textarea", $name, $id, $value)->get();
		DOM::appendAttr($textarea, "class", $class);
		DOM::nodeValue($textarea, $value);
		
		if ($autofocus)
			DOM::attr($input, 'autofocus', "autofocus");
		
		return $textarea;
	}
	
	/**
	 * Builds and returns an HTML5 fieldset.
	 * 
	 * @param	string	$title
	 * 		The fieldset's legend title.
	 * 
	 * @param	string	$name
	 * 		The fieldset's name.
	 * 
	 * @param	string	$class
	 * 		The extra class for the fieldset.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getFieldset($title, $name = "", $class = "")
	{
		// Create Item
		$fi = new formItem();
		$fieldset = $fi->build("fieldset", $name, $this->getInputID($name))->get();
		DOM::appendAttr($fieldset, "class", $class);
		
		$legend = DOM::create("legend", $title);
		DOM::append($fieldset, $legend);
		
		return $fieldset;
	}
	
	/**
	 * Builds and returns an option select input.
	 * 
	 * @param	string	$name
	 * 		The select's name.
	 * 
	 * @param	boolean	$multiple
	 * 		Option for multiple selection.
	 * 
	 * @param	string	$class
	 * 		The extra class for the select item.
	 * 
	 * @param	array	$options
	 * 		An array of option elements as select contents.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getSelect($name = "", $multiple = FALSE, $class = "", $options = array())
	{
		// Create Item
		$fi = new formItem();
		$select = $fi->build("select", $name, $this->getInputID($name), "", $class)->get();
		
		if ($multiple)
			DOM::attr($select, "multiple", "multiple");
		
		// Insert options if any
		foreach ($options as $option)
			DOM::append($select, $option);
		
		return $select;
	}
	
	/**
	 * Builds and returns a select input with a given resource.
	 * 
	 * @param	string	$name
	 * 		The select's name.
	 * 
	 * @param	boolean	$multiple
	 * 		Option for multiple selection.
	 * 
	 * @param	string	$class
	 * 		The extra class for the select item.
	 * 
	 * @param	array	$resource
	 * 		The select's resource as an associative array with key the value of the option and value the title.
	 * 
	 * @param	string	$selectedValue
	 * 		The selected value of the given resource.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getResourceSelect($name = "", $multiple = FALSE, $class = "", $resource = array(), $selectedValue = "")
	{
		// Get select element
		$select = $this->getSelect($name, $multiple, $class);
		
		// Set resource context
		foreach ($resource as $value => $title)
		{
			$option = $this->getOption($title, $value, $value == $selectedValue);
			DOM::append($select, $option);
		}
		
		return $select;
	}
	
	/**
	 * Builds and returns a select option item.
	 * 
	 * @param	string	$title
	 * 		The option's title.
	 * 
	 * @param	string	$value
	 * 		The option's value.
	 * 
	 * @param	boolean	$selected
	 * 		Specifies a selected option in the select item.
	 * 
	 * @param	string	$id
	 * 		The option element id.
	 * 
	 * @param	string	$class
	 * 		The option element class.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getOption($title, $value = "", $selected = FALSE, $id = "", $class = "")
	{
		$fi = new formItem();
		$option = $fi->build("option", "", $id, $value, $class)->get();
		
		// Option's title
		$titleElement = $title;
		if (gettype($titleElement) == "string")
			$titleElement = DOM::create("span", $title);
		DOM::append($option, $titleElement);
		
		if ($selected)
			DOM::attr($option, "selected", "selected");
		
		return $option;
	}
	
	/**
	 * Builds and returns a select option group.
	 * 
	 * @param	string	$label
	 * 		The group's lavel.
	 * 
	 * @param	array	$options
	 * 		An array of option elements for the group.
	 * 
	 * @return	DOMElement
	 * 		{description}
	 */
	public function getOptionGroup($label, $options = array())
	{
		$fi = new formItem();
		$optgroup = $fi->build("optgroup")->get();
		
		// Attributes
		DOM::attr($optgroup, "label", $label);
		
		// Insert options if any
		foreach ($options as $option)
			DOM::append($optgroup, $option);
		
		return $optgroup;
	}
	
	/**
	 * Create a system specific form input id.
	 * 
	 * @param	string	$name
	 * 		The input's name.
	 * 
	 * @return	string
	 * 		{description}
	 */
	protected function getInputID($name)
	{
		return (empty($name) ? "" : "i_".$this->getFormID()."_".$name);
	}
}
//#section_end#
?>