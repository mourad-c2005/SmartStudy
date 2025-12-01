<?php
require_once('../../Controller/ForumController.php');
require_once('../../Controller/ReplyController.php');

$forumC = new ForumController();
$replyC = new ReplyController();

// Récupérer l'ID du forum
$forum_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($forum_id === 0) {
    header('Location: forums.php');
    exit();
}

// Récupérer le forum
$forum = $forumC->showForum($forum_id);
if (!$forum) {
    header('Location: forums.php');
    exit();
}

// Récupérer les réponses
$replies = $replyC->listRepliesByForum($forum_id);

// Gérer l'ajout d'une réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    if (!$forum['is_locked']) {
        $reply = new Reply(
            null,
            $forum_id,
            'Vous', // À remplacer par le nom de l'utilisateur connecté
            $_POST['content'],
            null,
            null,
            false,
            0
        );
        $replyC->addReply($reply);
        header("Location: thread.php?id=$forum_id");
        exit();
    }
}

// Gérer les likes
if (isset($_GET['action']) && isset($_GET['reply_id'])) {
    $reply_id = intval($_GET['reply_id']);
    if ($_GET['action'] === 'like') {
        $replyC->addLike($reply_id);
    }
    header("Location: thread.php?id=$forum_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SmartStudy+ | <?php echo htmlspecialchars($forum['title']); ?></title>

  <!-- Fonts & Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{--green:#4CAF50;--light:#E8F5E8;--white:#fff;--yellow:#FFEB3B}
    body{font-family:'Open Sans',sans-serif;background:var(--light);margin:0;color:#333}
    .top-nav{background:var(--white);padding:1rem 5%;box-shadow:0 4px 15px rgba(76,175,80,0.1);display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:1000}
    .logo{font-family:'Montserrat',sans-serif;font-weight:700;font-size:1.8rem;color:var(--green);text-decoration:none}
    .nav-menu{display:flex;gap:1.2rem}
    .nav-menu a{color:#555;text-decoration:none;font-weight:600;padding:0.4rem 0.9rem;border-radius:30px}
    .nav-menu a.active,.nav-menu a:hover{background:var(--light);color:var(--green)}
    .container-main{padding:3rem 5%}
    .thread-card{background:var(--white);padding:1.5rem;border-radius:12px;border-left:5px solid var(--green);box-shadow:0 6px 18px rgba(0,0,0,0.04)}
    .reply-card{background:#fcfffc;border-radius:10px;padding:1rem;margin-top:0.8rem;border:1px solid #e9f7ef;transition:all 0.3s}
    .reply-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.06)}
    .reply-card.solution{border-left:4px solid #28a745;background:#f0fff4}
    .meta{font-size:0.85rem;color:#666}
    .reply-form textarea{min-height:120px}
    .badge-solution{background:#28a745;color:#fff;padding:0.25rem 0.5rem;border-radius:12px;font-size:0.75rem}
    .badge-cat{background:#f0f7f0;color:var(--green);padding:0.25rem 0.5rem;border-radius:12px;font-weight:600;font-size:0.75rem}
    .btn-like{background:transparent;border:1px solid #ddd;border-radius:20px;padding:0.25rem 0.75rem;font-size:0.85rem;cursor:pointer;transition:all 0.3s}
    .btn-like:hover{background:var(--green);color:#fff;border-color:var(--green)}
    footer{background:#222;color:#ccc;text-align:center;padding:2rem 0;margin-top:2rem}
    footer strong{color:var(--yellow)}
  </style>
</head>
<body>

  <!-- Nav -->
  <nav class="top-nav">
    <a class="logo" href="forums.php">SmartStudy+</a>
    <div class="nav-menu">
      <a href="forums.php" class="active">← Retour au Forum</a>
    </div>
    <div class="user-section d-flex align-items-center gap-2">
      <div class="user-info text-end">
        <div class="name fw-bold">CHEBIL Fedi</div>
        <div class="role small text-muted">Étudiant</div>
      </div>
      <img src="https://via.placeholder.com/45?text=F" class="rounded-circle border border-3 border-success" alt="photo">
    </div>
  </nav>

  <main class="container-main">
    <!-- Thread principal -->
    <div class="thread-card">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <h3 style="font-family:Montserrat;color:var(--green);margin:0">
          <?php echo htmlspecialchars($forum['title']); ?>
        </h3>
        <?php if ($forum['is_locked']): ?>
          <span class="badge-cat" style="background:#ffcccb;color:#c00">
            <i class="fas fa-lock"></i> Verrouillé
          </span>
        <?php endif; ?>
      </div>
      
      <div class="meta mb-3">
        Par <strong><?php echo htmlspecialchars($forum['author']); ?></strong> • 
        <span class="badge-cat"><?php echo htmlspecialchars($forum['category']); ?></span> • 
        <?php echo date('d/m/Y à H:i', strtotime($forum['created_at'])); ?> • 
        <i class="fas fa-eye"></i> <?php echo $forum['views']; ?> vues
      </div>
      
      <hr/>
      
      <p style="margin-top:1rem;white-space:pre-wrap"><?php echo htmlspecialchars($forum['content']); ?></p>
    </div>

    <!-- Réponses -->
    <section style="margin-top:2rem">
      <h5 class="mb-3">
        <i class="fas fa-comments"></i> Réponses (<?php echo $replyC->listRepliesByForum($forum_id)->rowCount(); ?>)
      </h5>
      
      <div id="repliesList">
        <?php
        $hasReplies = false;
        foreach($replies as $reply) {
          $hasReplies = true;
          $replyClass = 'reply-card';
          if ($reply['is_solution']) $replyClass .= ' solution';
        ?>
        <div class="<?php echo $replyClass; ?>">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <strong><?php echo htmlspecialchars($reply['author']); ?></strong> 
              <span class="meta">• <?php echo date('d/m/Y à H:i', strtotime($reply['created_at'])); ?></span>
              <?php if ($reply['is_solution']): ?>
                <span class="badge-solution"><i class="fas fa-check"></i> Solution</span>
              <?php endif; ?>
            </div>
            <div class="d-flex gap-2 align-items-center">
              <a href="?id=<?php echo $forum_id; ?>&action=like&reply_id=<?php echo $reply['id']; ?>" class="btn-like">
                <i class="fas fa-thumbs-up"></i> <?php echo $reply['likes']; ?>
              </a>
              <span class="meta">#<?php echo $reply['id']; ?></span>
            </div>
          </div>
          <p style="margin-top:0.75rem;margin-bottom:0;white-space:pre-wrap"><?php echo htmlspecialchars($reply['content']); ?></p>
        </div>
        <?php
        }
        
        if (!$hasReplies) {
          echo '<p class="text-muted">Aucune réponse pour l\'instant. Soyez le premier à répondre !</p>';
        }
        ?>
      </div>
    </section>

    <!-- Formulaire de réponse -->
    <?php if (!$forum['is_locked']): ?>
    <section style="margin-top:2rem">
      <h5 class="mb-3"><i class="fas fa-reply"></i> Ajouter une réponse</h5>
      <form method="POST" class="reply-form">
        <div class="mb-3">
          <textarea name="content" class="form-control" placeholder="Écrire votre réponse..." required></textarea>
        </div>
        <div>
          <button type="submit" class="btn" style="background:var(--green);color:#fff">
            <i class="fas fa-paper-plane"></i> Publier la réponse
          </button>
        </div>
      </form>
    </section>
    <?php else: ?>
    <div class="alert alert-warning mt-4">
      <i class="fas fa-lock"></i> Ce sujet est verrouillé. Vous ne pouvez pas ajouter de réponse.
    </div>
    <?php endif; ?>
  </main>

  <footer>
    <p>SmartStudy+ © 2025 – Nature • Croissance • Sérénité</p>
    <p>Développé par <strong>BLUEPIXEL 2032</strong></p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>