<?php
// controller/AdminUserController.php
require_once '../model/User.php';

header('Content-Type: application/json');
session_start();

// Vérifie admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès refusé - Admin requis']);
    exit;
}

// Connexion DB
try {
    $pdo = new PDO("mysql:host=localhost;dbname=smartstudy;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Connexion DB échouée: ' . $e->getMessage()]);
    exit;
}

$userModel = new User($pdo);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Récupérer tous les utilisateurs
    try {
        $users = $userModel->all();
        echo json_encode(['success' => true, 'users' => $users]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }
    exit;
}

// Pour PUT et DELETE, on lit les données
if ($method === 'PUT' || $method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    
    if ($method === 'PUT') {
        $id = $data['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID utilisateur requis']);
            exit;
        }
        
        $updateData = [
            'nom' => trim($data['nom'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'role' => $data['role'] ?? ''
        ];

        $success = $userModel->update($id, $updateData);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Utilisateur modifié avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification']);
        }
    }
    
    if ($method === 'DELETE') {
        $id = $data['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID utilisateur requis']);
            exit;
        }

        $success = $userModel->delete($id);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
        }
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
