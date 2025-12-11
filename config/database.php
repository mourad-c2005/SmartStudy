<?php
// config/Database.php

class Database
{
    /** @var PDO|null */
    private static $pdo = null;

    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            $host = '127.0.0.1';
            $dbname = 'smartstudy';
            $user = 'root';
            $pass = '';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$pdo;
    }
}
