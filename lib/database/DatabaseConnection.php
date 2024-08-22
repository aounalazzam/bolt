<?php

namespace Bolt\Lib\Database;

use Bolt\Utils\ServerErrorException;

class DatabaseConnection
{
    private static \mysqli|null $mySQLConnection = null;

    static function init(array $options)
    {
        [
            'host' => $servername,
            'username' => $username,
            'password' => $password,
            'database' => $dbname
        ] = $options;

        self::$mySQLConnection = mysqli_connect($servername, $username, $password, $dbname) ?? null;

        if (!self::$mySQLConnection) {
            throw ServerErrorException::InternalServerError("Connection failed: " . mysqli_connect_error());
        }
    }

    static function getConnection(): \mysqli|null
    {
        return self::$mySQLConnection;
    }
}
