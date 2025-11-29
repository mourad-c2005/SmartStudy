<?php
session_start();
// Vérifier si l'utilisateur est connecté, sinon rediriger vers login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartStudy+ | Apprentissage Durable</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="css/plan.css">
</head>
<body>

  <!-- Top Navigation -->
  <nav class="top-nav">
    <a href="index.php" class="logo">SmartStudy+</a>
    <div class="nav-menu">
      <a href="index.php" class="active">Accueil</a>
      <a href="mesplans.php">Mes Plans</a>
      <a href="planning.php">Planning</a>
      <a href="groupes.php">Groupes</a>
      <a href="progres.php">Progrès</a>
    </div>
    <div class="user-section">
      <div class="user-info">
        <a href="profile.php" class="profile-link">
          <div class="name"><?php echo $_SESSION['user']['nom']; ?></div>
          <div class="role"><?php echo ucfirst($_SESSION['user']['role']); ?></div>
        </a>
      </div>
      <a href="profile.php">
        <img src="https://via.placeholder.com/45?text=<?php echo substr($_SESSION['user']['nom'], 0, 1); ?>" alt="Photo" class="user-photo">
      </a>
      <?php
      // Vérifier si l'utilisateur est admin
      if ($_SESSION['user']['role'] === 'admin') {
          echo '<a href="back_office/index.php" class="admin-btn">
                  <i class="fas fa-cog"></i> Administration
                </a>';
      }
      ?>
      <a href="login.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Se déconnecter
      </a>
    </div>
  </nav>

  <!-- Accueil -->
  <div class="main-content">
    <div class="welcome-card">
      <h1>Bienvenue sur SmartStudy+</h1>
      <p>
        <strong>Apprentissage durable. Motivation garantie.</strong><br>
        Planifiez, collaborez, progressez — 100 % digital, écologique, intelligent.
      </p>
      <a href="mesplans.php" class="btn-start">Commencer maintenant</a>
    </div>

    <div class="row g-4">
      <div class="col-md-3">
        <a href="mesplans.php" class="text-decoration-none">
          <div class="module-card">
            <i class="fas fa-brain fa-3x"></i>
            <h5>Mes Plans</h5>
            <p>Plans personnalisés selon vos examens</p>
          </div>
        </a>
      </div>
      <div class="col-md-3">
        <a href="planning.php" class="text-decoration-none">
          <div class="module-card">
            <i class="fas fa-calendar-check fa-3x"></i>
            <h5>Planning</h5>
            <p>Organisation quotidienne</p>
          </div>
        </a>
      </div>
      <div class="col-md-3">
        <a href="groupes.php" class="text-decoration-none">
          <div class="module-card">
            <i class="fas fa-users fa-3x"></i>
            <h5>Groupes</h5>
            <p>Apprentissage collaboratif</p>
          </div>
        </a>
      </div>
      <div class="col-md-3">
        <a href="progres.php" class="text-decoration-none">
          <div class="module-card">
            <i class="fas fa-trophy fa-3x"></i>
            <h5>Progrès</h5>
            <p>Stats, badges, motivation +30 %</p>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- Rapport Button (Bottom Right) -->
  <a href="rapport.php" class="rapport-btn" target="_blank">
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