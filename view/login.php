<?php
session_start();

// Connexion DB directe (sans modèle pour test)
try {
    $pdo = new PDO("mysql:host=localhost;dbname=smartstudy;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Erreur DB: " . $e->getMessage());
}

// INITIALISER LA VARIABLE $error
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
    <style>
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: none;
            animation: fadeIn 0.3s ease-in;
        }
        
        .success-message {
            color: #198754;
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: none;
            animation: fadeIn 0.3s ease-in;
        }
        
        .input-error {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
        }
        
        .input-success {
            border-color: #198754 !important;
            background-color: #f8fff9;
        }
        
        .validation-icon {
            position: absolute;
            right: 12px;
            top: 40px;
            font-size: 0.9rem;
            display: none;
        }
        
        .fa-check-circle {
            color: #198754;
        }
        
        .fa-exclamation-circle {
            color: #dc3545;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .password-strength {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 0.25rem;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background: #dc3545; width: 33%; }
        .strength-medium { background: #ffc107; width: 66%; }
        .strength-strong { background: #198754; width: 100%; }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 40px;
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
        }
        
        .shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
        
        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-3px); }
            40%, 60% { transform: translateX(3px); }
        }
    </style>
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

                        <?php if (!empty($error)): ?>
                            <div class="alert <?php echo strpos($error, 'bloqué') !== false ? 'alert-warning' : 'alert-danger'; ?> alert-dismissible fade show">
                                <i class="fas <?php echo strpos($error, 'bloqué') !== false ? 'fa-lock me-2' : 'fa-exclamation-triangle me-2'; ?>"></i>
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="loginForm" novalidate>
                            <div class="form-group">
                                <label for="email" class="form-label text-muted">Email</label>
                                <input type="text" name="email" id="email" class="form-control" 
                                       placeholder="votre@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <i class="fas fa-check-circle validation-icon" id="email-success-icon"></i>
                                <i class="fas fa-exclamation-circle validation-icon" id="email-error-icon"></i>
                                <div class="error-message" id="email-error"></div>
                                <div class="success-message" id="email-success">Email valide ✓</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label text-muted">Mot de passe</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control" 
                                           placeholder="Votre mot de passe">
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <i class="fas fa-check-circle validation-icon" id="password-success-icon"></i>
                                <i class="fas fa-exclamation-circle validation-icon" id="password-error-icon"></i>
                                <div class="password-strength" id="password-strength">
                                    <div class="strength-meter" id="strength-meter"></div>
                                </div>
                                <div class="error-message" id="password-error"></div>
                                <div class="success-message" id="password-success">Mot de passe valide ✓</div>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 py-2 mt-3" id="submitBtn">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Éléments DOM
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailError = document.getElementById('email-error');
            const emailSuccess = document.getElementById('email-success');
            const passwordError = document.getElementById('password-error');
            const passwordSuccess = document.getElementById('password-success');
            const emailErrorIcon = document.getElementById('email-error-icon');
            const emailSuccessIcon = document.getElementById('email-success-icon');
            const passwordErrorIcon = document.getElementById('password-error-icon');
            const passwordSuccessIcon = document.getElementById('password-success-icon');
            const strengthMeter = document.getElementById('strength-meter');
            const submitBtn = document.getElementById('submitBtn');
            const togglePassword = document.getElementById('togglePassword');
            const passwordStrength = document.getElementById('password-strength');
            
            // Variables d'état
            let emailValid = false;
            let passwordValid = false;

            // Fonction de validation d'email
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Fonction pour vérifier la force du mot de passe
            function checkPasswordStrength(password) {
                let strength = 0;
                
                if (password.length >= 6) strength += 1;
                if (password.length >= 8) strength += 1;
                if (/[A-Z]/.test(password)) strength += 1;
                if (/[0-9]/.test(password)) strength += 1;
                if (/[^A-Za-z0-9]/.test(password)) strength += 1;
                
                return strength;
            }

            // Fonction pour afficher/masquer le mot de passe
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });

            // Validation de l'email
            function validateEmail() {
                const email = emailInput.value.trim();
                
                if (email === '') {
                    showError(emailInput, emailError, 'Veuillez saisir votre adresse email', emailErrorIcon);
                    hide(emailSuccess);
                    hide(emailSuccessIcon);
                    emailValid = false;
                } else if (!isValidEmail(email)) {
                    showError(emailInput, emailError, 'Veuillez saisir une adresse email valide', emailErrorIcon);
                    hide(emailSuccess);
                    hide(emailSuccessIcon);
                    emailValid = false;
                } else {
                    showSuccess(emailInput, emailSuccess, emailSuccessIcon);
                    hide(emailError);
                    hide(emailErrorIcon);
                    emailValid = true;
                }
                updateSubmitButton();
            }

            // Validation du mot de passe
            function validatePassword() {
                const password = passwordInput.value.trim();
                const strength = checkPasswordStrength(password);
                
                // Mettre à jour la barre de force
                if (password.length > 0) {
                    passwordStrength.style.display = 'block';
                    strengthMeter.className = 'strength-meter';
                    
                    if (strength < 2) {
                        strengthMeter.classList.add('strength-weak');
                    } else if (strength < 4) {
                        strengthMeter.classList.add('strength-medium');
                    } else {
                        strengthMeter.classList.add('strength-strong');
                    }
                } else {
                    passwordStrength.style.display = 'none';
                }
                
                if (password === '') {
                    showError(passwordInput, passwordError, 'Veuillez saisir votre mot de passe', passwordErrorIcon);
                    hide(passwordSuccess);
                    hide(passwordSuccessIcon);
                    passwordValid = false;
                } else if (password.length < 6) {
                    showError(passwordInput, passwordError, 'Le mot de passe doit contenir au moins 6 caractères', passwordErrorIcon);
                    hide(passwordSuccess);
                    hide(passwordSuccessIcon);
                    passwordValid = false;
                } else {
                    showSuccess(passwordInput, passwordSuccess, passwordSuccessIcon);
                    hide(passwordError);
                    hide(passwordErrorIcon);
                    passwordValid = true;
                }
                updateSubmitButton();
            }

            // Fonctions d'aide
            function showError(input, errorElement, message, errorIcon) {
                input.classList.remove('input-success');
                input.classList.add('input-error');
                errorElement.textContent = message;
                show(errorElement);
                show(errorIcon);
            }

            function showSuccess(input, successElement, successIcon) {
                input.classList.remove('input-error');
                input.classList.add('input-success');
                show(successElement);
                show(successIcon);
            }

            function show(element) {
                element.style.display = 'block';
            }

            function hide(element) {
                element.style.display = 'none';
            }

            function updateSubmitButton() {
                if (emailValid && passwordValid) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('btn-secondary');
                    submitBtn.classList.add('btn-success');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-secondary');
                }
            }

            // Événements
            emailInput.addEventListener('input', validateEmail);
            emailInput.addEventListener('blur', validateEmail);
            
            passwordInput.addEventListener('input', validatePassword);
            passwordInput.addEventListener('blur', validatePassword);

            // Soumission du formulaire
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                validateEmail();
                validatePassword();
                
                if (emailValid && passwordValid) {
                    // Animation du bouton
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Connexion en cours...';
                    submitBtn.classList.add('disabled');
                    
                    // Ajouter un délai pour l'animation
                    setTimeout(() => {
                        form.submit();
                    }, 800);
                } else {
                    // Shake animation pour les champs invalides
                    if (!emailValid) {
                        emailInput.classList.add('shake');
                        setTimeout(() => emailInput.classList.remove('shake'), 500);
                    }
                    if (!passwordValid) {
                        passwordInput.classList.add('shake');
                        setTimeout(() => passwordInput.classList.remove('shake'), 500);
                    }
                }
            });

            // Validation initiale
            validateEmail();
            validatePassword();
        });
    </script>
</body>
</html>