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
            $configs = Config::get('mailerConfig');
            foreach ($configs as $k => $v) {
                $this->$k = $v;
            }
        } else {
            $this->isMail();
        }
    }

    public function sendUsingModel($identifier, $toEmail, $toName = '', $data = [])
    {
        $email = new EmailModel();
        if ($email->loadByField('identifier', $identifier)) {

            $template = $email->getTemplate();

            $fromName = $email->getFromName();
            $fromEmail = $email->getFromEmail();
            $subject = $this->replaceData($template->getSubject(), $data);

            $markup = $this->rel2abs($this->replaceData($template->getMarkup(), $data));
            $body = PHP_EOL . $this->rel2abs($this->replaceData($template->getContent($markup), $data));

            // reset recipients
            $this->clearAllRecipients();

            // to
            $this->addAddress($toEmail, $toName);

            // from
            $this->From = $fromEmail;

            // fromName
            $this->FromName = $fromName;

            // subject
            $this->Subject = $subject;

            // utf8 please
            $this->CharSet = "utf-8";

            // body
            $this->msgHTML($body);

            // send!
            $status = $this->send();

            // log
            if (class_exists('MailerlogModel')) {
                MailerlogModel::log($fromEmail, $fromName, $toEmail, $toName, $subject, $body, $status ? 1 : 0);
            }

            return $status;
        } else {
            throw new Ajde_Exception('Email with identifier ' . $identifier . ' not found');
        }
    }

    private function rel2abs($text)
    {
        $base = Config::get('site_root');
        $replace = '$1' . $base . '$2$3';

        // Look for images
        $pattern = "#(<\s*?img\s*?[^>]*src\s*?=[\"'])(?!http)([^\"'>]+)([\"'>]+)#";
        $text = preg_replace($pattern, $replace, $text);

        // Look for links
        $pattern = "#(<\s*?a\s*?[^>]*href\s*?=[\"'])(?!http)([^\"'>]+)([\"'>]+)#";
        $text = preg_replace($pattern, $replace, $text);

        return $text;
    }

    private function mergeData($data)
    {
        $defaultData = [
            'sitename' => Config::get('sitename')
        ];

        return array_merge($defaultData, $data);
    }

    private function replaceData($string, $data)
    {
        $data = $this->mergeData($data);

        foreach ($data as $key => $value) {
            $string = str_replace('%' . $key . '%', $value, $string);
        }

        return $string;
    }

    public function SendQuickMail($to, $from, $fromName, $subject, $body, $toName = '')
    {
        // set class to use PHP mail function
        // $this->IsMail();

        // reset recipients
        $this->clearAllRecipients();

        // to
        $this->addAddress($to, $toName);

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
        $status = $this->send();

        // log
        if (class_exists('MailerlogModel')) {
            MailerlogModel::log($from, $fromName, $to, $toName, $subject, $body, $status ? 1 : 0);
        }

        return $status;
    }

    public function addAddress($address, $name = '')
    {
        if (Config::get('mailerDebug') === true) {
            $address = Config::get('email');
        }

        return parent::addAnAddress('to', $address, $name);
    }
}
