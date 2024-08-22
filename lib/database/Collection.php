<?php

namespace Bolt\Lib\Database;

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
                    throw new \Exception("Error Processing With Database Type");
            }
        }

        $types = implode(",", $rules);

        SQL::run("CREATE TABLE IF NOT EXISTS `$collectionName`($types)");

        return new RecordOperations($collectionName);
    }
}
