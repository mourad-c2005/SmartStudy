<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if ($user === 'admin' && $pass === '1234') {
        $_SESSION['admin_logged'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Identifiants incorrects";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - Connexion</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
  <div class="login-card">
    <h2>Connexion administrateur</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form id="loginForm" method="POST">
      <input type="text" id="username" name="username" placeholder="Nom d'utilisateur"><br>
      <input type="password" id="password" name="password" placeholder="Mot de passe"><br>

      <p id="formError" class="error" style="display:none;"></p>

      <button type="submit">Se connecter</button>
    </form>
  </div>

  <script>
      document.getElementById('loginForm').addEventListener('submit', function(e){
          const username = document.getElementById('username').value.trim();
          const password = document.getElementById('password').value.trim();
          const errorBox = document.getElementById('formError');

          errorBox.style.display = "none";
          errorBox.innerText = "";

          if(username === "" || password === ""){
              e.preventDefault();
              alert("Veuillez remplir tous les champs.");
              errorBox.style.display = "block";
          }
      });
  </script>
</body>
</html>
