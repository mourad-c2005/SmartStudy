<?php
session_start();
// Vérifier si l'utilisateur est connecté et est admin, sinon rediriger
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartStudy+ | Admin</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/index.css">
 
</head>
<body>

  <!-- Top Navigation -->
  <nav class="top-nav">
    <div class="left-section">
      <a href="index.php" class="logo">SmartStudy+</a>
      <img src="https://via.placeholder.com/45?text=<?php echo substr($_SESSION['user']['nom'], 0, 1); ?>" alt="Photo Admin" class="user-photo">
      <div class="user-info">
        <div class="name"><?php echo $_SESSION['user']['nom']; ?></div>
        <div class="role">Administrateur</div>
      </div>
    </div>

    <div class="nav-menu">
      <a href="index.php" class="active">Dashboard</a>
      <a href="user.php">Utilisateurs</a>
      <a href="admin_groupes.php">Groupes</a>
      <a href="admin_stats.php">Statistiques</a>
      <a href="admin_export.php">Export</a>
    </div>

    <div>
      <a href="../index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Retour au site
      </a>
      <a href="../login.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Se déconnecter
      </a>
    </div>
  </nav>

 
 

  <!-- Admin Modules -->
  <div class="main-content">
    <div class="row g-4">
      <div class="col-md-4">
        <a href="users.php" class="text-decoration-none">
          <div class="admin-card">
            <i class="fas fa-users-cog"></i>
            <h5>Gestion Utilisateurs</h5>
            <p>Créer, modifier, supprimer, rôles & profils</p>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="admin_groupes.php" class="text-decoration-none">
          <div class="admin-card">
            <i class="fas fa-user-friends"></i>
            <h5>Gestion Groupes</h5>
            <p>Créer, superviser, membres & collaboration</p>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="admin_stats.php" class="text-decoration-none">
          <div class="admin-card">
            <i class="fas fa-chart-bar"></i>
            <h5>Statistiques Globales</h5>
            <p>Progrès, engagement, badges & tendances</p>
          </div>
        </a>
      </div>
    </div>

    <div class="row g-4 mt-3">
      <div class="col-md-6">
        <a href="admin_export.php" class="text-decoration-none">
          <div class="admin-card">
            <i class="fas fa-file-export"></i>
            <h5>Export PDF / CSV</h5>
            <p>Rapports complets, données utilisateurs</p>
          </div>
        </a>
      </div>
      <div class="col-md-6">
        <a href="admin_settings.php" class="text-decoration-none">
          <div class="admin-card">
            <i class="fas fa-cogs"></i>
            <h5>Paramètres Système</h5>
            <p>Configuration, sauvegarde, logs</p>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- Rapport Button -->
   <a href="rapports.php" class="rapport-btn" target="_blank">
    <i class="fas fa-file-pdf"></i> Rapport
  </a>

  <!-- Footer -->
  <footer>
    <p>SmartStudy+ © 2025 — Nature • Croissance • Sérénité</p>
    <p>Développé par <strong>BLUEPIXEL 2032</strong></p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>