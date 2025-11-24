<?php
/**
 * AuthController - Gère l'authentification (login, signup, logout)
 */

require_once __DIR__ . "/../config/Database.php";

class AuthController {

    public function showLogin() {
        require __DIR__ . "/../views/auth/login.php";
    }

    public function showSignup() {
        require __DIR__ . "/../views/auth/signup.php";
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                header('Location: index.php?controller=auth&action=login&error=1');
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
                        $_SESSION['user_id'] = 1;
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
            header('Location: index.php?controller=auth&action=login&error=1');
            exit;
        } else {
            $this->showLogin();
        }
    }

    public function signup() {
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
                        $errors[] = "La table 'users' n'existe pas. Veuillez exécuter le script de création des tables.";
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
                    $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
                }
            }
            
            // If errors, redirect back with error message
            $errorMsg = urlencode(implode(' ', $errors));
            header('Location: index.php?controller=auth&action=signup&error=' . $errorMsg);
            exit;
        } else {
            $this->showSignup();
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?controller=user&action=home');
        exit;
    }
}

