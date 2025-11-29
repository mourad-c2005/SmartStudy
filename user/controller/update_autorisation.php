<?php
// controller/update_autorisation.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_autorisation'])) {
    $userId = $_POST['user_id'] ?? null;
    $autorisation = $_POST['autorisation'] ?? null;
    
    if ($userId && $autorisation !== null) {
        $userModel = new User($pdo);
        $success = $userModel->setAutorisation($userId, $autorisation);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Autorisation mise à jour']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requête invalide']);
