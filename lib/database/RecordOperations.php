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
        return "'" . mysqli_real_escape_string($GLOBALS['conn'], $value) . "'";
    }

    function create(array $data): int|string
    {
        $data['created'] = UnixTime::getCurrentTimeByMiliseconds();
        $data['updated'] = UnixTime::getCurrentTimeByMiliseconds();
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_map(function ($value) {
            $type = gettype($value);
            return $type === "string" ? $this->makeValidSqlString($value) : ($type === "array" ? $this->makeValidSqlString(json_encode($value)) : $value);
        }, $data));

        SQL::run(
            "INSERT INTO  `{$this->tableName}` ($columns) values ($values)"
        );

        return SQL::lastInsertId();
    }

    function getOne(string $filter): array|null
    {
        return SQL::run("SELECT * FROM `{$this->tableName}` WHERE $filter LIMIT 1", true);
    }

    function getById(string $id): array|null

    {
        return SQL::run("SELECT * FROM `{$this->tableName}` WHERE id = $id", true);
    }

    function getList(int $limit, int $offset = 0): array
    {
        return SQL::run("SELECT * FROM `{$this->tableName}` LIMIT {$limit}" . ($offset > 0 ? " OFFSET {$offset}" : ""));
    }


    function getAll(): array
    {
        return SQL::run("SELECT * FROM `{$this->tableName}`");
    }

    function update(string $id, array $data): true
    {
        foreach ($data as $key => $value) {
            $type = gettype($value);
            $parsedValue = $type === "string" ? $this->makeValidSqlString($value) : ($type === "array" ? $this->makeValidSqlString(json_encode($value, true)) : $value);
            SQL::run("UPDATE `{$this->tableName}` SET $key = $parsedValue WHERE id = $id");
        }

        $ti = UnixTime::getCurrentTimeByMiliseconds();

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
