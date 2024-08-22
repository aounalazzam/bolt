<?php

namespace Bolt\Lib\Database;

use Bolt\Utils\ServerErrorException;

class SQL
{
    static function run(string $sqlQuery, $expectedReturnOneItem = false): ?array
    {
        $conn = DatabaseConnection::getConnection();

        if (!$conn) {
            throw ServerErrorException::InternalServerError('Database connection failed');
        }

        $result = $conn->query($sqlQuery);
        $data = [];


        if (gettype($result) === "boolean") {
            if (!$result) {
                http_response_code(500);
            }

            return [];
        }

        try {
            if ($result->num_rows) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }

        if ($expectedReturnOneItem && count($data) === 0) {
            return null;
        }

        if ($expectedReturnOneItem && !(count($data) > 1) && isset($data[0])) {
            return $data[0];
        }

        if (count($data) === 0) {
            return [];
        }

        return $data;
    }

    static function lastInsertId(): int|string
    {
        $conn = DatabaseConnection::getConnection();
        return mysqli_insert_id($conn);
    }
}
