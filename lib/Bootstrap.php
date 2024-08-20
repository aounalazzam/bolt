<?php

namespace Bolt\Lib;

use Bolt\Lib\Routing\{Router};

class Bootstrap
{
    static  string $name;
    static function run($options)
    {
        Bootstrap::$name = $options['name'];

        header("X-Powered-By: " . Bootstrap::$name);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('X-XSS-Protection: 1');
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');

        Router::run();
    }
}
