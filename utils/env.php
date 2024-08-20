<?php

namespace Bolt\Utils;

class Env
{
    private static $environmentVariables = [];

    static function get(string $name): mixed
    {
        if (count(Env::$environmentVariables) > 0) {
            return Env::$environmentVariables[$name];
        }

        $envFile = './.env';

        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $envLines = explode("\n", $envContent);

            foreach ($envLines as $line) {
                $line = trim($line);
                if (!empty($line) && strpos($line, '=') !== false) {
                    [$envName, $envValue] = explode('=', $line, 2);
                    Env::$environmentVariables[$envName] = str_replace(['\'', '"'], '', $envValue);
                }
            }
        }

        return Env::$environmentVariables[$name] ?? "";
    }
}
