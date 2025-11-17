<?php
require_once "config/Database.php";

class Formation {

    public static function getByCategory($cat_id) {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM formation WHERE categorie_id = ?");
        $stmt->execute([$cat_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}