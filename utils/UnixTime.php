<?php

namespace Bolt\Utils;

class UnixTime
{
    static function getCurrentTimeByMilliseconds(): int
    {
        return round(microtime(true) * 1000);
    }

    static function getCurrentTimeBySeconds(): int
    {
        return time();
    }

    static function getCurrentTimeByMinutes(): int
    {
        return floor(time() / 60);
    }

    static function compareTimeByMilliseconds(int $time1, int $time2): int
    {
        return ($time2 - $time1) / 1000;
    }

    static function compareTimeBySeconds(int $time1, int $time2): int
    {
        return $time2 - $time1;
    }

    static function compareTimeByMinutes(int $time1, int $time2): int
    {
        return ($time2 - $time1) / 60;
    }
}
