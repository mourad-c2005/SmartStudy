<?php
session_start();

// Connexion DB directe (sans modèle pour test)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=smartstudy;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur DB: " . $e->getMessage());
}

$error = '';

if ($_POST) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // LOG pour debug
    error_log("=== TENTATIVE CONNEXION ===");
    error_log("Email: $email");
    error_log("Password: $password");
    
    // Requête directe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        error_log("Utilisateur trouvé: " . $user['email']);
        error_log("Hash DB: " . $user['password']);
        
        if (password_verify($password, $user['password'])) {
            error_log(" CONNEXION RÉUSSIE");
            
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            // Redirection
            if ($user['role'] === 'admin') {
                header("Location: index.php");
            } else {
                header("Location: index.php");
            }
            exit;
            
        } else {
            error_log(" MOT DE PASSE INCORRECT");
            $error = "Email ou mot de passe incorrect";
        }
    } else {
        error_log(" UTILISATEUR NON TROUVÉ");
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStudy+ | Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fbf8; min-height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 15px; box-shadow: 0 10px 30px rgba(76, 175, 80, 0.1); border-top: 5px solid #4CAF50; }
        .btn-success { background: #4CAF50; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-success">SmartStudy+</h2>
                            <p class="text-muted">Connexion à votre espace</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" 
                                       placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Mot de passe" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2">
                                Se connecter
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-1">Pas de compte ? <a href="inscrire.php" class="text-success">S'inscrire</a></p>
                            <small class="text-muted">
                                <strong>Test:</strong> admin@smartstudy.com / admin123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
