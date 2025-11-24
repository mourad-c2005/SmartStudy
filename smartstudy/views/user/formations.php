<?php
// This view is called by UserController::formations()
// Variables available: $sections, $allCategories, $selectedSection, $selectedCategory, $formations
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formations | SmartStudy+</title>
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
    .nav-menu a i {
      margin-right: 0.3rem;
    }
    .user-section {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .user-info {
      text-align: right;
    }
    .user-info .name {
      font-weight: 600;
      color: #333;
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
    .main-content {
      padding: 3rem 5%;
    }
    .filters-section {
      background: var(--white);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
      margin-bottom: 2rem;
    }
    .filters-section h2 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      margin-bottom: 1.5rem;
      font-size: 1.8rem;
    }
    .filters-row {
      display: grid;
      grid-template-columns: 1fr 1fr auto;
      gap: 1.5rem;
      align-items: end;
    }
    .filter-group {
      display: flex;
      flex-direction: column;
    }
    .filter-group label {
      font-weight: 600;
      color: #555;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }
    .filter-group select {
      padding: 0.8rem 1rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      font-family: 'Open Sans', sans-serif;
      transition: 0.3s;
      background: white;
    }
    .filter-group select:focus {
      outline: none;
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    .btn-filter {
      background: var(--green);
      color: white;
      padding: 0.8rem 2rem;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
      white-space: nowrap;
    }
    .btn-filter:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }
    .btn-reset {
      background: #ccc;
      color: #333;
      padding: 0.8rem 1.5rem;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: 0.3s;
    }
    .btn-reset:hover {
      background: #bbb;
    }
    .formations-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }
    .formations-header h2 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      margin: 0;
    }
    .formations-count {
      color: #777;
      font-size: 0.9rem;
    }
    .formations-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 2rem;
    }
    .formation-card {
      background: var(--white);
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
      border-left: 5px solid var(--green);
      transition: transform 0.3s;
      display: flex;
      flex-direction: column;
    }
    .formation-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .formation-card .badge {
      display: inline-block;
      background: var(--light);
      color: var(--green);
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    .formation-card h3 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      margin: 0 0 0.5rem 0;
      font-size: 1.3rem;
    }
    .formation-card .category {
      color: #777;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }
    .formation-card .price {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--green);
      margin-top: auto;
      margin-bottom: 1rem;
    }
    .formation-card .btn-view {
      background: var(--green);
      color: white;
      padding: 0.8rem;
      border-radius: 10px;
      text-decoration: none;
      text-align: center;
      font-weight: 600;
      transition: 0.3s;
      display: block;
    }
    .formation-card .btn-view:hover {
      background: var(--dark);
    }
    .no-results {
      text-align: center;
      padding: 4rem 2rem;
      color: #777;
    }
    .no-results i {
      font-size: 4rem;
      color: #ccc;
      margin-bottom: 1rem;
    }
    @media (max-width: 992px) {
      .top-nav { flex-direction: column; gap: 1rem; padding: 1rem; }
      .nav-menu { order: 3; flex-wrap: wrap; justify-content: center; }
      .main-content { padding: 2rem 3%; }
      .filters-row {
        grid-template-columns: 1fr;
      }
      .formations-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <a href="index.php?controller=user&action=home" class="logo">SmartStudy+</a>
    <div class="nav-menu">
      <a href="index.php?controller=user&action=home">Accueil</a>
      <a href="index.php?controller=user&action=formations" class="active"><i class="fas fa-book"></i> Formations</a>
      <a href="mesplans.html">Mes Plans</a>
      <a href="planning.html">Planning</a>
      <a href="groupes.html">Groupes</a>
      <a href="progres.html">Progrès</a>
    </div>
    <div class="user-section">
      <div class="user-info">
        <div class="name"><?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Visiteur' ?></div>
        <div class="role"><?= isset($_SESSION['user_role']) ? ucfirst($_SESSION['user_role']) : 'Invité' ?></div>
      </div>
      <img src="https://via.placeholder.com/45?text=<?= isset($_SESSION['user_name']) ? substr($_SESSION['user_name'], 0, 1) : 'V' ?>" alt="Photo" class="user-photo">
      <?php if (isset($_SESSION['user_role'])): ?>
        <a href="index.php?controller=auth&action=logout" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Se déconnecter
        </a>
      <?php else: ?>
        <a href="index.php?controller=auth&action=login" class="logout-btn">
          <i class="fas fa-sign-in-alt"></i> Se connecter
        </a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="main-content">
    <div class="filters-section">
      <h2><i class="fas fa-filter"></i> Filtrer les Formations</h2>
      <form method="GET" action="index.php">
        <input type="hidden" name="controller" value="user">
        <input type="hidden" name="action" value="formations">
        <div class="filters-row">
          <div class="filter-group">
            <label for="section"><i class="fas fa-folder"></i> Section</label>
            <select name="section" id="section" onchange="updateCategories()">
              <option value="">Toutes les sections</option>
              <?php foreach ($sections as $section): ?>
                <option value="<?= $section['id'] ?>" <?= $selectedSection == $section['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($section['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="filter-group">
            <label for="category"><i class="fas fa-tags"></i> Catégorie</label>
            <select name="category" id="category">
              <option value="">Toutes les catégories</option>
              <?php 
              $displayCategories = $selectedSection ? Category::getBySection($selectedSection) : $allCategories;
              foreach ($displayCategories as $category): 
              ?>
                <option value="<?= $category['id'] ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($category['nom']) ?>
                  <?= isset($category['section_nom']) ? ' - ' . htmlspecialchars($category['section_nom']) : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn-filter">
              <i class="fas fa-search"></i> Filtrer
            </button>
            <a href="index.php?controller=user&action=formations" class="btn-reset">
              <i class="fas fa-redo"></i> Réinitialiser
            </a>
          </div>
        </div>
      </form>
    </div>

    <div class="formations-header">
      <h2><i class="fas fa-book-open"></i> Formations Disponibles</h2>
      <div class="formations-count">
        <?= count($formations) ?> formation(s) trouvée(s)
      </div>
    </div>

    <?php if (empty($formations)): ?>
      <div class="no-results">
        <i class="fas fa-inbox"></i>
        <h3>Aucune formation trouvée</h3>
        <p>Essayez de modifier vos filtres ou ajoutez des formations dans la base de données.</p>
      </div>
    <?php else: ?>
      <div class="formations-grid">
        <?php foreach ($formations as $formation): ?>
          <div class="formation-card">
            <span class="badge">
              <?= isset($formation['category_nom']) ? htmlspecialchars($formation['category_nom']) : 'Formation' ?>
            </span>
            <h3><?= htmlspecialchars($formation['titre'] ?? 'Titre non disponible') ?></h3>
            <div class="category">
              <i class="fas fa-tag"></i> Catégorie: <?= isset($formation['category_nom']) ? htmlspecialchars($formation['category_nom']) : 'N/A' ?>
            </div>
            <?php if (isset($formation['prix'])): ?>
              <div class="price">
                <?= number_format($formation['prix'], 2) ?> €
              </div>
            <?php endif; ?>
            <a href="index.php?controller=user&action=formation_detail&id=<?= $formation['id_formation'] ?? '' ?>" class="btn-view">
              <i class="fas fa-eye"></i> Voir les détails
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function updateCategories() {
      const sectionId = document.getElementById('section').value;
      const categorySelect = document.getElementById('category');
      const form = categorySelect.closest('form');
      if (form && sectionId) {
        form.submit();
      }
    }
  </script>
</body>
</html>

