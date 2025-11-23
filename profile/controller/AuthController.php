<?php
// controller/AuthController.php

header('Content-Type: application/json');
session_start();

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'smartstudy');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit;
}

// Inclure le modèle User
require_once '../model/User.php';
$userModel = new User($pdo);

// Récupérer les données d'entrée
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Vérifier l'état de connexion
    echo json_encode([
        'success' => true,
        'logged' => isset($_SESSION['user']),
        'user' => $_SESSION['user'] ?? null
    ]);
    exit;
}

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if ($action === 'login') {
        // Connexion utilisateur
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        // Validation des champs
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email et mot de passe requis']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Format d\'email invalide']);
            exit;
        }

        // Tentative de connexion
        $user = $userModel->login($email, $password);

        if ($user) {
            // Connexion réussie
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Déterminer la redirection selon le rôle
            $redirect = determineRedirectUrl($user['role']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => $_SESSION['user'],
                'redirect' => $redirect
            ]);
        } else {
            // Échec de connexion
            echo json_encode([
                'success' => false, 
                'message' => 'Email ou mot de passe incorrect'
            ]);
        }
        exit;
    }

    if ($action === 'signup') {
        // Inscription utilisateur
        $data = [
            'nom' => trim($input['nom'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'password' => $input['password'] ?? '',
            'role' => $input['role'] ?? '',
            'date_naissance' => $input['date_naissance'] ?? null,
            'etablissement' => $input['etablissement'] ?? null,
            'niveau' => $input['niveau'] ?? null,
            'twitter' => trim($input['twitter'] ?? ''),
            'linkedin' => trim($input['linkedin'] ?? ''),
            'github' => trim($input['github'] ?? '')
        ];

        // Validation des données
        $validation = validateSignupData($data);
        if (!$validation['success']) {
            echo json_encode($validation);
            exit;
        }

        // Vérifier si l'email existe déjà
        if (emailExists($pdo, $data['email'])) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé']);
            exit;
        }

        // Créer l'utilisateur
        $user = $userModel->create($data);
        
        if ($user) {
            // Connexion automatique après inscription
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            $redirect = determineRedirectUrl($user['role']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Inscription réussie',
                'user' => $_SESSION['user'],
                'redirect' => $redirect
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du compte']);
        }
        exit;
    }

    if ($action === 'logout') {
        // Déconnexion
        session_destroy();
        echo json_encode([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
        exit;
    }

    // Action non reconnue
    echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
    exit;
}

// Méthode non autorisée
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);

// Fonctions utilitaires

/**
 * Détermine l'URL de redirection selon le rôle
 */
function determineRedirectUrl($role) {
    $baseUrl = '/smartstudy';
    
    switch ($role) {
        case 'admin':
            return $baseUrl . '/view/admin/index.html';
        case 'professeur':
        case 'etudiant':
        default:
            return $baseUrl . '/view/index.html';
    }
}

/**
 * Valide les données d'inscription
 */
function validateSignupData($data) {
    // Validation du nom
    if (empty($data['nom']) || preg_match('/\d/', $data['nom'])) {
        return ['success' => false, 'message' => 'Nom invalide'];
    }

    // Validation de l'email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Format d\'email invalide'];
    }

    // Validation du mot de passe
    if (empty($data['password']) || strlen($data['password']) < 6) {
        return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 6 caractères'];
    }

    // Validation du rôle
    if (empty($data['role']) || !in_array($data['role'], ['etudiant', 'professeur'])) {
        return ['success' => false, 'message' => 'Rôle invalide'];
    }

    // Validation de la date de naissance
    if (empty($data['date_naissance'])) {
        return ['success' => false, 'message' => 'Date de naissance requise'];
    }

    $age = date_diff(date_create($data['date_naissance']), date_create('today'))->y;
    if ($age < 13) {
        return ['success' => false, 'message' => 'Vous devez avoir au moins 13 ans'];
    }

    // Validation établissement et niveau
    if (empty($data['etablissement']) || empty($data['niveau'])) {
        return ['success' => false, 'message' => 'Établissement et niveau requis'];
    }

    return ['success' => true];
}

/**
 * Vérifie si un email existe déjà
 */
function emailExists($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    } catch (Exception $e) {
        error_log("Erreur vérification email: " . $e->getMessage());
        return false;
    }
}