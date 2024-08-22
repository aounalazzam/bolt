<?php

require "./vendor/autoload.php";

use Bolt\Lib\{Bootstrap};

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

Bootstrap::run([
    "name" => "Bolt",
]);
