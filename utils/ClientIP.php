<?php

namespace Bolt\Utils;

class ClientIP
{
    static function get(): string
    {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = trim($ipList[0]);
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return "";
        }

        return  $ip;
    }
}
