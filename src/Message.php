<?php

namespace Pails\ActionMailer;

class Message
{
    private $to, $from, $subject, $view;
    private $transport;

    public function __construct($to, $from, $subject, $view, $transport)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->view = $view;
    }

    public function deliver()
    {
        $transport->send($this);
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getSubject()
    {
        return $this->subject;
    }
}
