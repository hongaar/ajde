<?php

class Ajde_Publisher_Mail extends Ajde_Publisher
{
    private $_recipients;

    public function setRecipients($addresses)
    {
        $this->_recipients = $addresses;
    }

    public function publish()
    {
        $mailer = new Ajde_Mailer();

        $mailer->From = config('app.email');
        $mailer->FromName = config('app.title');
        $mailer->Subject = $this->getTitle();
        $mailer->Body = $this->getMessage().PHP_EOL.PHP_EOL.$this->getUrl();
        $mailer->AltBody = strip_tags($mailer->Body);
        $mailer->isHTML(true);

        $count = 0;
        foreach ($this->_recipients as $to) {
            $mailer->clearAllRecipients();
            $mailer->addAddress($to);
            if ($mailer->send()) {
                $count++;
            }
        }

        return "javascript:alert('Message sent as e-mail to ".$count." recipients');";
    }
}
