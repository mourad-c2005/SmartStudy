<?php
session_start();

require_once '../config/database.php';
require_once '../model/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view/reset-password.php?error=Méthode non autorisée");
    exit;
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($token) || empty($password) || empty($confirm_password)) {
    header("Location: ../view/reset-password.php?token=" . urlencode($token) . "&error=Tous les champs sont requis");
    exit;
}

if ($password !== $confirm_password) {
    header("Location: ../view/reset-password.php?token=" . urlencode($token) . "&error=Les mots de passe ne correspondent pas");
    exit;
}

if (strlen($password) < 6) {
    header("Location: ../view/reset-password.php?token=" . urlencode($token) . "&error=Le mot de passe doit contenir au moins 6 caractères");
    exit;
}

try {
    $userModel = new User($pdo);
    $success = $userModel->resetPasswordWithToken($token, $password);
    
    if ($success) {
        header("Location: ../view/login.php?message=Mot de passe réinitialisé avec succès");
        exit;
    } else {
        header("Location: ../view/reset-password.php?token=" . urlencode($token) . "&error=Token invalide ou expiré");
        exit;
    }
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    header("Location: ../view/reset-password.php?token=" . urlencode($token) . "&error=Erreur système");
    exit;
}
