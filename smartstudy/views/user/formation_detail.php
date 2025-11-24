<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($formation['titre']) ?> | SmartStudy+</title>
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

    /* User Section */
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
    .btn-start {
      background: var(--green);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 30px;
      font-weight: 600;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.3s;
    }
    .btn-start:hover {
      background: var(--dark);
      transform: translateY(-1px);
    }

    /* Main Content */
    .main-content {
      padding: 3rem 5%;
      max-width: 1200px;
      margin: 0 auto;
    }

    .btn-back {
      background: var(--green);
      color: white;
      padding: 0.6rem 1.5rem;
      border-radius: 30px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 2rem;
      transition: 0.3s;
    }
    .btn-back:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }

    .detail-card {
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      padding: 3rem;
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 3rem;
      align-items: start;
    }

    .detail-header .badge {
      background: var(--yellow);
      color: #333;
      padding: 0.4rem 1rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.9rem;
      margin-bottom: 1rem;
      display: inline-block;
    }

    .detail-header h1 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      font-size: 2.8rem;
      margin-bottom: 1rem;
    }

    .detail-header p {
      font-size: 1.1rem;
      color: #555;
      line-height: 1.6;
      margin-bottom: 1.5rem;
    }

    .detail-meta {
      display: flex;
      gap: 1.5rem;
      margin-top: 1rem;
      font-size: 0.95rem;
      color: #777;
    }

    .detail-meta .meta-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #777;
    }

    .price-section {
      background: linear-gradient(135deg, var(--green) 0%, var(--dark) 100%);
      color: white;
      padding: 2rem;
      border-radius: 16px;
      text-align: center;
      margin-bottom: 2rem;
    }

    .price-section .price-label {
      font-size: 1rem;
      opacity: 0.9;
      margin-bottom: 0.5rem;
    }

    .price-section .price {
      font-size: 3rem;
      font-weight: 700;
      margin: 0.5rem 0;
    }

    .btn-add-cart {
      background: var(--yellow);
      color: #333;
      padding: 1rem 2.5rem;
      border: none;
      border-radius: 30px;
      font-weight: 600;
      font-size: 1.1rem;
      cursor: pointer;
      transition: 0.3s;
      width: 100%;
      margin-top: 1rem;
    }

    .btn-add-cart:hover {
      background: #fbc02d;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }

    .links-section {
      background: var(--light);
      border-radius: 12px;
      padding: 2rem;
      margin-top: 2rem;
      grid-column: 1 / -1;
    }

    .links-section h3 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      margin-bottom: 1.5rem;
    }

    .link-item {
      background: var(--white);
      padding: 1rem 1.5rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .link-item a {
      color: var(--green);
      text-decoration: none;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .link-item a:hover {
      text-decoration: underline;
    }

    .alert-success {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      border-left: 4px solid var(--green);
    }

    .alert-error {
      background: #ffebee;
      color: #c62828;
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      border-left: 4px solid var(--red);
    }

    @media (max-width: 992px) {
      .top-nav { flex-direction: column; gap: 1rem; padding: 1rem; }
      .nav-menu { order: 3; flex-wrap: wrap; justify-content: center; }
      .main-content { padding: 2rem 3%; }
      .detail-card {
        grid-template-columns: 1fr;
        padding: 2rem;
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
    <div style="display: flex; align-items: center; gap: 1rem;">
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-section">
          <div class="user-info">
            <div class="name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilisateur') ?></div>
            <div class="role"><?= htmlspecialchars($_SESSION['user_role'] ?? '') ?></div>
          </div>
          <img src="<?= htmlspecialchars($_SESSION['profile_picture'] ?? 'https://via.placeholder.com/45?text=' . strtoupper(substr($_SESSION['user_prenom'] ?? '', 0, 1) . substr($_SESSION['user_nom'] ?? '', 0, 1))) ?>" alt="Photo" class="user-photo">
          <a href="index.php?controller=panier&action=show" style="color: var(--green); text-decoration: none; font-weight: 600; position: relative;">
            <i class="fas fa-shopping-cart"></i> Panier
            <?php 
            require_once __DIR__ . "/../../models/Panier.php";
            $cartCount = Panier::getCount();
            if ($cartCount > 0): ?>
              <span class="badge bg-danger rounded-pill" style="position: absolute; top: -10px; right: -10px; font-size: 0.7em;"><?= $cartCount ?></span>
            <?php endif; ?>
          </a>
          <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Se déconnecter
          </a>
        </div>
      <?php else: ?>
        <a href="index.php?controller=auth&action=login" class="btn-start">
          <i class="fas fa-sign-in-alt"></i> Se connecter
        </a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="main-content">
    <a href="index.php?controller=user&action=formations" class="btn-back">
      <i class="fas fa-arrow-left"></i> Retour aux formations
    </a>

    <?php if (!empty($cartMessage)): ?>
      <div class="alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($cartMessage) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($cartError)): ?>
      <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($cartError) ?>
      </div>
    <?php endif; ?>

    <div class="detail-card">
      <div class="detail-header">
        <span class="badge">
          <?= $category ? htmlspecialchars($category['nom']) : 'Formation' ?>
        </span>
        <h1><?= htmlspecialchars($formation['titre']) ?></h1>
        <p><?= htmlspecialchars($formation['description'] ?? 'Aucune description disponible.') ?></p>
        <div class="detail-meta">
          <?php if ($section): ?>
            <div class="meta-item">
              <i class="fas fa-folder"></i> <?= htmlspecialchars($section['nom']) ?>
            </div>
          <?php endif; ?>
          <?php if ($category): ?>
            <div class="meta-item">
              <i class="fas fa-tag"></i> <?= htmlspecialchars($category['nom']) ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="price-section">
        <div class="price-label">Prix de la formation</div>
        <div class="price"><?= number_format($formation['prix'], 2) ?> €</div>
        <form action="index.php?controller=user&action=formation_detail&id=<?= $formation['id_formation'] ?>" method="post">
          <button type="submit" name="add_to_cart" class="btn-add-cart">
            <i class="fas fa-cart-plus"></i> Ajouter au panier
          </button>
        </form>
      </div>

      <?php if (!empty($urls)): ?>
        <div class="links-section">
          <h3><i class="fas fa-link"></i> Liens de la formation</h3>
          <?php $linkNum = 1; foreach ($urls as $link): ?>
            <div class="link-item">
              <span>Lien <?= $linkNum++ ?>:</span>
              <a href="<?= htmlspecialchars($link) ?>" target="_blank">
                <i class="fas fa-external-link-alt"></i> <?= htmlspecialchars($link) ?>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

