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
    
    // Requête directe avec vérification de l'autorisation
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        error_log("Utilisateur trouvé: " . $user['email']);
        error_log("Hash DB: " . $user['password']);
        error_log("Autorisation: " . $user['autorisation']);
        
        // Vérifier d'abord si le compte est autorisé
        if ($user['autorisation'] != 1) {
            error_log(" COMPTE BLOQUÉ - autorisation = " . $user['autorisation']);
            $error = "Votre compte est bloqué. Contactez l'administrateur.";
        }
        // Vérifier le mot de passe seulement si le compte est autorisé
        elseif (password_verify($password, $user['password'])) {
            error_log(" CONNEXION RÉUSSIE");
            
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            // Redirection
            header("Location: index.php");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="text-success fw-bold">SmartStudy+</h2>
                            <p class="text-muted">Connexion à votre espace</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert <?php echo strpos($error, 'bloqué') !== false ? 'alert-warning' : 'alert-danger'; ?>">
                                <i class="fas <?php echo strpos($error, 'bloqué') !== false ? 'fa-lock me-2' : 'fa-exclamation-triangle me-2'; ?>"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label text-muted">Email</label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label text-muted">Mot de passe</label>
                                <input type="password" name="password" id="password" class="form-control" 
                                       placeholder="Votre mot de passe" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2 mt-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-1">Pas de compte ? <a href="inscrire.php" class="text-success text-decoration-none fw-bold">S'inscrire</a></p>
                            <p class="text-muted mb-1"> <a href="forget-password.php" class="text-success text-decoration-none fw-bold">forget password</a></p>
                            <small class="text-muted">
                                <strong>Test:</strong> admin@smartstudy.com / admin123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>