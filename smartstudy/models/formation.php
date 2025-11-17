<?php
require_once "config/Database.php";

class Formation {

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
