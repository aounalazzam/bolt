<?php

namespace Bolt\Utils;

class ClientException extends \Exception
{
    public function __construct($message = "Bad Request")
    {
        parent::__construct($message, 400);
        header($_SERVER['SERVER_PROTOCOL'] . " 400 Bad Request");
    }
}
