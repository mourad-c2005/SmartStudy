<?php
class Group {
    private $db;

    public function __construct() {
        require_once '../config/database.php';
        $this->db = $pdo;
    }

    public function create($name, $creator_id) {
        $stmt = $this->db->prepare("INSERT INTO groups (name, creator_id) VALUES (?, ?)");
        $stmt->execute([$name, $creator_id]);
        $group_id = $this->db->lastInsertId();
        $this->join($group_id, $creator_id);
        return $group_id;
    }

    public function join($group_id, $user_id) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)");
        return $stmt->execute([$group_id, $user_id]);
    }

    public function getMembers($group_id) {
        $stmt = $this->db->prepare("
            SELECT u.* FROM users u
            JOIN group_members gm ON u.id = gm.user_id
            WHERE gm.group_id = ?
        ");
        $stmt->execute([$group_id]);
        return $stmt->fetchAll();
    }
}