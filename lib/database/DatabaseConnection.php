<?php

namespace Bolt\Lib\Database;

class DatabaseConnection
{
    static \mysqli $mySQLConnection;

    static function getConnection(): \mysqli
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
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }
    }
}
