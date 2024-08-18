<?php

include_once "./lib/sql.php";

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

class Collection
{
    static function create(string $collectionName, array $schema): RecordOperations
    {
        $rules = [
            "id INT(8) NOT NULL AUTO_INCREMENT PRIMARY KEY",
            "created BIGINT NOT NULL",
            "updated BIGINT NOT NULL",
        ];


        foreach ($schema as $key => $value) {

            [
                "type" => $type,
                "nullable" => $isNull,
                "length" => $length
            ] = $value;

            $nullable = $isNull ? "NULL" : "NOT NULL";

            switch ($type) {
                case 'string':
                    $rules[] = "$key VARCHAR({$length}) {$nullable}" . (isset($value["default"]) ? " DEFAULT {$value["default"]}" : "");
                    break;

                case 'number':
                    $typeFn = $length > 10 ? "BIGINT" : "INT";
                    $rules[] = "$key $typeFn({$length}) {$nullable}" . (isset($value["default"]) ? " DEFAULT {$value["default"]}" : "");
                    break;

                case 'relational':
                    $relationalTableName = $value["table"];
                    $rules[] = "$key INT(8), FOREIGN KEY ($key) REFERENCES $relationalTableName(id)";
                    break;

                case 'json':
                    $rules[] = "$key TEXT {$nullable}";
                    break;

                default:
                    throw new Exception("Error Processing With Database Type");
            }
        }

        $types = implode(",", $rules);

        SQL::run("CREATE TABLE IF NOT EXISTS `$collectionName`($types)");

        return new RecordOperations($collectionName);
    }
}
