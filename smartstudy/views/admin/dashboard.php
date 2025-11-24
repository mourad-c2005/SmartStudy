<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tableau de bord Admin | SmartStudy+</title>
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
      --red: #f44336;
    }
    * { box-sizing: border-box; }
    body {
      font-family: 'Open Sans', sans-serif;
      background: var(--light);
      color: #333;
      margin: 0;
    }

    /* Top Navigation */
    .top-nav {
      background: var(--white);
      padding: 1rem 5%;
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
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
    .logo .admin-badge {
      color: var(--red);
      font-size: 1rem;
    }

    /* Main Content */
    .main-content {
      padding: 3rem 5%;
      max-width: 1400px;
      margin: 0 auto;
    }

    .dashboard-header {
      text-align: center;
      margin-bottom: 3rem;
    }

    .dashboard-header h1 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      font-size: 2.8rem;
      margin-bottom: 0.5rem;
    }

    .dashboard-header p {
      color: #777;
      font-size: 1.1rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background: var(--white);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
      text-align: center;
      transition: transform 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-card i {
      font-size: 3rem;
      color: var(--green);
      margin-bottom: 1rem;
    }

    .stat-card .number {
      font-family: 'Montserrat', sans-serif;
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--green);
      margin-bottom: 0.5rem;
    }

    .stat-card .label {
      color: #777;
      font-size: 1rem;
      font-weight: 600;
    }

    .actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
    }

    .action-card {
      background: var(--white);
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
      border-left: 5px solid var(--green);
      transition: transform 0.3s;
    }

    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .action-card h3 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      margin-bottom: 1rem;
      font-size: 1.5rem;
    }

    .action-card p {
      color: #555;
      margin-bottom: 1.5rem;
      line-height: 1.6;
    }

    .btn-action {
      background: var(--green);
      color: white;
      padding: 0.8rem 1.5rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: 0.3s;
    }

    .btn-action:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }

    .btn-action.secondary {
      background: var(--yellow);
      color: #333;
    }

    .btn-action.secondary:hover {
      background: #fdd835;
    }

    .btn-logout {
      background: var(--red);
      color: white;
      padding: 0.6rem 1.2rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-logout:hover {
      background: #d32f2f;
      transform: translateY(-1px);
    }

    @media (max-width: 768px) {
      .main-content { padding: 2rem 3%; }
      .stats-grid, .actions-grid {
        grid-template-columns: 1fr;
      }
      .dashboard-header h1 {
        font-size: 2.2rem;
      }
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <a href="index.php?controller=admin&action=dashboard" class="logo">
      SmartStudy+ <span class="admin-badge">Admin</span>
    </a>
    <div style="display: flex; align-items: center; gap: 1rem;">
      <a href="index.php?controller=user&action=home" style="color: var(--green); text-decoration: none; font-weight: 600;">
        <i class="fas fa-home"></i> Site public
      </a>
      <a href="logout.php" class="btn-logout">
        <i class="fas fa-sign-out-alt"></i> Déconnexion
      </a>
    </div>
  </nav>

  <div class="main-content">
    <div class="dashboard-header">
      <h1><i class="fas fa-tachometer-alt"></i> Tableau de bord Administrateur</h1>
      <p>Gérez les sections, catégories et formations de SmartStudy+</p>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <i class="fas fa-folder"></i>
        <div class="number"><?= $totalSections ?></div>
        <div class="label">Sections</div>
      </div>

      <div class="stat-card">
        <i class="fas fa-tags"></i>
        <div class="number"><?= $totalCategories ?></div>
        <div class="label">Catégories</div>
      </div>

      <div class="stat-card">
        <i class="fas fa-book"></i>
        <div class="number"><?= $totalFormations ?></div>
        <div class="label">Formations</div>
      </div>
    </div>

    <div class="actions-grid">
      <div class="action-card">
        <h3><i class="fas fa-folder"></i> Gérer les Sections</h3>
        <p>Créez, modifiez et supprimez les sections de formations.</p>
        <a href="index.php?controller=admin_sections&action=list" class="btn-action">
          <i class="fas fa-arrow-right"></i> Gérer les sections
        </a>
      </div>

      <div class="action-card">
        <h3><i class="fas fa-tags"></i> Gérer les Catégories</h3>
        <p>Organisez vos formations en catégories par section.</p>
        <a href="index.php?controller=admin_categories&action=list" class="btn-action">
          <i class="fas fa-arrow-right"></i> Gérer les catégories
        </a>
      </div>

      <div class="action-card">
        <h3><i class="fas fa-book"></i> Gérer les Formations</h3>
        <p>Ajoutez, modifiez et supprimez les formations disponibles.</p>
        <a href="index.php?controller=admin_formations&action=list" class="btn-action">
          <i class="fas fa-arrow-right"></i> Gérer les formations
        </a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
