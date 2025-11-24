<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Votre Panier | SmartStudy+</title>
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

    .main-content {
      padding: 3rem 5%;
      max-width: 900px;
      margin: 0 auto;
    }

    .cart-card {
      background: var(--white);
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      padding: 3rem;
    }

    .cart-card h1 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      font-size: 2.5rem;
      margin-bottom: 2rem;
      text-align: center;
    }

    .cart-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 10px;
      margin-bottom: 2rem;
    }

    .cart-table th, .cart-table td {
      padding: 1rem;
      text-align: left;
      vertical-align: middle;
    }

    .cart-table th {
      background: var(--green);
      color: white;
      font-weight: 600;
      border: none;
    }

    .cart-table tr:first-child th:first-child { border-top-left-radius: 10px; }
    .cart-table tr:first-child th:last-child { border-top-right-radius: 10px; }

    .cart-table td {
      background: var(--light);
      border-bottom: 1px solid #e0e0e0;
    }

    .cart-table tr:last-child td { border-bottom: none; }

    .cart-table .item-title {
      font-weight: 600;
      color: #333;
    }

    .cart-table .item-price {
      font-weight: 700;
      color: var(--dark);
    }

    .btn-remove {
      background: var(--red);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.3s;
    }

    .btn-remove:hover {
      background: #d32f2f;
      transform: translateY(-1px);
    }

    .cart-summary {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 2px solid #eee;
    }

    .cart-summary .total-label {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .cart-summary .total-price {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--green);
    }

    .cart-actions {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      margin-top: 2rem;
    }

    .btn-clear-cart {
      background: #9e9e9e;
      color: white;
      padding: 0.8rem 1.5rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-clear-cart:hover {
      background: #757575;
      transform: translateY(-2px);
    }

    .btn-checkout {
      background: var(--yellow);
      color: #333;
      padding: 0.8rem 1.5rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn-checkout:hover {
      background: #fdd835;
      transform: translateY(-2px);
    }

    .btn-back-formations {
      display: inline-block;
      margin-top: 2rem;
      color: var(--green);
      text-decoration: none;
      font-weight: 600;
    }

    .btn-back-formations:hover {
      text-decoration: underline;
    }

    .empty-cart {
      text-align: center;
      padding: 3rem;
    }

    .empty-cart i {
      font-size: 5rem;
      color: #ccc;
      margin-bottom: 1rem;
    }

    .empty-cart h2 {
      color: #777;
      margin-bottom: 1rem;
    }

    .alert {
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background: #e8f5e9;
      color: #2e7d32;
      border-left: 4px solid var(--green);
    }

    .alert-error {
      background: #ffebee;
      color: #c62828;
      border-left: 4px solid var(--red);
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
            <?php if (isset($items) && count($items) > 0): ?>
              <span class="badge bg-danger rounded-pill" style="position: absolute; top: -10px; right: -10px; font-size: 0.7em;"><?= count($items) ?></span>
            <?php endif; ?>
          </a>
          <a href="index.php?controller=auth&action=logout" class="logout-btn">
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
    <div class="cart-card">
      <h1><i class="fas fa-shopping-cart"></i> Votre Panier</h1>

      <?php 
      // Debug info (temporaire)
      if (isset($_GET['debug'])) {
        echo "<div style='background: #fff3cd; padding: 1rem; margin-bottom: 1rem; border-radius: 5px;'>";
        echo "<strong>Debug Info:</strong><br>";
        echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'N/A') . "<br>";
        echo "Items count: " . (isset($items) ? count($items) : 'N/A') . "<br>";
        echo "Total: " . (isset($total) ? $total : 'N/A') . "<br>";
        if (isset($items) && !empty($items)) {
          echo "Items: <pre>" . print_r($items, true) . "</pre>";
        }
        echo "</div>";
      }
      ?>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> Formation ajoutée au panier avec succès !
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> Cette formation est déjà dans votre panier.
        </div>
      <?php endif; ?>

      <?php if (empty($items)): ?>
        <div class="empty-cart">
          <i class="fas fa-shopping-cart"></i>
          <h2>Votre panier est vide</h2>
          <p>Commencez à ajouter des formations à votre panier !</p>
          <div style="margin-top: 2rem;">
            <a href="index.php?controller=user&action=formations" class="btn-back-formations">
              <i class="fas fa-arrow-left"></i> Continuer vos achats
            </a>
          </div>
        </div>
      <?php else: ?>
        <table class="cart-table">
          <thead>
            <tr>
              <th>Formation</th>
              <th>Prix</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td class="item-title"><?= htmlspecialchars($item['titre']) ?></td>
                <td class="item-price"><?= number_format($item['prix'], 2) ?> €</td>
                <td>
                  <a href="index.php?controller=panier&action=remove&id=<?= $item['id_formation'] ?>" class="btn-remove" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation du panier ?');">
                    <i class="fas fa-trash-alt"></i> Supprimer
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="cart-summary">
          <span class="total-label">Total :</span>
          <span class="total-price"><?= number_format($total, 2) ?> €</span>
        </div>

        <div class="cart-actions">
          <a href="index.php?controller=panier&action=clear" class="btn-clear-cart" onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier ?');">
            <i class="fas fa-times-circle"></i> Vider le panier
          </a>
          <a href="index.php?controller=panier&action=checkout" class="btn-checkout">
            <i class="fas fa-credit-card"></i> Payer maintenant
          </a>
        </div>

        <div class="text-center mt-4">
          <a href="index.php?controller=user&action=formations" class="btn-back-formations">
            <i class="fas fa-arrow-left"></i> Continuer vos achats
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

