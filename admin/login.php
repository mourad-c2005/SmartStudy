<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Identifiants simples (tu peux les changer)
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
    <?php if (!empty($error)): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
      <input type="password" name="password" placeholder="Mot de passe" required><br>
      <button type="submit">Se connecter</button>
    </form>
  </div>
</body>
</html>
