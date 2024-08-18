<?php
session_start();

include_once "./lib/db.php";
include_once "./lib/app.php";

DatabaseConnection::init([
    "host" => env("DATABASE_HOST"),
    "username" => env("DATABASE_USERNAME"),
    "password" => env("DATABASE_PASSWORD"),
    "database" => env("DATABASE_NAME"),
]);

App::run([
    "name" => "",
]);
