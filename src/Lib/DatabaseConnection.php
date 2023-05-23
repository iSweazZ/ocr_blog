<?php

namespace Application\Lib;

use PDO;

class DatabaseConnection
{
    private static ?PDO $database = null;

    private function __construct()
    {
    }

    public static function getConnection(): PDO
    {
        if (self::$database === null) {
            self::$database = new \PDO("{$_ENV['DB_PROTOCOL']}:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DBNAME']};charset={$_ENV['DB_CHARSET']}", $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        }

        return self::$database;
    }
}
