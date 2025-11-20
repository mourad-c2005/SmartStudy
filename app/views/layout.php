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
  <a class="logo" href="<?= URLROOT ?>">SmartStudy+</a>
  <div class="nav-menu">
    <a href="<?= URLROOT ?>" class="active">Accueil</a>
    <a href="<?= URLROOT ?>/Matiere/index">Matières</a>
  </div>
  <div class="user-section">
    <div class="user-info">
      <div class="name">Utilisateur</div>
      <div class="role">Étudiant</div>
    </div>
    <img src="https://via.placeholder.com/45?text=A" class="user-photo" alt="User">
  </div>
</nav>
<main class="main-content">
