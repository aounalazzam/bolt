<?php

include_once "./lib/db.php";
include_once "./utils/time.php";

function makeValidSqlString($value)
{
    return "'" . mysqli_real_escape_string($GLOBALS['conn'], $value) . "'";
}


class RecordOperations
{
    private string $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    function create(array $data): int|string
    {
        $data['created'] = getTime();
        $data['updated'] = getTime();
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_map(function ($value) {
            $type = gettype($value);
            return $type === "string" ? makeValidSqlString($value) : ($type === "array" ? makeValidSqlString(json_encode($value)) : $value);
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
            $parsedValue = $type === "string" ? makeValidSqlString($value) : ($type === "array" ? makeValidSqlString(json_encode($value, true)) : $value);
            SQL::run("UPDATE `{$this->tableName}` SET $key = $parsedValue WHERE id = $id");
        }

        $ti = getTime();

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
