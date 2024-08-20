<?php

namespace Bolt\Utils;

class UnixTime
{
    static function getCurrentTimeByMiliseconds()
    {
        return time() * 1000;
    }

    static function getCurrentTimeBySeconds()
    {
        return time();
    }
}
