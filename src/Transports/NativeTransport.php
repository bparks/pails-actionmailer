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
        $options = array_merge($options, ['Content-Type' => $message->getContentTypeHeader()]);
        $headers = implode("\r\n", array_map(function ($key, $value) { return $key.': '.$value; },
            array_keys($options),
            array_values($options)
        ));
        $body = $message->render();
        $to = $message->getTo();
        if (is_array($to))
            $to = implode(", ", $to);
        mail($message->getTo(), $message->getSubject(), $body, $headers);
    }
}
