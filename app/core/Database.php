<?php

namespace core;
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../config/config.php';

        // New mysqli connexion
        $this->connection = new \mysqli(
            $config['db_host'],
            $config['db_user'],
            $config['db_pass'],
            $config['db_name']
        );

        // if connexion error -> display error message
        if ($this->connection->connect_error) {
            die("Erreur de connexion MySQLi: " . $this->connection->connect_error);
        }
    }

    // Get database instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Get database connexion
    public function getConnection() {
        return $this->connection;
    }
}
