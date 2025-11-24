<?php
/**
 * PanierController - Gère le panier utilisateur
 */

require_once __DIR__ . "/../models/Panier.php";
require_once __DIR__ . "/../models/formation.php";

class PanierController {

    public function add() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id <= 0) {
            header('Location: index.php?controller=user&action=formations');
            exit;
        }

        if (Panier::addFormation($id)) {
            header("Location: index.php?controller=panier&action=show&success=added");
        } else {
            header("Location: index.php?controller=panier&action=show&error=already_in_cart");
        }
        exit;
    }

    public function show() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Initialize variables
        $items = [];
        $total = 0;
        $success = '';
        $error = '';

        // Get cart items
        $items = Panier::getItems();
        
        // Debug: Log items count
        error_log("PanierController::show - Items count: " . count($items));
        error_log("PanierController::show - User ID: " . $_SESSION['user_id']);
        
        // Calculate total
        foreach ($items as $item) {
            if (isset($item['prix']) && is_numeric($item['prix'])) {
                $total += floatval($item['prix']);
            }
        }

        // Handle messages
        $success = isset($_GET['success']) ? $_GET['success'] : '';
        $error = isset($_GET['error']) ? $_GET['error'] : '';

        require __DIR__ . "/../views/user/panier.php";
    }

    public function remove() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $formationId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($formationId > 0) {
            Panier::removeFormation($formationId);
        }

        header('Location: index.php?controller=panier&action=show');
        exit;
    }

    public function clear() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        Panier::clear();
        header('Location: index.php?controller=panier&action=show');
        exit;
    }

    public function pay() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        Panier::clear();
        require __DIR__ . "/../views/user/pay_success.php";
    }
}
