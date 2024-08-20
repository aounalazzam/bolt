<?php

namespace Bolt\Utils;

class InternalServerErrorException extends \Exception
{
    public function __construct($message = "Internal Server Error")
    {
        parent::__construct($message, 500);
        header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
    }
}
