<?php

namespace Bolt\Lib\Routing;

class Redirect
{
    static function to(string $url): string
    {
        header("Location: $url", false, 302);
        return "redirect";
    }
}