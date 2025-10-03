<?php

namespace core;
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $config = require __DIR__ . '/../config/config.php';

        try {
            $this->connection = new \PDO ('mysql:host=mysql-mmnotes.alwaysdata.net;dbname=mmnotes_programation_web;charset=utf8','mmnotes','^E&I8KFKD%mF7k');
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
            //echo "Connexion réussie !";
        }catch (\PDOexception $e){
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
