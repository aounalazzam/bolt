<?php

class SQL
{
    static function run(string $sqlQuery, $expectedReturnOneItem = false): ?array
    {
        $conn = DatabaseConnection::getConnection();
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
        } catch (Throwable $th) {
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
        global $conn;
        return mysqli_insert_id($conn);
    }
}


class DatabaseConnection
{

    static mysqli $mySQLConnection;

    static function getConnection(): mysqli
    {
        return  self::$mySQLConnection;
    }
    static function init(array $options)
    {
        [
            'host' => $servername,
            'username' => $username,
            'password' => $password,
            'database' => $dbname
        ] = $options;

        try {
            self::$mySQLConnection = mysqli_connect($servername, $username, $password, $dbname);

            if (!self::$mySQLConnection) {
                error_log("Connection failed: " . mysqli_connect_error());
            }
        } catch (Throwable $th) {
            error_log($th->getMessage());
        }
    }
}
