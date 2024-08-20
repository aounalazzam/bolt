<?php

namespace Bolt\Utils;

class UnauthorizedException extends \Exception
{
    public function __construct($message = "Unauthorized")
    {
        parent::__construct($message, 401);
        header($_SERVER['SERVER_PROTOCOL'] . " 401 Unauthorized");
    }
}
