<?php
// controller/UserController.php
require_once '../model/User.php';

header('Content-Type: application/json');
session_start();

// Vérifie admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(array('error' => 'Accès refusé'));
    exit;
}

// Connexion DB
try {
    $pdo = new PDO("mysql:host=localhost;dbname=smartstudy;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('error' => 'Connexion DB échouée'));
    exit;
}

$userModel = new User($pdo);
$users = $userModel->all();

echo json_encode($users);