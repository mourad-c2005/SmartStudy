<?php
// verifier.php
session_start();

require_once '../config/database.php';
require_once '../model/User.php';

$userModel = new User($pdo);

$message = '';
$message_type = 'error';

// Vérifier si l'utilisateur vient de l'inscription
if (!isset($_SESSION['verification_code']) || !isset($_SESSION['verification_email'])) {
    header("Location: inscrire.php");
    exit;
}

// Gérer la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_entre = trim($_POST['verification_code'] ?? '');
    $code_verification = $_SESSION['verification_code'];
    
    if (empty($code_entre)) {
        $message = 'Veuillez entrer le code de vérification';
    } elseif ($code_entre == $code_verification) {
        // Code correct - activer le compte
        $email = $_SESSION['verification_email'];
        $user_id = $_SESSION['temp_user_id'];
        
        // CORRECTION ICI: Utilisez find() au lieu de findById()
        $user = $userModel->find($user_id); // <-- Correction ici
        
        if ($user) {
            // Marquer l'utilisateur comme vérifié
            if (method_exists($userModel, 'verifyUser')) {
                $userModel->verifyUser($user_id);
            }
            
            // Supprimer les données temporaires
            unset($_SESSION['verification_code']);
            unset($_SESSION['verification_email']);
            unset($_SESSION['temp_user_id']);
            
            // Connecter l'utilisateur
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;
            
            // Message de succès
            $message = 'Votre compte a été vérifié avec succès ! Redirection en cours...';
            $message_type = 'success';
            
            // Redirection après 3 secondes
            header("refresh:3;url=profile.php");
        } else {
            $message = 'Erreur lors de la vérification du compte';
        }
    } else {
        $message = 'Code de vérification incorrect';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du compte - SmartStudy+</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo svg {
            width: 40px;
            height: 40px;
            fill: #3498db;
        }

        .verification-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .verification-icon {
            font-size: 60px;
            color: #3498db;
            margin-bottom: 20px;
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 28px;
        }

        .subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .email-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }

        .email-info i {
            color: #3498db;
            margin-right: 10px;
        }

        .verification-form {
            margin-top: 20px;
        }

        .verification-form input[type="text"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 18px;
            text-align: center;
            letter-spacing: 5px;
            transition: all 0.3s;
        }

        .verification-form input[type="text"]:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .verify-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 16px 40px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
            width: 100%;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        .verify-btn:hover {
            background: linear-gradient(135deg, #2980b9, #1c6ea4);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            font-size: 20px;
        }

        .resend-link {
            margin-top: 20px;
            color: #7f8c8d;
        }

        .resend-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }

        .resend-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            margin-top: 30px;
            text-align: center;
        }

        .back-link a {
            color: #7f8c8d;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-link a:hover {
            color: #3498db;
        }

        .countdown {
            font-size: 14px;
            color: #e74c3c;
            margin-top: 10px;
            font-weight: bold;
        }

        @media (max-width: 480px) {
            .verification-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM7 17H17V15H7V17ZM7 13H17V11H7V13ZM7 9H17V7H7V9Z"/>
            <path d="M5 21V19H3V21H5ZM21 19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19H5V21H19V19H21Z"/>
            <path d="M12 2L13.09 8.26L19 9L15.5 13.74L16.59 20L12 16.81L7.41 20L8.5 13.74L5 9L10.91 8.26L12 2Z"/>
        </svg>
        SmartStudy+
    </div>

    <div class="verification-card">
        <div class="verification-icon">
            <i class="fas fa-envelope-circle-check"></i>
        </div>
        
        <h1>Vérification du compte</h1>
        
        <p class="subtitle">
            Nous avons envoyé un code de vérification à 6 chiffres à l'adresse email suivante :
        </p>
        
        <div class="email-info">
            <i class="fas fa-envelope"></i>
            <strong><?php echo htmlspecialchars($_SESSION['verification_email']); ?></strong>
        </div>
        
        <p class="subtitle">
            Veuillez entrer le code reçu par email ci-dessous :
        </p>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($message_type !== 'success'): ?>
            <form method="POST" action="" class="verification-form">
                <input type="text" 
                       name="verification_code" 
                       placeholder="Entrez le code à 6 chiffres"
                       maxlength="6"
                       pattern="\d{6}"
                       required
                       autocomplete="off"
                       autofocus>
                
                <div class="countdown" id="countdown">
                    Le code expire dans: <span id="timer">15:00</span>
                </div>
                
                <button type="submit" class="verify-btn">
                    <i class="fas fa-check"></i> Vérifier le code
                </button>
            </form>
            
            <div class="resend-link">
                <p>Vous n'avez pas reçu le code ? 
                    <a href="#" onclick="resendCode()">Renvoyer le code</a>
                </p>
            </div>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="inscrire.php">
                <i class="fas fa-arrow-left"></i> Retour à l'inscription
            </a>
        </div>
    </div>

    <script>
        // Compte à rebours de 15 minutes
        function startTimer(duration, display) {
            let timer = duration, minutes, seconds;
            const interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    display.textContent = "Expiré";
                    document.querySelector('.resend-link').innerHTML = 
                        '<p style="color: #e74c3c;">Le code a expiré. <a href="#" onclick="resendCode()">Renvoyer un nouveau code</a></p>';
                }
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Démarrer le compte à rebours de 15 minutes (900 secondes)
            const fifteenMinutes = 60 * 15;
            const display = document.querySelector('#timer');
            if (display) {
                startTimer(fifteenMinutes, display);
            }

            // Auto-focus sur l'input
            const codeInput = document.querySelector('input[name="verification_code"]');
            if (codeInput) {
                codeInput.focus();
                
                // Format automatique pour n'accepter que des chiffres
                codeInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '').slice(0, 6);
                    
                    // Auto-soumettre si 6 chiffres sont entrés
                    if (this.value.length === 6) {
                        this.form.submit();
                    }
                });
            }
        });

        function resendCode() {
            if (confirm('Voulez-vous renvoyer le code de vérification ?')) {
                // Dans un cas réel, vous feriez un appel AJAX ici
                fetch('resend_code.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: '<?php echo $_SESSION['verification_email']; ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Un nouveau code a été envoyé à votre email.');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'envoi du code. Veuillez réessayer.');
                });
            }
        }
    </script>
</body>
</html>
