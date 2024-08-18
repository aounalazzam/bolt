<?php

function getServerProtocol()
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
}


function getBaseUrl()
{
    $protocol = getServerProtocol();
    return "$protocol://{$_SERVER["HTTP_HOST"]}{$_SERVER['REQUEST_URI']}";
}
