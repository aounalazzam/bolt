<?php

include_once "./lib/router.php";

class App
{
    static  string $name;
    static function run($options)
    {
        App::$name = $options['name'];

        header("X-Powered-By: " . App::$name);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header('X-XSS-Protection: 1');
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');

        Router::run();
    }
}
