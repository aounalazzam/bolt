<?php

namespace Bolt\Utils;

class ForbiddenException extends \Exception
{
    public function __construct($message = "Forbidden")
    {
        parent::__construct($message, 403);
        header($_SERVER['SERVER_PROTOCOL'] . " 403 Forbidden");
    }
}
