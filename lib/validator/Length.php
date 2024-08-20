<?php

namespace Bolt\Lib\Validator;

class Length
{
    static  function max(int $length): string
    {
        return "maxLength_$length";
    }

    static function min(int $length): string
    {
        return "minLength_$length";
    }
}
