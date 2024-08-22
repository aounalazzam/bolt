<?php

namespace Bolt\Lib\Database;

use Bolt\Utils\{UnixTime};
use Bolt\Lib\Database\{SQL};

class RecordOperations
{
    private string $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    private function makeValidSqlString($value)
    {
        return "'" . mysqli_real_escape_string(DatabaseConnection::getConnection(), $value) . "'";
    }

    function create(array $data): array
    {
        $data['created'] = UnixTime::getCurrentTimeByMilliseconds();
        $data['updated'] = UnixTime::getCurrentTimeByMilliseconds();
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_map(function ($value) {
            $type = gettype($value);

            // Do Validation

            return $type === "string" ? $this->makeValidSqlString($value) : ($type === "array" ? $this->makeValidSqlString(json_encode($value)) : $value);
        }, $data));

        SQL::run(
            "INSERT INTO  `{$this->tableName}` ($columns) values ($values)"
        );

        $id = SQL::lastInsertId();

        return SQL::run("SELECT * FROM `{$this->tableName}` WHERE id = $id", true);
    }

    function getOne(string $filter, string $fields = "*"): array|null
    {
        return SQL::run("SELECT {$fields} FROM `{$this->tableName}` WHERE $filter LIMIT 1", true);
    }

    function getById(string $id, string $fields = "*"): array|null

    {
        return SQL::run("SELECT {$fields} FROM `{$this->tableName}` WHERE id = $id", true);
    }

    function getList(int $limit, int $offset = 0, string $fields = "*"): array
    {
        return SQL::run("SELECT {$fields} FROM `{$this->tableName}` LIMIT {$limit}" . ($offset > 0 ? " OFFSET {$offset}" : ""));
    }

    function getFilteredList(string $filter, string $fields = "*"): array
    {
        return SQL::run("SELECT {$fields} FROM `{$this->tableName}` WHERE {$filter}");
    }

    function getAll(string $fields = "*"): array
    {
        return SQL::run("SELECT {$fields} FROM `{$this->tableName}`");
    }

    function update(string $id, array $data): true
    {
        foreach ($data as $key => $value) {
            $type = gettype($value);
            $parsedValue = $type === "string" ? $this->makeValidSqlString($value) : ($type === "array" ? $this->makeValidSqlString(json_encode($value, true)) : $value);
            SQL::run("UPDATE `{$this->tableName}` SET $key = $parsedValue WHERE id = $id");
        }

        $ti = UnixTime::getCurrentTimeByMilliseconds();

        SQL::run("UPDATE `{$this->tableName}` SET updated = $ti WHERE id = $id");
        return true;
    }

    function delete(string $id): true
    {
        SQL::run("DELETE FROM `{$this->tableName}` WHERE id=$id");
        return true;
    }

    function deleteList(string $filter): true
    {
        SQL::run("DELETE FROM `{$this->tableName}` WHERE $filter");
        return true;
    }
}
