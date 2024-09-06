<?php
class Database {
    private $pdo;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $host = 'localhost';
        $db   = 'blog_db';  // Vervang dit door de naam van je database
        $user = 'root';      // Vervang dit door je MySQL gebruikersnaam (standaard is het 'root' voor XAMPP)
        $pass = '';          // Vervang dit door je MySQL wachtwoord (standaard is het leeg voor XAMPP)
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }
}