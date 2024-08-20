<?php

namespace Bolt\Utils;

class ConflictException extends \Exception
{
    public function __construct($message = "Conflict")
    {
        parent::__construct($message, 409);
        header($_SERVER['SERVER_PROTOCOL'] . " 409 Conflict");
    }
}
