<?php

namespace core;
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../config/config.php';

        try {
            $this->connection = new \PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8', $config['db_user'], $config['db_pass']);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        } catch (\PDOexception $e){
            die("Echec de la connexion" . $e->getMessage());
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
