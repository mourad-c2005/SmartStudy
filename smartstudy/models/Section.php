<?php
require_once __DIR__ . "/../config/Database.php";

class Section {

    public static function getAll() {
        $pdo = Database::connect();
        return $pdo->query("SELECT * FROM sections")->fetchAll(PDO::FETCH_ASSOC);
    }
}
