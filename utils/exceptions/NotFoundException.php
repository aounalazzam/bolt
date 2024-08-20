<?php

namespace Bolt\Utils;

class NotFoundException extends \Exception
{
    public function __construct($message = "Not Found")
    {
        parent::__construct($message, 404);
        header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
    }
}
