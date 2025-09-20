<?php
class Database {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../config/config.php';
            self::$instance = new PDO(
                "mysql:host=".$config['db_host'].";dbname=".$config['db_name'],
                $config['db_user'],
                $config['db_pass']
            );
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
