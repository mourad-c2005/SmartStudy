<?php
session_start();
require_once 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        header('Location: login.html?error=1');
        exit;
    }
    
    try {
        $pdo = Database::connect();
        
        // Check if users table exists
        $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
        
        if ($tableExists) {
            // Get user from database
            $stmt = $pdo->prepare("SELECT id, prenom, nom, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_nom'] = $user['nom'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: index.php?controller=admin&action=dashboard');
                } else {
                    header('Location: index.php?controller=user&action=home');
                }
                exit;
            }
        } else {
            // Fallback: Simple demo authentication if table doesn't exist
            $admin_email = 'admin@smartstudy.com';
            $admin_password = 'admin123';
            
            if ($email === $admin_email && $password === $admin_password) {
                $_SESSION['user_role'] = 'admin';
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = 'Administrateur';
                $_SESSION['user_id'] = 1; // Temporary admin ID
                header('Location: index.php?controller=admin&action=dashboard');
                exit;
            }
        }
    } catch (PDOException $e) {
        // Fallback on error
        $admin_email = 'admin@smartstudy.com';
        $admin_password = 'admin123';
        
        if ($email === $admin_email && $password === $admin_password) {
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = 'Administrateur';
            $_SESSION['user_id'] = 1;
            header('Location: index.php?controller=admin&action=dashboard');
            exit;
        }
    }
    
    // Invalid credentials
    header('Location: login.html?error=1');
    exit;
} else {
    header('Location: login.html');
    exit;
}

