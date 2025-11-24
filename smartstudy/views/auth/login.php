<?php
// Get error message from URL
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Connexion | SmartStudy+</title>
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
    .login-container {
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      padding: 3rem;
      max-width: 450px;
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
    .input-icon input {
      width: 100%;
      padding: 0.8rem 1rem 0.8rem 2.8rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      transition: 0.3s;
    }
    .input-icon input:focus {
      outline: none;
      border-color: var(--green);
    }
    .btn-login {
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
    .btn-login:hover {
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
    .signup-link {
      margin-top: 1.5rem;
      text-align: center;
    }
    .signup-link a {
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
  </style>
</head>
<body>
  <div class="login-container">
    <a href="index.php?controller=user&action=home" class="logo">SmartStudy+</a>
    <p class="subtitle">Connectez-vous à votre compte</p>

    <div id="error-message">
      <i class="fas fa-exclamation-circle"></i> Email ou mot de passe incorrect
    </div>
    
    <div style="background: var(--light); padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; color: #555;">
      <strong>Admin:</strong> admin@smartstudy.com / admin123
    </div>

    <form action="index.php?controller=auth&action=login" method="post">
      <div class="form-group">
        <label for="email">Email ou Nom d'utilisateur</label>
        <div class="input-icon">
          <i class="fas fa-user"></i>
          <input type="text" id="email" name="email" placeholder="votre@email.com" required autofocus>
        </div>
      </div>

      <div class="form-group">
        <label for="password">Mot de passe</label>
        <div class="input-icon">
          <i class="fas fa-lock"></i>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>
      </div>

      <button type="submit" class="btn-login">
        <i class="fas fa-sign-in-alt"></i> Se connecter
      </button>
    </form>

    <div class="signup-link">
      <p>Vous n'avez pas de compte ? <a href="index.php?controller=auth&action=signup"><i class="fas fa-user-plus"></i> S'inscrire</a></p>
    </div>

    <div class="back-link">
      <a href="index.php?controller=user&action=home">
        <i class="fas fa-arrow-left"></i> Retour à l'accueil
      </a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

