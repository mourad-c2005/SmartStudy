<?php
// app/views/layout.php
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SmartRevision+</title>
  <link rel="stylesheet" href="<?= URLROOT ?>/public/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <nav class="top-nav">
  <a href="view/index.php" class="logo">SmartStudy+</a>
  <div class="nav-menu">
    <a href="view/index.php" >Accueil</a>
    <a href="/smartstudy/index.php" class="active">Mes Plans</a>
    <a href="index.php?controller=Planning&action=index">Planning</a>
    <a href="index.php?controller=Groupes&action=index">Groupes</a>
    <a href="index.php?controller=Progress&action=index">Progrès</a>
    <a href="index.php?url=Chatbot/index">Chatbot</a>
    
  </div>
  <div class="user-section">
      <div class="user-info">
        <a href="view/profile.php" class="profile-link">
          <div class="name"><?php echo $_SESSION['user']['nom']; ?></div>
          <div class="role"><?php echo ucfirst($_SESSION['user']['role']); ?></div>
        </a>
      </div>
    <img src="https://via.placeholder.com/45?text=A" alt="Photo" class="user-photo">
    <a href="/smartstudy/view/login.php" class="logout-btn">
      <i class="fas fa-sign-out-alt"></i> Se déconnecter
    </a>
  </div>
</nav>

<main class="main-content">
