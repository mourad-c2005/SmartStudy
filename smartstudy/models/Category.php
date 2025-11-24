<?php
require_once __DIR__ . "/../config/Database.php";

class Category {

    public static function getBySection($section_id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM categorie WHERE section_id = ?");
        $stmt->execute([$section_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
