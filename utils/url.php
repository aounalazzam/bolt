<?php

namespace Bolt\Utils;

class URL
{
    static function getServerProtocol()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    }


    static function getBaseUrl()
    {
        $protocol = URL::getServerProtocol();
        return "$protocol://{$_SERVER["HTTP_HOST"]}{$_SERVER['REQUEST_URI']}";
    }
}
