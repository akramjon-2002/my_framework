<?php

namespace Core;

use PDO;

class Database {
    private static ?PDO $connection = null;

    public static function connect(): PDO {
        if (self::$connection === null) {
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s;', getenv('DB_HOST'), getenv('DB_PORT'), getenv('DB_NAME'));
            self::$connection = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASSWORD'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$connection;
    }
}
