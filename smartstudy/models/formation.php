<?php
require_once __DIR__ . "/../config/Database.php";

class Formation {

    public static function getAll() {
        $pdo = Database::connect();
        return $pdo->query("SELECT * FROM formation ORDER BY id_formation DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByCategory($idCategorie) {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM formation WHERE id_categorie = ?");
        $stmt->execute([$idCategorie]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM formation WHERE id_formation = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
