<?php

namespace Bolt\Lib\Database;

class CollectionTypes
{
    static function string(int $length, mixed $nullable = false, string $default = null): array
    {
        return [
            "type" => "string",
            "default" => $default,
            "length" => $length,
            "nullable" => $nullable === null
        ];
    }

    static function json(mixed $nullable = false): array
    {
        return [
            "type" => "json",
            "length" => null,
            "nullable" => $nullable
        ];
    }

    static function hash(): array
    {
        return [
            "type" => "json",
            "length" => null,
            "nullable" => false
        ];
    }

    static function url(): array
    {
        return [
            "type" => "json",
            "length" => null,
            "nullable" => false
        ];
    }

    static function array(mixed $nullable = false): array
    {
        return [
            "type" => "json",
            "length" => null,
            "nullable" => $nullable === null
        ];
    }

    static function translatableString(mixed $nullable = false): array
    {
        return [
            "type" => "json",
            "length" => null,
            "nullable" => $nullable === null
        ];
    }

    static function number(int $length, mixed $nullable = false, string $default = null): array
    {
        return [
            "type" => "number",
            "length" => $length,
            "default" => $default,
            "nullable" => $nullable === null
        ];
    }

    static function relational(string $tableName, mixed $nullable = false): array
    {
        return [
            "table" => $tableName,
            "type" => "relational",
            "length" => null,
            "nullable" => $nullable === null
        ];
    }
}
