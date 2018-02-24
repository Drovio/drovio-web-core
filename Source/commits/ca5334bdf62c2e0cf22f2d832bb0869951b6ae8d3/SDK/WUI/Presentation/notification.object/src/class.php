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
 * @namespace	\
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WUI", "Prototype", "UIObjectPrototype");
importer::import("WUI", "Html", "HTML");

use \WUI\Prototype\UIObjectPrototype;
use \WUI\Html\HTML;

/**
 * Website Notification
 * 
 * Creates a UI notification for all usages.
 * It can be used to notify the user for changes and updates, show warning messages or show succeed messages after a successful post.
 * 
 * @version	0.1-1
 * @created	October 1, 2014, 17:01 (EEST)
 * @revised	October 1, 2014, 17:01 (EEST)
 */
class notification extends UIObjectPrototype
{
	/**
	 * The error notification indicator.
	 * 
	 * @type	string
	 */
	const ERROR = "error";
	/**
	 * The warning notification indicator.
	 * 
	 * @type	string
	 */
	const WARNING = "warning";
	/**
	 * The info notification indicator.
	 * 
	 * @type	string
	 */
	const INFO = "info";
	/**
	 * The success notification indicator.
	 * 
	 * @type	string
	 */
	const SUCCESS = "success";
	
	/**
	 * The notification's body.
	 * 
	 * @type	DOMElement
	 */
	private $body;
	
	/**
	 * Builds the notification.
	 * 
	 * @param	string	$type
	 * 		The notification's type. See class constants for better explanation.
	 * 
	 * @param	boolean	$header
	 * 		Specified whether the notification will have header.
	 * 
	 * @param	boolean	$timeout
	 * 		Sets the notification to fadeout after 1.5 seconds.
	 * 
	 * @param	boolean	$disposable
	 * 		Lets the user to be able to close the notification.
	 * 
	 * @return	notification
	 * 		The notification object.
	 */
	public function build($type = self::INFO, $header = FALSE, $timeout = FALSE, $disposable = FALSE)
	{
		// Normalize type
		$type = (empty($type) ? self::INFO : $type);
		
		// Create notification holder
		$notificationHolder = HTML::create("div", "", "", "uiNotification");
		HTML::addClass($notificationHolder, $type);
		if ($timeout)
			HTML::addClass($notificationHolder, "timeout");
		$this->set($notificationHolder);
		
		// Build Header (if any)
		if ($header)
			$this->buildHead($type, $disposable);

		// Build Body
		$this->buildBody();
		
		return $this;
	}
	
	/**
	 * Appends a DOMElement to notification body.
	 * 
	 * @param	DOMElement	$content
	 * 		The element to be appended.
	 * 
	 * @return	notification
	 * 		The notification object.
	 */
	public function append($content)
	{
		HTML::append($this->body, $content);
		return $this;
	}
	
	/**
	 * Creates and appends a custom notification message.
	 * 
	 * @param	mixed	$message
	 * 		The message content (string or DOMElement)
	 * 
	 * @return	notification
	 * 		The notification object.
	 */
	public function appendCustomMessage($message)
	{
		if (gettype($message) == "string")
			$customMessage = HTML::create("div", $message, "", "customMessage");
		else
		{
			$customMessage = HTML::create("div", "", "", "customMessage");
			HTML::append($customMessage, $message);
		}
		
		return $this->append($customMessage);
	}
	
	/**
	 * Builds the notification head.
	 * 
	 * @param	mixed	$title
	 * 		The header's title.
	 * 
	 * @param	boolean	$disposable
	 * 		Adds a close button to header and lets the user to be able to close the notification.
	 * 
	 * @return	notification
	 * 		The notification object.
	 */
	private function buildHead($title, $disposable = FALSE)
	{
		// Build Head Element
		$head = HTML::create("div", $title, "", "uiNtfHead");

		// Populate the close button
		if ($disposable)
		{
			$closeBtn = HTML::create("span", "", "", "closeBtn");
			HTML::append($head, $closeBtn);
		}

		// Append To Holder
		HTML::append($this->get(), $head);

		return $this;
	}
	
	/**
	 * Builds the notification body
	 * 
	 * @return	notification
	 * 		The notification object.
	 */
	private function buildBody()
	{
		// Build Body Element
		$body = HTML::create("div", "", "", "uiNtfBody");
		$this->body = $body;
		
		// Populate the notification icon
		$icon = HTML::create("span", "", "", 'uiNtfIcon');
		HTML::append($body, $icon);
		
		// Append To Holder
		HTML::append($this->get(), $body);
		
		return $this;
	}
}
//#section_end#
?>