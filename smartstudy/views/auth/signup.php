<?php
// Get error message from URL
$error = isset($_GET['error']) ? urldecode($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inscription | SmartStudy+</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --green: #4CAF50;
      --yellow: #FFEB3B;
      --light: #E8F5E8;
      --white: #ffffff;
      --dark: #2e7d32;
    }
    * { box-sizing: border-box; }
    body {
      font-family: 'Open Sans', sans-serif;
      background: linear-gradient(135deg, var(--light) 0%, #d4edda 100%);
      color: #333;
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    .signup-container {
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      padding: 3rem;
      max-width: 500px;
      width: 100%;
      text-align: center;
    }
    .logo {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 2.5rem;
      color: var(--green);
      margin-bottom: 0.5rem;
      text-decoration: none;
    }
    .subtitle {
      color: #777;
      margin-bottom: 2rem;
      font-size: 1rem;
    }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }
    .form-group {
      margin-bottom: 1.5rem;
      text-align: left;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: #555;
      font-weight: 600;
    }
    .input-icon {
      position: relative;
    }
    .input-icon i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #999;
    }
    .input-icon input,
    .form-group select {
      width: 100%;
      padding: 0.8rem 1rem 0.8rem 2.8rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      transition: 0.3s;
    }
    .form-group select {
      padding-left: 1rem;
    }
    .input-icon input:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--green);
    }
    .btn-signup {
      width: 100%;
      background: var(--green);
      color: white;
      padding: 0.8rem;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn-signup:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }
    #error-message {
      display: <?= $error ? 'block' : 'none' ?>;
      background: #ffebee;
      color: #c62828;
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      border-left: 4px solid #c62828;
    }
    .login-link {
      margin-top: 1.5rem;
      text-align: center;
    }
    .login-link a {
      color: var(--green);
      text-decoration: none;
      font-weight: 600;
    }
    .back-link {
      margin-top: 1rem;
      text-align: center;
    }
    .back-link a {
      color: #777;
      text-decoration: none;
      font-size: 0.9rem;
    }
    @media (max-width: 576px) {
      .form-row {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="signup-container">
    <a href="index.php?controller=user&action=home" class="logo">SmartStudy+</a>
    <p class="subtitle">Créez votre compte gratuitement</p>

    <div id="error-message">
      <i class="fas fa-exclamation-circle"></i> <span id="error-text"><?= htmlspecialchars($error) ?></span>
    </div>

    <form action="index.php?controller=auth&action=signup" method="post">
      <div class="form-row">
        <div class="form-group">
          <label for="prenom">Prénom</label>
          <div class="input-icon">
            <i class="fas fa-user"></i>
            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" required>
          </div>
        </div>

        <div class="form-group">
          <label for="nom">Nom</label>
          <div class="input-icon">
            <i class="fas fa-user"></i>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" required>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <div class="input-icon">
          <i class="fas fa-envelope"></i>
          <input type="email" id="email" name="email" placeholder="votre@email.com" required>
        </div>
      </div>

      <div class="form-group">
        <label for="role">Je suis</label>
        <select id="role" name="role" required>
          <option value="">Sélectionnez votre rôle</option>
          <option value="etudiant">Étudiant</option>
          <option value="enseignant">Enseignant</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="form-group">
        <label for="password">Mot de passe</label>
        <div class="input-icon">
          <i class="fas fa-lock"></i>
          <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8">
        </div>
        <div style="font-size: 0.8rem; margin-top: 0.3rem; color: #777;">Minimum 8 caractères</div>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirmer le mot de passe</label>
        <div class="input-icon">
          <i class="fas fa-lock"></i>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
        </div>
      </div>

      <button type="submit" class="btn-signup">
        <i class="fas fa-user-plus"></i> Créer mon compte
      </button>
    </form>

    <div class="login-link">
      <p>Vous avez déjà un compte ? <a href="index.php?controller=auth&action=login"><i class="fas fa-sign-in-alt"></i> Se connecter</a></p>
    </div>

    <div class="back-link">
      <a href="index.php?controller=user&action=home">
        <i class="fas fa-arrow-left"></i> Retour à l'accueil
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Password confirmation validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      if (password !== confirmPassword) {
        e.preventDefault();
        const errorMessage = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        errorText.textContent = 'Les mots de passe ne correspondent pas !';
        errorMessage.style.display = 'block';
        return false;
      }
    });
  </script>
</body>
</html>

