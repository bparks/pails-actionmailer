<?php

namespace Pails\ActionMailer\Transports;

interface ITransport
{
    function __construct($options);
    function deliver($message);
}
