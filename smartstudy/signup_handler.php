<?php
session_start();
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'etudiant';
    
    $errors = [];
    
    // Validation
    if (empty($prenom)) {
        $errors[] = "Le prénom est requis.";
    }
    if (empty($nom)) {
        $errors[] = "Le nom est requis.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Un email valide est requis.";
    }
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    if (!in_array($role, ['etudiant', 'enseignant', 'admin'])) {
        $role = 'etudiant';
    }
    
    if (empty($errors)) {
        try {
            $pdo = Database::connect();
            
            // Check if users table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
            
            if (!$tableExists) {
                $errors[] = "La table 'users' n'existe pas. Veuillez exécuter le script de création des tables : create_users_and_panier_tables.php";
            } else {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                
                if ($stmt->fetch()) {
                    $errors[] = "Cet email est déjà utilisé.";
                } else {
                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert user
                    $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, password, role) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$prenom, $nom, $email, $hashedPassword, $role]);
                    
                    // Get user ID
                    $userId = $pdo->lastInsertId();
                    
                    // Set session
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_role'] = $role;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $prenom . ' ' . $nom;
                    $_SESSION['user_prenom'] = $prenom;
                    $_SESSION['user_nom'] = $nom;
                    
                    // Redirect based on role
                    if ($role === 'admin') {
                        header('Location: index.php?controller=admin&action=dashboard');
                    } else {
                        header('Location: index.php?controller=user&action=home');
                    }
                    exit;
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage() . " (Code: " . $e->getCode() . ")";
        }
    }
    
    // If errors, redirect back with error message
    $errorMsg = urlencode(implode(' ', $errors));
    header('Location: signup.html?error=' . $errorMsg);
    exit;
} else {
    header('Location: signup.html');
    exit;
}

