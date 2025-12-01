<?php
require_once('../../Controller/ForumController.php');

$forumC = new ForumController();

// Gestion de la recherche et du filtre
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!empty($search)) {
    $list = $forumC->searchForums($search);
} elseif (!empty($category)) {
    $list = $forumC->filterByCategory($category);
} else {
    $list = $forumC->listForums();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SmartStudy+ | Forum</title>

  <!-- Fonts & Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --green:#4CAF50; --yellow:#FFEB3B; --light:#E8F5E8; --white:#fff; --dark:#2e7d32;
    }
    body{font-family:'Open Sans',sans-serif;background:var(--light);margin:0;color:#333}
    .top-nav{background:var(--white);padding:1rem 5%;box-shadow:0 4px 15px rgba(76,175,80,0.1);display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:1000}
    .logo{font-family:'Montserrat',sans-serif;font-weight:700;font-size:1.8rem;color:var(--green);text-decoration:none}
    .nav-menu{display:flex;gap:1.2rem}
    .nav-menu a{color:#555;text-decoration:none;font-weight:600;padding:0.4rem 0.9rem;border-radius:30px}
    .nav-menu a.active,.nav-menu a:hover{background:var(--light);color:var(--green)}
    .container-main{padding:3rem 5%}
    .forum-actions{display:flex;gap:1rem;flex-wrap:wrap;align-items:center;margin-bottom:1rem}
    .search-box{flex:1;min-width:220px}
    .badge-cat{background:#f0f7f0;color:var(--green);padding:0.25rem 0.5rem;border-radius:12px;font-weight:600;font-size:0.75rem}
    .badge-pinned{background:#ffd700;color:#333;padding:0.25rem 0.5rem;border-radius:12px;font-weight:600;font-size:0.75rem;margin-left:0.5rem}
    .topic-card{background:var(--white);border-radius:12px;padding:1rem;border-left:4px solid var(--green);box-shadow:0 6px 18px rgba(0,0,0,0.04);margin-bottom:1rem;transition:all 0.3s}
    .topic-card:hover{box-shadow:0 8px 25px rgba(0,0,0,0.08);transform:translateY(-2px)}
    .topic-card.pinned{border-left-color:#ffd700;background:#fffef5}
    .topic-card.locked{opacity:0.7}
    .topic-meta{font-size:0.85rem;color:#666}
    .small-muted{font-size:0.85rem;color:#777}
    footer{background:#222;color:#ccc;text-align:center;padding:2rem 0;margin-top:2rem}
    footer strong{color:var(--yellow)}
  </style>
</head>
<body>

  <!-- Nav -->
  <nav class="top-nav">
    <a class="logo" href="forums.php">SmartStudy+</a>
    <div class="nav-menu">
      <a href="forums.php" class="active">Forum</a>
      <a href="../BackOffice/manageForums.php">Admin</a>
    </div>
    <div class="user-section d-flex align-items-center gap-2">
      <div class="user-info text-end">
        <div class="name fw-bold">CHEBIL Fedi</div>
        <div class="role text-muted small">Étudiant</div>
      </div>
      <img src="https://via.placeholder.com/45?text=F" class="rounded-circle border border-3 border-success" alt="photo">
    </div>
  </nav>

  <!-- Main -->
  <main class="container-main">
    <h1 style="font-family:Montserrat;color:var(--green);text-align:center;margin-bottom:1.5rem">Forum & Discussions</h1>

    <div class="forum-actions">
      <form method="GET" class="d-flex gap-2 flex-grow-1">
        <input name="search" class="form-control search-box" type="search" placeholder="Chercher un sujet, mot-clé..." value="<?php echo htmlspecialchars($search); ?>" />
        <select name="category" class="form-select" style="width:220px">
          <option value="">Toutes catégories</option>
          <option value="Planning" <?php echo $category === 'Planning' ? 'selected' : ''; ?>>Planning</option>
          <option value="Méthodes" <?php echo $category === 'Méthodes' ? 'selected' : ''; ?>>Méthodes</option>
          <option value="Technique" <?php echo $category === 'Technique' ? 'selected' : ''; ?>>Technique</option>
          <option value="Général" <?php echo $category === 'Général' ? 'selected' : ''; ?>>Général</option>
        </select>
        <button type="submit" class="btn btn-success">
          <i class="fas fa-search"></i>
        </button>
        <?php if (!empty($search) || !empty($category)): ?>
          <a href="forums.php" class="btn btn-secondary">Réinitialiser</a>
        <?php endif; ?>
      </form>
      <a href="../BackOffice/addForum.php" class="btn" style="background:var(--green);color:#fff;border-radius:30px">
        <i class="fas fa-plus"></i> Nouveau sujet
      </a>
    </div>

    <!-- Topics List -->
    <div id="topicsList">
      <?php
      $hasTopics = false;
      foreach($list as $forum) {
        $hasTopics = true;
        $repliesCount = $forumC->countReplies($forum['id']);
        $lastReply = $forumC->getLastReplyDate($forum['id']);
        $lastDate = $lastReply ? date('d/m/Y', strtotime($lastReply)) : date('d/m/Y', strtotime($forum['created_at']));
        $excerpt = strlen($forum['content']) > 120 ? substr($forum['content'], 0, 120) . '...' : $forum['content'];
        
        $cardClass = 'topic-card';
        if ($forum['is_pinned']) $cardClass .= ' pinned';
        if ($forum['is_locked']) $cardClass .= ' locked';
      ?>
      <div class="<?php echo $cardClass; ?> d-flex justify-content-between align-items-start">
        <div style="flex:1">
          <div>
            <a href="thread.php?id=<?php echo $forum['id']; ?>" class="h5" style="text-decoration:none;color:#2c3e50">
              <?php echo htmlspecialchars($forum['title']); ?>
            </a>
            <?php if ($forum['is_pinned']): ?>
              <span class="badge-pinned"><i class="fas fa-thumbtack"></i> Épinglé</span>
            <?php endif; ?>
            <?php if ($forum['is_locked']): ?>
              <span class="badge-cat" style="background:#ffcccb;color:#c00"><i class="fas fa-lock"></i> Verrouillé</span>
            <?php endif; ?>
          </div>
          <div class="topic-meta small-muted mt-1"><?php echo htmlspecialchars($excerpt); ?></div>
          <div class="small-muted mt-2">
            Par <strong><?php echo htmlspecialchars($forum['author']); ?></strong> • 
            <span class="badge-cat"><?php echo htmlspecialchars($forum['category']); ?></span> • 
            <i class="fas fa-eye"></i> <?php echo $forum['views']; ?> vues
          </div>
        </div>
        <div style="width:120px;text-align:right">
          <div class="small-muted"><i class="fas fa-comments"></i> <?php echo $repliesCount; ?> réponses</div>
          <div class="small-muted">Dernier: <?php echo $lastDate; ?></div>
        </div>
      </div>
      <?php
      }
      
      if (!$hasTopics) {
        echo '<div class="topic-card text-center"><p class="small-muted">Aucun sujet trouvé.</p></div>';
      }
      ?>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <p>SmartStudy+ © 2025 – Nature • Croissance • Sérénité</p>
    <p>Développé par <strong>BLUEPIXEL 2032</strong></p>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>