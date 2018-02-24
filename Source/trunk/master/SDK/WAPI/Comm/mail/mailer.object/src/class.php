<?php
//#section#[header]
// Namespace
namespace WAPI\Comm\mail;

// Use Important Headers
use \WAPI\Platform\importer;
use \Exception;
//#section_end#
//#section#[class]
/**
 * @library	WAPI
 * @package	Comm
 * @namespace	\mail
 * 
 * @copyright	Copyright (C) 2014 Skyworks SD. All rights reserved.
 */

importer::import("WAPI", "Comm", "phpmailer::phpmailer");
importer::import("WAPI", "Comm", "phpmailer::smtp");
importer::import("WAPI", "Comm", "phpmailer::pop3");

use \WAPI\Comm\phpmailer\phpmailer;
use \WAPI\Comm\phpmailer\pop3;
use \WAPI\Comm\phpmailer\smtp;


/**
 * Website mailer
 * 
 * Manages all mail actions for the website.
 * 
 * @version	0.1-1
 * @created	November 28, 2014, 9:40 (EET)
 * @revised	November 28, 2014, 9:40 (EET)
 */
class mailer extends PHPMailer
{
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	const CLASS_NAME = "rbMailer";
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	const CLASS_VERSION = "2.0.4";
	
	/**
	 * {description}
	 * 
	 * @type	{type}
	 */
	private $optionSetDefined = FALSE;
	
	/**
	 * {description}
	 * 
	 * @return	void
	 */
	protected function intialize()
	{
		//Set Exception display
		$this->exceptions = FALSE;
		//enables SMTP debug information (for testing)
		$this->SMTPDebug = FALSE;
		$this->Debugoutput = "error_log";
		
		//For pop, may need object
		//$this->do_debug = 2;
			
		//Set common Mailer Options
		$this->XMailer = "Redback - ".self::CLASS_NAME." Version : ".self::CLASS_VERSION ;
		$this->CharSet = 'UTF-8';
		
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$optionSetArray
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function options($optionSetArray)
	{	
		if(!is_array($optionSetArray) || empty($optionSetArray))
			return;
		try
		{
			//Set send method
			$this->IsSMTP();  			
			//IsMail() 
			//IsSendmail() 
			//IsQmail() 
			
			//On Email
			//Requireed - Stop Script
			$this->Hostname = $this->getValue($this->Hostname, $optionSetArray['Hostname']);
			
 
			//Optional - Set Defaults
			$this->ContentType = $this->getValue($this->ContentType, $optionSetArray['ContentType']);
			$this->Encoding= $this->getValue($this->Encoding, $optionSetArray['Encoding']);
			$this->ReturnPath= $this->getValue($this->ReturnPath, $optionSetArray['ReturnPath']);
			$this->WordWrap= $this->getValue($this->WordWrap, $optionSetArray['WordWrap']);
			$this->ConfirmReadingTo= $this->getValue($this->ConfirmReadingTo, $optionSetArray['ConfirmReadingTo']);
			
  			// PROPERTIES FOR SMTP
			//Requireed - Stop Script
			//Optional - Set Defaults
			$this->Host = $this->getValue($this->Host , $optionSetArray['SMTPHost']);
			$this->Port = $this->getValue($this->Port , $optionSetArray['SMTPPort']);
			$this->SMTPSecure = $this->getValue($this->SMTPSecure , $optionSetArray['SMTPSecure']);
			$this->SMTPAuth= $this->getValue($this->SMTPAuth, $optionSetArray['SMTPAuth']);
			$this->Username = $this->getValue($this->Username , $optionSetArray['SMTPUsername']);
			$this->Password = $this->getValue($this->Password , $optionSetArray['SMTPPassword']);
			$this->AuthType = $this->getValue($this->AuthType , $optionSetArray['SMTPAuthType']);
			$this->Realm = $this->getValue($this->Realm , $optionSetArray['SMTPRealm']);
			$this->Workstation = $this->getValue($this->Workstation , $optionSetArray['SMTPWorkstation']);
			$this->Timeout = $this->getValue($this->Timeout , $optionSetArray['SMTPTimeout']);
			// PROPERTIES FOR POP
			//For pop, may need object
			//Requireed - Stop Script
			//Optional - Set Defaults
			$this->host = $this->getValue($this->host , $optionSetArray['POPhost']);
			$this->port = $this->getValue($this->port , $optionSetArray['POPport']);
			$this->tval = $this->getValue($this->tval , $optionSetArray['POPtval']);
			$this->username = $this->getValue($this->username , $optionSetArray['POPusername']);
			$this->password = $this->getValue($this->password , $optionSetArray['POPpassword']);
			
		}
		catch(Exception $e)
		{
			$this->optionSetDefined = FALSE;
			return;	
		}
		$this->optionSetDefined = TRUE;
				
		//Pop initialazation, not need for send
		//$pop = new POP3();
		//$pop->Authorise($this->host, $this->port, $this->timeout, "username", "password", 1);	
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$currValue
	 * 		{description}
	 * 
	 * @param	{type}	$newValue
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function getValue($currValue, $newValue)
	{
		if (isset($newValue))
			return $newValue;
		else
			return $currValue;		
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$subject
	 * 		{description}
	 * 
	 * @param	{type}	$from
	 * 		{description}
	 * 
	 * @param	{type}	$to
	 * 		{description}
	 * 
	 * @param	{type}	$auto
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function send($subject = '', $from = array(), $to = '', $auto = 1)
	{
		if (!$this->optionSetDefined)
			return;
		
		/* 
		*  If function's args are empty
		*  We assume that user has already given them
		*  using the appropriate functions
		*/		
	
		// Init
		if(!empty($subject))
			$this->setSubject($subject);
		
		if(!empty($from))
		{
			// Key of first element
			$key = key($from);
			if($this->ValidateAddress($key))
			{
				$sAddress = $key;
				$sName = $from[$key];
			}
			else
			{
				$sAddress = $from[$key];
				$sName = '';
			}			
			//1 : Also set Reply-To and Sender
			$this->SetFrom($sAddress, $sName, $auto);
		}
		
		if(!empty($to))
		{
			$rName = '';
			$rAddress = $to;
			if(is_array($to))
			{
				$rAddress = $to[0];
				$rName = $to[1];			
			}
			$this->AddAddress($rAddress, $rName);
		}
		
		try
		{
			parent::Send();
			return TRUE;
		}
		catch (phpmailerException $e)
		{
			//Pretty error messages from PHPMailer
			return FALSE;
		}
		catch (Exception $e) 
		{
			 //Boring error messages from anything else!
			return FALSE;
		}
	}

	/**
	 * {description}
	 * 
	 * @param	{type}	$subject
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function setSubject($subject)
	{
		return  $this->Subject = $subject;
	}
	/**
	 * {description}
	 * 
	 * @param	{type}	$address
	 * 		{description}
	 * 
	 * @param	{type}	$name
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function AddAddress($address, $name = '')
	{
		return  parent::AddAddress($address, $name);
	}	
	/**
	 * {description}
	 * 
	 * @param	{type}	$address
	 * 		{description}
	 * 
	 * @param	{type}	$name
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function AddCC($address, $name = '')
	{
		return  parent::AddCC($address, $name);
	}	
	/**
	 * {description}
	 * 
	 * @param	{type}	$address
	 * 		{description}
	 * 
	 * @param	{type}	$name
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function AddBCC($address, $name = '')
	{
		return  parent::AddBCC($address, $name);
	}
	/**
	 * {description}
	 * 
	 * @param	{type}	$address
	 * 		{description}
	 * 
	 * @param	{type}	$name
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function AddReplyTo($address, $name = '')
	{
		return parent::AddReplyTo($address, $name);
	}
	/**
	 * {description}
	 * 
	 * @param	{type}	$priority
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function setPriority($priority)
	{
		$this->Priority = $priority;	
	}
	/**
	 * {description}
	 * 
	 * @param	{type}	$kind
	 * 		{description}
	 * 
	 * @param	{type}	$addressesArray
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function addMultipleRecipients($kind, $addressesArray)
	{
		if(!is_array($addressesArray))
			return false;	
		//to|rto|cc|bcc
		foreach($address as $addressesArray)
		{
			$addr = array();		
			if(!is_array($address ))
			{
				$addr[0] = $address;
				$addr[1] = '';
			}
			else
			{
				$addr = $address;
			}		
			switch ($kind)
			{
				case "to" :
					$this->AddAddress($addr[0], $addr[1]);
					break;
				case "rto" :
					$this->AddReplyTo($addr[0], $addr[1]);
					break;	
				case "cc" :
					$this->AddCC($addr[0], $addr[1]);
					break;	
				case "bcc" :
					$this->AddBCC($addr[0], $addr[1]);
					break;
				default :			
					break;			
				
			}
		}
	}
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$altBody
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function setAltBody($altBody)
	{
		$this->AltBody = $altBody;
	}
	/**
	 * {description}
	 * 
	 * @param	{type}	$body
	 * 		{description}
	 * 
	 * @return	void
	 */
	public function MsgHTML($body)
	{
		parent::MsgHTML($body);
	}
			
	
	/**
	 * {description}
	 * 
	 * @param	{type}	$address
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function validate_address($address){return $address;}
	/**
	 * {description}
	 * 
	 * @param	{type}	$name
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function validate_name($name){return $name;}
	/**
	 * {description}
	 * 
	 * @param	{type}	$subject
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function validate_subject($subject){return $subject;}
	/**
	 * {description}
	 * 
	 * @param	{type}	$htmlBody
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function validate_htmlBody($htmlBody){return $htmlBody;}
	/**
	 * {description}
	 * 
	 * @param	{type}	$altBody
	 * 		{description}
	 * 
	 * @return	void
	 */
	private function validate_altBody($altBody){return $altBody;}
	
}
//#section_end#
?>