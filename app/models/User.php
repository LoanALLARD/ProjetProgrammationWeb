<?php
class User {
    public function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
