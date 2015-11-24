<?php

namespace Pails\ActionMailer\Transports;

class NativeTransport implements ITransport
{
    private $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function deliver($message)
    {
        $options = $this->options;
        if ($message->getFrom() != '')
            $options = array_merge($options, ['From' => $message->getFrom()]);
        $headers = implode("\r\n", array_map(function ($key, $value) { return $key.': '.$value; },
            array_keys($options),
            array_values($options)
        ));
        $body = '';
        mail($message->to, $message->subject, $body, $headers);
    }
}
