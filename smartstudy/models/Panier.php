<?php
session_start();

class Panier {

    public static function addFormation($id) {
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }
        $_SESSION['panier'][] = $id;
    }

    public static function getItems() {
        if (!isset($_SESSION['panier'])) return [];

        require_once "models/Formation.php";

        $items = [];
        foreach ($_SESSION['panier'] as $id) {
            $items[] = Formation::getById($id);
        }
        return $items;
    }

    public static function clear() {
        $_SESSION['panier'] = [];
    }
}
