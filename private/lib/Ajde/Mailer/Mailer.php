<?php 
require_once "class.phpmailer.php";
require_once "class.smtp.php";
require_once "class.pop3.php";

class Ajde_Mailer extends PHPMailer
{
	public function __construct($exceptions = false)
    {
        parent::__construct($exceptions);
        if (Config::get('mailer') == 'smtp') {
        	$this->isSMTP();
        	$this->Host = Config::get('mailerSmtpHost');
        } else {
        	$this->isMail();
        }
    }
    
	public function SendQuickMail($to, $from, $fromName, $subject, $body) {	
		// set class to use PHP mail function
		// $this->IsMail();
		
		// reset recipients
		$this->clearAllRecipients();
		
		// to
		$this->addAddress($to);
		
		// from
		$this->From = $from;
		
		// fromName
		$this->FromName = $fromName;
		
		// subject
		$this->Subject = $subject;
		
		// body
		$this->Body = $body;
		
		// alt body
		$this->AltBody = strip_tags($body);
		
		// set html content type
		$this->isHTML(true);
		
		// send!
		return $this->send();
	}
	
	public function addAddress($address, $name = '')
	{
		if (Config::get('mailerDebug') === true) {
			$address = Config::get('email'); 
		}
		return parent::addAnAddress('to', $address, $name);
	}
}