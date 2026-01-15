<?php

class Database
{

    public function connect()
    {
        $host = '127.0.0.1'; // or 'localhost'
        $db   = 'library_native';
        $user = 'root';      // username
        $pass = '';          // empty string by default
        $port = '3308';      // or '5432'
        $charset = 'utf8mb4';

        // Data Source Name (DSN) string
        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";
        // For PostgreSQL, the DSN would be:
        // $dsn = "pgsql:host=$host;dbname=$db;port=$port";


        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Default fetch mode to associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulated prepared statements
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
            return $pdo; // Return the PDO connection object
        } catch (\PDOException $e) {
            // Handle connection error
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
