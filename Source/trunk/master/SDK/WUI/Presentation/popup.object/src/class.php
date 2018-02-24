<?php
//#section#[header]
// Namespace
namespace WUI\Presentation;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WUI
 * @package	Presentation
 * 
 * @copyright	Copyright (C) 2015 Redback. All rights reserved.
 */

importer::import("WUI", "Prototype", "PopupPrototype");
importer::import("WUI", "Html", "HTML");

use \WUI\Prototype\PopupPrototype;
use \WUI\Html\HTML;

/**
 * Redback Popup
 * 
 * This builds a simple popup. It extends the system's popup prototype.
 * 
 * @version	0.1-2
 * @created	October 3, 2014, 20:37 (EEST)
 * @updated	May 29, 2015, 19:09 (EEST)
 */
class popup extends PopupPrototype
{
	/**
	 * Builds the popup content.
	 * 
	 * @param	DOMElement	$content
	 * 		The content of the popup.
	 * 
	 * @return	popup
	 * 		The popup object.
	 */
	public function build($content = NULL)
	{
		// Create container
		$popupContainer = DOM::create("div", "", "", "popup");
		if (!empty($content))
			DOM::append($popupContainer, $content);
		
		// Build popup
		return parent::build($popupContainer);
	}
	
	/**
	 * Returns the html (in string format) of popup.
	 * 
	 * @return	string
	 * 		{description}
	 */
	public function getContent()
	{
		return;
	}
}
//#section_end#
?>