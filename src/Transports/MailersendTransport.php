<?php
namespace Pails\ActionMailer\Transports;

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

class MailersendTransport implements ITransport
{
    private $apikey;
    private $from;
    private $from_name;
    private $options;

    public function __construct($options)
    {
        $keys = array_keys($options);
        if (!in_array('apikey', $keys) ||
            !in_array('from', $keys) ||
            !in_array('from_name', $keys))
            throw new Exception("Sendgrid transport requires options apikey, from, from_name");

        $this->apikey    = $options['apikey'];
        $this->from      = $options['from'];
        $this->from_name = $options['from_name'];
        unset($options['apikey']);
        unset($options['from']);
        unset($options['from_name']);
        $this->options   = $options;
    }

    public function deliver($message)
    {
        $mailersend = new MailerSend(['api_key' => $this->apikey]);

        $recipients = [];
        $to = $message->getTo();
        if (is_array($to))
        {
            foreach ($to as $rcpt)
                $recipients[] = new Recipient($rcpt, $rcpt);
        }
        else
        {
            $recipients[] = new Recipient($to, $to);
        }

        $emailParams = (new EmailParams())
            ->setFrom($this->from)
            ->setFromName($this->from_name)
            ->setRecipients($recipients)
            ->setSubject($message->getSubject())
            ->setHtml($message->renderHtml())
            ->setText($message->renderText());

        $mailersend->email->send($emailParams);
    }
}
