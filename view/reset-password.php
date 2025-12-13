<?php
// reset-password.php - Clean version
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/User.php';

// Get token from URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

error_log("=== RESET PASSWORD PAGE ===");
error_log("Token from URL: " . $token);

if (empty($token)) {
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Erreur - SmartStudy</title>
        <style>
        
            body {
                font-family: 'Open Sans', sans-serif;
                background: #F8FFF8;
                color: #333;
                margin: 0;
                padding: 0;
            }
            .top-nav {
                background: #fff;
                padding: 16px 5%;
                box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
                overflow: hidden;
            }
            .logo {
                font-family: 'Montserrat', sans-serif;
                font-weight: 700;
                font-size: 1.6rem;
                color: #4CAF50;
                text-decoration: none;
                float: left;
                line-height: 42px;
            }
            .main {
                padding: 48px 5%;
                clear: both;
                min-height: calc(100vh - 200px);
            }
            .card {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
                padding: 40px;
                margin: 0 auto;
                max-width: 500px;
                text-align: center;
            }
            h2 {
                font-family: 'Montserrat', sans-serif;
                color: #f44336;
                margin-bottom: 20px;
            }
            .error-message {
                background: #ffebee;
                color: #c62828;
                padding: 16px;
                border-radius: 8px;
                margin: 20px 0;
                border: 1px solid #ffcdd2;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-weight: 600;
                margin: 10px 5px;
            }
            .btn:hover {
                background: #45a049;
            }
            footer {
                background: #222;
                color: #ccc;
                text-align: center;
                padding: 24px;
                font-size: 0.9rem;
                clear: both;
            }
            footer strong {
                color: #FFEB3B;
            }
              
</style>


        
    </head>
    <body>
        <nav class='top-nav'>
            <a href='index.php' class='logo'>SmartStudy</a>
        </nav>

        <div class='main'>
            <div class='card'>
                <h2>Erreur</h2>
                <div class='error-message'>
                    <strong>Token manquant</strong>
                    <p>Aucun token n'a été fourni dans l'URL.</p>
                </div>
                <div>
                    <a href='forgot-password.php' class='btn'>Demander un nouveau lien</a>
                    <a href='login.php' class='btn'>Retour à la connexion</a>
                </div>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 <strong>SmartStudy</strong>. Tous droits réservés.</p>
        </footer>
    </body>
    </html>";
    exit;
}

// Initialize user model and check token
try {
    $userModel = new User($pdo);
    $user = $userModel->findByResetToken($token);
    
    if (!$user) {
        error_log("Token validation failed for: " . $token);
        echo "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Erreur - SmartStudy</title>
            <style>  body {
                    font-family: 'Open Sans', sans-serif;
                    background: #F8FFF8;
                    color: #333;
                    margin: 0;
                    padding: 0;
                }
                .top-nav {
                    background: #fff;
                    padding: 16px 5%;
                    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
                    overflow: hidden;
                }
                .logo {
                    font-family: 'Montserrat', sans-serif;
                    font-weight: 700;
                    font-size: 1.6rem;
                    color: #4CAF50;
                    text-decoration: none;
                    float: left;
                    line-height: 42px;
                }
                .main {
                    padding: 48px 5%;
                    clear: both;
                    min-height: calc(100vh - 200px);
                }
                .card {
                    background: #fff;
                    border-radius: 14px;
                    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
                    padding: 40px;
                    margin: 0 auto;
                    max-width: 500px;
                    text-align: center;
                }
                h2 {
                    font-family: 'Montserrat', sans-serif;
                    color: #f44336;
                    margin-bottom: 20px;
                }
                .error-message {
                    background: #ffebee;
                    color: #c62828;
                    padding: 16px;
                    border-radius: 8px;
                    margin: 20px 0;
                    border: 1px solid #ffcdd2;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    background: #4CAF50;
                    color: white;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: 600;
                    margin: 10px 5px;
                }
                .btn:hover {
                    background: #45a049;
                }
                footer {
                    background: #222;
                    color: #ccc;
                    text-align: center;
                    padding: 24px;
                    font-size: 0.9rem;
                    clear: both;
                }
                footer strong {
                    color: #FFEB3B;
                }
</style>
        </head>
        <body>
            <nav class='top-nav'>
                <a href='index.php' class='logo'>SmartStudy</a>
            </nav>

            <div class='main'>
                <div class='card'>
                    <h2>Erreur</h2>
                    <div class='error-message'>
                        <strong>Token invalide ou expiré</strong>
                        <p>Le token n'existe pas ou a expiré.</p>
                    </div>
                    <div>
                        <a href='forgot-password.php' class='btn'>Demander un nouveau lien</a>
                        <a href='login.php' class='btn'>Retour à la connexion</a>
                    </div>
                </div>
            </div>

            <footer>
                <p>&copy; 2024 <strong>SmartStudy</strong>. Tous droits réservés.</p>
            </footer>
        </body>
        </html>";
        exit;
    }
    
    error_log("Token valid for user: " . $user['email']);
    
} catch (Exception $e) {
    error_log("Error in reset-password: " . $e->getMessage());
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Erreur - SmartStudy</title>
        <style> body {
                font-family: 'Open Sans', sans-serif;
                background: #F8FFF8;
                color: #333;
                margin: 0;
                padding: 0;
            }
            .top-nav {
                background: #fff;
                padding: 16px 5%;
                box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
                overflow: hidden;
            }
            .logo {
                font-family: 'Montserrat', sans-serif;
                font-weight: 700;
                font-size: 1.6rem;
                color: #4CAF50;
                text-decoration: none;
                float: left;
                line-height: 42px;
            }
            .main {
                padding: 48px 5%;
                clear: both;
                min-height: calc(100vh - 200px);
            }
            .card {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
                padding: 40px;
                margin: 0 auto;
                max-width: 500px;
                text-align: center;
            }
            h2 {
                font-family: 'Montserrat', sans-serif;
                color: #f44336;
                margin-bottom: 20px;
            }
            .error-message {
                background: #ffebee;
                color: #c62828;
                padding: 16px;
                border-radius: 8px;
                margin: 20px 0;
                border: 1px solid #ffcdd2;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-weight: 600;
                margin: 10px 5px;
            }
            .btn:hover {
                background: #45a049;
            }
            footer {
                background: #222;
                color: #ccc;
                text-align: center;
                padding: 24px;
                font-size: 0.9rem;
                clear: both;
            }
            footer strong {
                color: #FFEB3B;
            }
</style>
        
    </head>
    <body>
        <nav class='top-nav'>
            <a href='index.php' class='logo'>SmartStudy</a>
        </nav>

        <div class='main'>
            <div class='card'>
                <h2>Erreur système</h2>
                <div class='error-message'>
                    <p>Une erreur est survenue. Veuillez réessayer.</p>
                </div>
                <div>
                    <a href='forgot-password.php' class='btn'>Réessayer</a>
                    <a href='login.php' class='btn'>Retour à la connexion</a>
                </div>
            </div>
        </div>

        <footer>
            <p>&copy; 2024 <strong>SmartStudy</strong>. Tous droits réservés.</p>
        </footer>
    </body>
    </html>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - SmartStudy</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #F8FFF8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .top-nav {
            background: #fff;
            padding: 16px 5%;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
            overflow: hidden;
        }
        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: #4CAF50;
            text-decoration: none;
            float: left;
            line-height: 42px;
        }
        .main {
            padding: 48px 5%;
            clear: both;
            min-height: calc(100vh - 200px);
        }
        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            padding: 40px;
            margin: 0 auto;
            max-width: 450px;
        }
        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #4CAF50;
            margin-bottom: 24px;
            text-align: center;
            font-size: 1.8rem;
        }
        .user-info {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            border-left: 4px solid #4CAF50;
        }
        .user-info p {
            margin: 5px 0;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
        }
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background: #45a049;
        }
        .message {
            padding: 16px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .text-center {
            text-align: center;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        footer {
            background: #222;
            color: #ccc;
            text-align: center;
            padding: 24px;
            font-size: 0.9rem;
            clear: both;
        }
        footer strong {
            color: #FFEB3B;
        }
    </style>
</head>
<body>
    <nav class="top-nav">
        <a href="index.php" class="logo">SmartStudy</a>
    </nav>

    <div class="main">
        <div class="card">
            <h2>Réinitialiser votre mot de passe</h2>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="message success"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <div class="user-info">
                <p><strong>Bonjour <?php echo htmlspecialchars($user['nom']); ?>!</strong></p>
                <p>Veuillez créer votre nouveau mot de passe ci-dessous :</p>
            </div>
            
            <form action="../controller/process-reset-password.php" method="POST" id="resetForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="password" class="form-label">Nouveau mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="6" placeholder="Minimum 6 caractères">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirmer le mot de passe :</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Retapez votre mot de passe">
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">Réinitialiser le mot de passe</button>
            </form>
            
            <div class="text-center">
                <a href="login.php" class="back-link">← Retour à la connexion</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 <strong>SmartStudy</strong>. Tous droits réservés.</p>
    </footer>

    <script>
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const submitBtn = document.getElementById('submitBtn');
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères.');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return;
            }
            
            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.textContent = 'Réinitialisation en cours...';
        });
    </script>
</body>
</html>