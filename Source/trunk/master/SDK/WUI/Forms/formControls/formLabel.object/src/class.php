<?php
//#section#[header]
// Namespace
namespace WUI\Forms\formControls;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Forms
 * @namespace	\formControls
 * 
 * @copyright	Copyright (C) 2013 Skyworks SD. All rights reserved.
 */

importer::import("WUI", "Forms", "formControls::formItem");
importer::import("UI", "Html", "DOM");

use \WUI\Forms\formControls\formItem;
use \UI\Html\DOM;

/**
 * Form Label
 * 
 * Builds a universal form label
 * 
 * @version	{empty}
 * @created	March 11, 2013, 15:26 (EET)
 * @revised	April 18, 2013, 16:00 (EEST)
 */
class formLabel extends formItem
{
	/**
	 * Builds the form label
	 * 
	 * @param	DOMElement	$context
	 * 		The label's context
	 * 
	 * @param	string	$for
	 * 		The element's id that the label is pointing to
	 * 
	 * @return	formLabel
	 */
	public function build($context = NULL, $for = "")
	{
		// Create label
		parent::build("label", "", "", "", "uiFormLabel");
		
		// Populate label attributes and children
		$label = $this->get();
		DOM::attr($label, "for", $for);
		
		if (!is_null($context))
		{
			if (gettype($context) == "string")
				$context = DOM::create("span", $context);
			
			DOM::append($label, $context);
		}
		
		return $this;
	}
}
//#section_end#
?>