<?php
class User {
    public function getAll() {
        $db = Database::getInstance()->getConnection();
        $result = $db->query("SELECT * FROM users");

        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }
}
