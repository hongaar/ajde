<?php

class MailerlogModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'subject';

    public static function log($fromEmail, $fromName = '', $toEmail, $toName = '', $subject, $body, $status = 0)
    {
        $mailerlog = new self();
        $mailerlog->populate(array(
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'to_name' => $toName,
            'to_email' => $toEmail,
            'subject' => $subject,
            'body' => $body,
            'status' => $status
        ));
        return $mailerlog->insert();
    }
}
