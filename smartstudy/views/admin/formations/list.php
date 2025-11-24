<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gérer les Formations | SmartStudy+ Admin</title>
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
    .logo .admin-badge {
      color: var(--red);
      font-size: 1rem;
    }
    .main-content {
      padding: 3rem 5%;
      max-width: 1400px;
      margin: 0 auto;
    }
    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }
    .page-header h1 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      font-size: 2.5rem;
    }
    .btn-add {
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
    .btn-add:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }
    .btn-back {
      background: #ccc;
      color: #333;
      padding: 0.6rem 1.5rem;
      border-radius: 30px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
    }
    .table-card {
      background: var(--white);
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background: var(--green);
      color: white;
      font-weight: 600;
    }
    tr:hover {
      background: var(--light);
    }
    .btn-edit {
      background: var(--yellow);
      color: #333;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.3s;
      margin-right: 0.5rem;
    }
    .btn-edit:hover {
      background: #fdd835;
    }
    .btn-delete {
      background: var(--red);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: 0.3s;
    }
    .btn-delete:hover {
      background: #d32f2f;
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
    .empty-state {
      text-align: center;
      padding: 3rem;
      color: #777;
    }
    .empty-state i {
      font-size: 4rem;
      color: #ccc;
      margin-bottom: 1rem;
    }
    .price {
      font-weight: 700;
      color: var(--dark);
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
      <a href="logout.php" style="color: var(--red); text-decoration: none; font-weight: 600;">
        <i class="fas fa-sign-out-alt"></i> Déconnexion
      </a>
    </div>
  </nav>

  <div class="main-content">
    <a href="index.php?controller=admin&action=dashboard" class="btn-back">
      <i class="fas fa-arrow-left"></i> Retour au tableau de bord
    </a>

    <div class="page-header">
      <h1><i class="fas fa-book"></i> Gérer les Formations</h1>
      <a href="index.php?controller=admin_formations&action=add" class="btn-add">
        <i class="fas fa-plus"></i> Ajouter une formation
      </a>
    </div>

    <?php if ($success === 'added'): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Formation ajoutée avec succès !
      </div>
    <?php endif; ?>

    <?php if ($success === 'updated'): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Formation modifiée avec succès !
      </div>
    <?php endif; ?>

    <?php if ($success === 'deleted'): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Formation supprimée avec succès !
      </div>
    <?php endif; ?>

    <?php if ($error_msg === 'not_found'): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> Formation non trouvée.
      </div>
    <?php elseif (!empty($error_msg)): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?>
      </div>
    <?php endif; ?>

    <div class="table-card">
      <?php if (empty($formations)): ?>
        <div class="empty-state">
          <i class="fas fa-book-open"></i>
          <h3>Aucune formation</h3>
          <p>Commencez par ajouter une formation.</p>
        </div>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Titre</th>
              <th>Catégorie</th>
              <th>Prix</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($formations as $formation): ?>
              <tr>
                <td><?= htmlspecialchars($formation['id_formation']) ?></td>
                <td><strong><?= htmlspecialchars($formation['titre']) ?></strong></td>
                <td><?= htmlspecialchars($categoriesMap[$formation['id_categorie']] ?? 'N/A') ?></td>
                <td class="price"><?= number_format($formation['prix'], 2) ?> €</td>
                <td>
                  <a href="index.php?controller=admin_formations&action=edit&id=<?= $formation['id_formation'] ?>" class="btn-edit">
                    <i class="fas fa-edit"></i> Modifier
                  </a>
                  <a href="index.php?controller=admin_formations&action=delete&id=<?= $formation['id_formation'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?');">
                    <i class="fas fa-trash-alt"></i> Supprimer
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

