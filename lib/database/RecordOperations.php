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

    private function join(
        string $joinType,
        string $joinTable,
        string $onCondition,
        string $fields = "*",
        string $filter = null,
        int $limit = null,
        int $offset = 0
    ): array {
        $joinType = strtoupper($joinType);
        $query = "SELECT {$fields} FROM `{$this->tableName}` {$joinType} JOIN `{$joinTable}` ON {$onCondition}";

        if (!is_null($filter)) {
            $query .= " WHERE {$filter}";
        }

        if (!is_null($limit)) {
            $query .= " LIMIT {$limit}" . ($offset > 0 ? " OFFSET {$offset}" : "");
        }

        return SQL::run($query);
    }

    function innerJoin(
        string $joinTable,
        string $onCondition,
        string $fields = "*",
        string $filter = null,
        int $limit = null,
        int $offset = 0
    ): array {
        return $this->join('INNER', $joinTable, $onCondition, $fields, $filter, $limit, $offset);
    }

    function leftJoin(
        string $joinTable,
        string $onCondition,
        string $fields = "*",
        string $filter = null,
        int $limit = null,
        int $offset = 0
    ): array {
        return $this->join('LEFT', $joinTable, $onCondition, $fields, $filter, $limit, $offset);
    }

    function rightJoin(
        string $joinTable,
        string $onCondition,
        string $fields = "*",
        string $filter = null,
        int $limit = null,
        int $offset = 0
    ): array {
        return $this->join('RIGHT', $joinTable, $onCondition, $fields, $filter, $limit, $offset);
    }

    function fullJoin(
        string $joinTable,
        string $onCondition,
        string $fields = "*",
        string $filter = null,
        int $limit = null,
        int $offset = 0
    ): array {
        return $this->join('FULL', $joinTable, $onCondition, $fields, $filter, $limit, $offset);
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

    function updateByFilter(string $filter, array $data): true
    {
        foreach ($data as $key => $value) {
            $type = gettype($value);
            $parsedValue = $type === "string" ? $this->makeValidSqlString($value) : ($type === "array" ? $this->makeValidSqlString(json_encode($value, true)) : $value);
            SQL::run("UPDATE `{$this->tableName}` SET $key = $parsedValue WHERE $filter");
        }

        $ti = UnixTime::getCurrentTimeByMilliseconds();

        SQL::run("UPDATE `{$this->tableName}` SET updated = $ti WHERE $filter");
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
