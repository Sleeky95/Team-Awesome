<?php
class DatabaseConnector {
    private static $dbConnection = null;
    protected function __construct() {
    }

    public static function getConnection() {
        $cls = static::class;
        if (!isset(self::$dbConnection)) {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');


        try {
            self::$dbConnection = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        return self::$dbConnection;
        }
    }
    
}
