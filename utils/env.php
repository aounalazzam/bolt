<?php

$environmentVariables = [];

function env(string $name): mixed
{
    global $environmentVariables;

    if (count($environmentVariables) > 0) {
        return $environmentVariables[$name];
    }

    $envFile = './.env';

    if (file_exists($envFile)) {
        $envContent = file_get_contents($envFile);
        $envLines = explode("\n", $envContent);

        foreach ($envLines as $line) {
            $line = trim($line);
            if (!empty($line) && strpos($line, '=') !== false) {
                [$envName, $envValue] = explode('=', $line, 2);
                $environmentVariables[$envName] = str_replace(['\'', '"'], '', $envValue);
            }
        }
    }

    return $environmentVariables[$name] ?? "";
}
