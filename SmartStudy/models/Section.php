<?php
require_once "config/Database.php";

class Section {

    public static function getAll() {
        $pdo = Database::connect();
        return $pdo->query("SELECT * FROM section")->fetchAll(PDO::FETCH_ASSOC);
    }
}
