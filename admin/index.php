<?php
session_start();
if (!isset($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}
include 'header.php';
?>
<h1>Tableau de bord - Administration</h1>
<p>Bienvenue, <strong>Admin</strong> 👋</p>

<div class="dashboard">
  <a href="../Matiere/index" class="card">
    <i class="fas fa-book"></i>
    <span>Matières</span>
  </a>
  <a href="../Chapitre/index/1" class="card">
    <i class="fas fa-list"></i>
    <span>Chapitres</span>
  </a>
  <a href="../Cours/index/1" class="card">
    <i class="fas fa-file-alt"></i>
    <span>Cours</span>
  </a>
  <a href="../Quiz/index/1" class="card">
    <i class="fas fa-question-circle"></i>
    <span>Quiz</span>
  </a>
</div>

<a href="logout.php" class="logout-btn">Se déconnecter</a>
<?php include 'footer.php'; ?>
