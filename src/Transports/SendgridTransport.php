<?php

namespace Pails\ActionMailer\Transports;

class SendgridTransport implements ITransport
{
    private $username;
    private $password;
    private $from;
    private $from_name;
    private $options;

    public function __construct($options)
    {
        $keys = array_keys($options);
        if (!in_array('username', $keys) ||
            !in_array('password', $keys) ||
            !in_array('from', $keys) ||
            !in_array('from_name', $keys))
            throw new Exception("Sendgrid transport requires options username, password, from, from_name");

        $this->username  = $options['username'];
        $this->password  = $options['password'];
        $this->from      = $options['from'];
        $this->from_name = $options['from_name'];
        unset($options['username']);
        unset($options['password']);
        unset($options['from']);
        unset($options['from_name']);
        $this->options   = $options;
    }

    public function deliver($message)
    {
        $sendgrid = $this->username == "apikey"
            ? new \SendGrid($this->password, $this->options)
            : new \SendGrid($this->username, $this->password, $this->options);
        $email = new \SendGrid\Mail\Mail;

        $to = $message->getTo();
        if (is_array($to))
        {
            foreach ($to as $rcpt)
            $email->addTo($rcpt);
        }
        else
        {
            $email->addTo($to);
        }

        $email->setFrom($this->from, $this->from_name);
        $email->setSubject($message->getSubject());
        $email->addContent('text/plain', $message->renderText());
        $email->addContent('text/html', $message->renderHtml());

        $sendgrid->send($email);
    }
}
