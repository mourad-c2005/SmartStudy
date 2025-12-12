<?php
// inscrire.php

session_start();

// Include database configuration
require_once '../config/database.php';
require_once '../model/User.php';

// Vérifiez le chemin de mailer.php
$mailer_path = dirname(__DIR__) . '/lib/mailer.php';
if (file_exists($mailer_path)) {
    require_once $mailer_path;
} else {
    // Chemin alternatif
    $mailer_path = __DIR__ . '/../lib/mailer.php';
    if (file_exists($mailer_path)) {
        require_once $mailer_path;
    } else {
        die("Fichier mailer.php non trouvé. Cherché à: " . $mailer_path);
    }
}

// $pdo est déjà disponible depuis database.php
$userModel = new User($pdo);

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit;
}

// Gérer la soumission du formulaire
$alert_message = '';
$alert_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $role = $_POST['role'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $etablissement = $_POST['etablissement'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $twitter = trim($_POST['twitter'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $github = trim($_POST['github'] ?? '');

    // Validation
    if (empty($nom) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        $alert_message = 'Veuillez remplir tous les champs obligatoires';
        $alert_type = 'error';
    } elseif ($password !== $confirmPassword) {
        $alert_message = 'Les mots de passe ne correspondent pas';
        $alert_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alert_message = 'Veuillez entrer un email valide';
        $alert_type = 'error';
    } elseif ($userModel->emailExists($email)) {
        $alert_message = 'Cet email est déjà utilisé';
        $alert_type = 'error';
    } else {
        // Créer l'utilisateur
        $userData = [
            'nom' => $nom,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'date_naissance' => $date_naissance ?: null,
            'etablissement' => $etablissement ?: null,
            'niveau' => $niveau ?: null,
            'twitter' => $twitter ?: null,
            'linkedin' => $linkedin ?: null,
            'github' => $github ?: null
        ];

        $user = $userModel->create($userData);
        
        if ($user) {
            // Générer un code de vérification
            $verification_code = rand(100000, 999999);
            
            // Stocker le code dans la session
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['verification_email'] = $email;
            $_SESSION['temp_user_id'] = $user['id'];
            
            // Envoyer le code par email
            $subject = "Code de vérification SmartStudy+";
            $body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='utf-8'>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                        .code { font-size: 32px; font-weight: bold; color: #4CAF50; text-align: center; margin: 30px 0; padding: 15px; background: white; border-radius: 8px; border: 2px dashed #4CAF50; }
                        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>SmartStudy+</h1>
                            <p>Vérification de votre compte</p>
                        </div>
                        <div class='content'>
                            <h2>Bonjour $nom,</h2>
                            <p>Merci de vous être inscrit sur SmartStudy+.</p>
                            <p>Pour finaliser votre inscription, veuillez utiliser le code de vérification ci-dessous :</p>
                            <div class='code'>$verification_code</div>
                            <p>Ce code est valable pendant 15 minutes.</p>
                            <p>Si vous n'avez pas créé de compte sur SmartStudy+, veuillez ignorer cet email.</p>
                        </div>
                        <div class='footer'>
                            <p>© 2025 SmartStudy+. Tous droits réservés.</p>
                            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $altBody = "Code de vérification SmartStudy+\n\nBonjour $nom,\n\nVotre code de vérification est : $verification_code\n\nCe code est valable pendant 15 minutes.\n\nCordialement,\nL'équipe SmartStudy+";
            
            // Envoyer l'email
            if (function_exists('sendEmail') && sendEmail($email, $subject, $body, $altBody)) {
                // Rediriger vers la page de vérification
                header("Location: verifier.php");
                exit;
            } else {
                // Fallback: utiliser la fonction mail() si PHPMailer échoue
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=utf-8\r\n";
                $headers .= "From: SmartStudy+ <noreply@smartstudy.com>\r\n";
                
                if (mail($email, $subject, $body, $headers)) {
                    header("Location: verifier.php");
                    exit;
                } else {
                    $alert_message = "Erreur lors de l'envoi du code de vérification. Veuillez réessayer.";
                    $alert_type = 'error';
                    // Supprimer l'utilisateur créé
                    $userModel->delete($user['id']);
                }
            }
        } else {
            $alert_message = 'Erreur lors de l\'inscription';
            $alert_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartStudy+ | Inscription</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

   <link rel="stylesheet" type="text/css" href="css/inscrire.css">
</head>
<body>
  <header>
    <div class="logo">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM7 17H17V15H7V17ZM7 13H17V11H7V13ZM7 9H17V7H7V9Z"/>
        <path d="M5 21V19H3V21H5ZM21 19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19H5V21H19V19H21Z"/>
        <path d="M12 2L13.09 8.26L19 9L15.5 13.74L16.59 20L12 16.81L7.41 20L8.5 13.74L5 9L10.91 8.26L12 2Z"/>
      </svg>
      SmartStudy+
    </div>
  </header>

  <div class="main-container">
    <!-- LEFT: Social Links (Optional) -->
    <div class="sidebar">
      <h3>Réseaux sociaux (Optionnel)</h3>
      <div class="social-group">
        <label for="twitter">Twitter</label>
        <input type="url" id="twitter" placeholder="https://twitter.com/...">
      </div>
      <div class="social-group">
        <label for="linkedin">LinkedIn</label>
        <input type="url" id="linkedin" placeholder="https://linkedin.com/...">
      </div>
      <div class="social-group">
        <label for="github">GitHub</label>
        <input type="url" id="github" placeholder="https://github.com/...">
      </div>
    </div>

    <!-- CENTER: Registration Form -->
    <div class="card" id="formCard">
      <h2>Inscription</h2>
      <p>Rejoignez une communauté d'apprentissage sereine</p>

      <div id="alert-container">
        <?php if ($alert_message): ?>
          <div class="alert alert-<?php echo $alert_type; ?>">
            <i class="fas fa-<?php echo $alert_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i> 
            <?php echo htmlspecialchars($alert_message); ?>
          </div>
        <?php endif; ?>
      </div>

      <form id="inscriptionForm" method="POST" action="">
        <input type="text" id="nom" name="nom" placeholder="Nom complet" value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
        <div class="error-msg" id="nomError"><i class="fas fa-exclamation-circle"></i> <span></span></div>

        <input type="email" id="email" name="email" placeholder="Email (ex: nom@gmail.com)" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <div class="error-msg" id="emailError"><i class="fas fa-exclamation-circle"></i> <span></span></div>

        <input type="password" id="password" name="password" placeholder="Mot de passe">
        <div class="error-msg" id="passwordError"><i class="fas fa-exclamation-circle"></i> <span></span></div>
        
        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirmer le mot de passe">
        <div class="error-msg" id="confirmPasswordError"><i class="fas fa-exclamation-circle"></i> <span></span></div>

        <input type="date" id="date_naissance" name="date_naissance" value="<?php echo isset($_POST['date_naissance']) ? htmlspecialchars($_POST['date_naissance']) : ''; ?>">
        <div class="error-msg" id="ageError"><i class="fas fa-exclamation-circle"></i> <span></span></div>

        <select id="etablissement" name="etablissement">
          <option value="" disabled selected>Établissement</option>
          <option value="lycee" <?php echo (isset($_POST['etablissement']) && $_POST['etablissement'] === 'lycee') ? 'selected' : ''; ?>>Lycée</option>
          <option value="universite" <?php echo (isset($_POST['etablissement']) && $_POST['etablissement'] === 'universite') ? 'selected' : ''; ?>>Université</option>
          <option value="ecole" <?php echo (isset($_POST['etablissement']) && $_POST['etablissement'] === 'ecole') ? 'selected' : ''; ?>>École d'ingénieurs</option>
          <option value="autre" <?php echo (isset($_POST['etablissement']) && $_POST['etablissement'] === 'autre') ? 'selected' : ''; ?>>Autre</option>
        </select>

        <select id="niveau" name="niveau">
          <option value="" disabled selected>Niveau / Grade</option>
          <option value="1ere" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] === '1ere') ? 'selected' : ''; ?>>1ère année</option>
          <option value="2eme" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] === '2eme') ? 'selected' : ''; ?>>2ème année</option>
          <option value="3eme" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] === '3eme') ? 'selected' : ''; ?>>3ème année</option>
          <option value="4eme" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] === '4eme') ? 'selected' : ''; ?>>4ème année</option>
          <option value="5eme" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] === '5eme') ? 'selected' : ''; ?>>5ème année</option>
          <option value="master" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] === 'master') ? 'selected' : ''; ?>>Master</option>
          <option value="doctorat" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] === 'doctorat') ? 'selected' : ''; ?>>Doctorat</option>
        </select>

        <select id="role" name="role">
          <option value="" disabled selected>Choisir un rôle</option>
          <option value="etudiant" <?php echo (isset($_POST['role']) && $_POST['role'] === 'etudiant') ? 'selected' : ''; ?>>Étudiant</option>
          <option value="professeur" <?php echo (isset($_POST['role']) && $_POST['role'] === 'professeur') ? 'selected' : ''; ?>>Professeur</option>
        </select>

        <!-- Hidden fields for social links -->
        <input type="hidden" id="twitter_hidden" name="twitter">
        <input type="hidden" id="linkedin_hidden" name="linkedin">
        <input type="hidden" id="github_hidden" name="github">

        <button type="submit">S'inscrire</button>
      </form>

      <div class="links">
        <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
      </div>
    </div>
  </div>

  <footer>
    <p>SmartStudy+ © 2025 — Nature • Croissance • Sérénité</p>
  </footer>

  <script>
    // Votre script JavaScript reste inchangé
    console.log("Validation JavaScript chargée !");

    const form = document.getElementById('inscriptionForm');
    if (!form) return console.error("Formulaire non trouvé");

    const getEl = (id) => document.getElementById(id);
    
    const inputs = {
        nom: getEl('nom'),
        email: getEl('email'),
        password: getEl('password'),
        confirmPassword: getEl('confirm-password'),
        dateNaissance: getEl('date_naissance'),
        etablissement: getEl('etablissement'),
        niveau: getEl('niveau'),
        role: getEl('role'),
        twitter: getEl('twitter'),
        linkedin: getEl('linkedin'),
        github: getEl('github')
    };

    const showError = (field, msg) => {
        const errorEl = document.getElementById(field + 'Error');
        if (errorEl && inputs[field]) {
            errorEl.querySelector('span').textContent = msg;
            errorEl.style.display = 'flex';
            inputs[field].classList.add('error');
        }
    };

    const hideError = (field) => {
        const errorEl = document.getElementById(field + 'Error');
        if (errorEl && inputs[field]) {
            errorEl.style.display = 'none';
            inputs[field].classList.remove('error');
        }
    };

    // Validation en temps réel
    inputs.nom.addEventListener('input', () => {
        const v = inputs.nom.value.trim();
        if (v && /\d/.test(v)) {
            showError('nom', 'Le nom ne doit pas contenir de chiffres');
        } else {
            hideError('nom');
        }
    });

    inputs.email.addEventListener('input', () => {
        const v = inputs.email.value.trim().toLowerCase();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (v && !emailRegex.test(v)) {
            showError('email', 'Veuillez entrer un email valide');
        } else {
            hideError('email');
        }
    });

    inputs.dateNaissance.addEventListener('change', () => {
        const birth = new Date(inputs.dateNaissance.value);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        
        if (inputs.dateNaissance.value && age < 13) {
            showError('age', 'Âge minimum : 13 ans');
        } else {
            hideError('age');
        }
    });

    inputs.password.addEventListener('input', () => {
        const password = inputs.password.value;
        if (password && password.length < 6) {
            showError('password', 'Le mot de passe doit contenir au moins 6 caractères');
        } else {
            hideError('password');
        }
    });

    inputs.confirmPassword.addEventListener('input', () => {
        const password = inputs.password.value;
        const confirmPassword = inputs.confirmPassword.value;
        if (confirmPassword && password !== confirmPassword) {
            showError('confirmPassword', 'Les mots de passe ne correspondent pas');
        } else {
            hideError('confirmPassword');
        }
    });

    // Validation avant soumission
    form.addEventListener('submit', e => {
        e.preventDefault();
        
        ['nom', 'email', 'age', 'password', 'confirmPassword'].forEach(hideError);

        let isValid = true;

        if (!inputs.nom.value.trim()) {
            showError('nom', 'Le nom complet est obligatoire');
            isValid = false;
        }

        if (!inputs.email.value.trim()) {
            showError('email', 'L\'email est obligatoire');
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputs.email.value.trim())) {
            showError('email', 'Veuillez entrer un email valide');
            isValid = false;
        }

        if (!inputs.password.value) {
            showError('password', 'Le mot de passe est obligatoire');
            isValid = false;
        } else if (inputs.password.value.length < 6) {
            showError('password', 'Le mot de passe doit contenir au moins 6 caractères');
            isValid = false;
        }

        if (!inputs.confirmPassword.value) {
            showError('confirmPassword', 'Veuillez confirmer le mot de passe');
            isValid = false;
        } else if (inputs.password.value !== inputs.confirmPassword.value) {
            showError('confirmPassword', 'Les mots de passe ne correspondent pas');
            isValid = false;
        }

        if (!inputs.role.value) {
            alert('Veuillez sélectionner un rôle');
            isValid = false;
        }

        if (inputs.dateNaissance.value) {
            const birth = new Date(inputs.dateNaissance.value);
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const m = today.getMonth() - birth.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
            
            if (age < 13) {
                showError('age', 'Âge minimum : 13 ans');
                isValid = false;
            }
        }

        if (isValid) {
            document.getElementById('twitter_hidden').value = inputs.twitter.value.trim();
            document.getElementById('linkedin_hidden').value = inputs.linkedin.value.trim();
            document.getElementById('github_hidden').value = inputs.github.value.trim();
            
            form.submit();
        }
    });
  </script>
</body>
</html>
