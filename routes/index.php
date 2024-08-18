<?php
include_once './lib/api.php';

$handleHello = function (RouteRequestData $req) {
    $req->body;
    $req->query;

    return [
        "message" => "Hello World!",
    ];
};

API::post($handleHello);

$handleHello2 = function (RouteRequestData $req) {
    $req->query;

    return [
        "message" => "Hello World!",
    ];
};

API::get($handleHello2);
