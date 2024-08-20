<?php

require "./vendor/autoload.php";

use Bolt\Utils\{Env};
use Bolt\Lib\{Bootstrap};
use Bolt\Lib\Database\{DatabaseConnection};

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

session_start();

DatabaseConnection::init([
    "host" =>  Env::get("DATABASE_HOST"),
    "username" => Env::get("DATABASE_USERNAME"),
    "password" =>  Env::get("DATABASE_PASSWORD"),
    "database" =>  Env::get("DATABASE_NAME"),
]);

Bootstrap::run([
    "name" => "Bolt",
]);
