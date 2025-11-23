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
      background: var(--light);
      color: #333;
      margin: 0;
      padding-bottom: 60px;
    }

    /* Top Navigation */
    .top-nav {
      background: var(--white);
      padding: 1rem 5%;
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--green);
      text-decoration: none;
    }
    .nav-menu {
      display: flex;
      gap: 1.5rem;
    }
    .nav-menu a {
      color: #555;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
      padding: 0.5rem 1rem;
      border-radius: 30px;
      transition: 0.3s;
    }
    .nav-menu a:hover, .nav-menu a.active {
      background: var(--light);
      color: var(--green);
    }

    /* User Section (Right Top) */
    .user-section {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .user-info {
      text-align: right;
    }
    .user-info .profile-link {
      text-decoration: none;
      color: inherit;
      transition: 0.3s;
      padding: 0.5rem;
      border-radius: 8px;
    }
    .user-info .profile-link:hover {
      background: var(--light);
      transform: translateY(-1px);
    }
    .user-info .name {
      font-weight: 600;
      color: #333;
      margin-bottom: 0.2rem;
    }
    .user-info .role {
      font-size: 0.85rem;
      color: #777;
    }
    .user-photo {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--green);
      cursor: pointer;
      transition: 0.3s;
    }
    .user-photo:hover {
      transform: scale(1.05);
      border-color: var(--dark);
    }
    .logout-btn {
      background: var(--yellow);
      color: #333;
      padding: 0.5rem 1rem;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.3s;
    }
    .logout-btn:hover {
      background: #fdd835;
      transform: translateY(-1px);
    }
    .admin-btn {
      background: var(--dark);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.3s;
      margin-right: 0.5rem;
    }
    .admin-btn:hover {
      background: #1b5e20;
      transform: translateY(-1px);
    }

    /* Main Content */
    .main-content {
      padding: 3rem 5%;
      min-height: calc(100vh - 80px);
    }
    .welcome-card {
      background: var(--white);
      border-radius: 20px;
      padding: 3rem;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      margin-bottom: 2rem;
      position: relative;
    }
    .welcome-card h1 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      font-size: 2.8rem;
      margin-bottom: 1rem;
    }
    .welcome-card p {
      font-size: 1.1rem;
      color: #555;
      max-width: 700px;
      margin: 0 auto 1.5rem;
    }
    .btn-start {
      background: var(--green);
      color: white;
      padding: 0.8rem 2rem;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: 0.3s;
    }
    .btn-start:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }

    /* Module Cards */
    .module-card {
      background: var(--white);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
      border-left: 5px solid var(--green);
      transition: transform 0.3s;
      height: 100%;
      text-align: center;
    }
    .module-card:hover {
      transform: translateY(-5px);
    }
    .module-card i {
      color: var(--green);
      margin-bottom: 1rem;
    }
    .module-card h5 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      margin-bottom: 0.5rem;
    }

    /* Footer + Rapport Button (Bottom Right) */
    .rapport-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: var(--yellow);
      color: #333;
      padding: 0.8rem 1.5rem;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 999;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: 0.3s;
    }
    .rapport-btn:hover {
      background: #fdd835;
      transform: translateY(-3px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.2);
    }

    /* Footer */
    footer {
      background: #222;
      color: #ccc;
      text-align: center;
      padding: 2rem 0;
      font-size: 0.9rem;
      position: relative;
    }
    footer strong {
      color: var(--yellow);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .top-nav { flex-direction: column; gap: 1rem; padding: 1rem; }
      .nav-menu { order: 3; flex-wrap: wrap; justify-content: center; }
      .main-content { padding: 2rem 3%; }
      .welcome-card h1 { font-size: 2.2rem; }
      .user-section { flex-direction: column; text-align: center; }
      .user-info { text-align: center; }
    }
  </style>
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
  <a href="rapport.pdf" class="rapport-btn" target="_blank">
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