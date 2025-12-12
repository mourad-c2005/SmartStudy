<?php
// En haut de SmartStudyForum/View/FrontOffice/thread.php
session_start();

// ✅ Vérifier connexion
if (!isset($_SESSION['user'])) {
    header('Location: ../../../view/login.php');
    exit();
}

$currentUser = $_SESSION['user']['nom'];
$isAdmin = ($_SESSION['user']['role'] === 'admin');
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

// Récupérer les réponses principales (sans parent)
$replies = $replyC->listMainRepliesByForum($forum_id);

// Gérer l'ajout d'une réponse
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    if (!$forum['is_locked']) {
              // ✅ VALIDATION DU CONTENU
              require_once('../../contentValidator.php');
              $validation = ContentValidator::validate($_POST['content']);
              
              if (!$validation['valid']) {
                  $_SESSION['error_message'] = $validation['error'];
                  header("Location: thread.php?id=$forum_id&error=content");
                  exit();
              }
        $parent_id = isset($_POST['parent_id']) && !empty($_POST['parent_id']) ? intval($_POST['parent_id']) : null;
        
        $reply = new Reply(
            null,
            $forum_id,
            $parent_id,  // ← NOUVEAU
            
            $_POST['content'],
            null,
            null,
            false,
            0
        );
        $replyC->addReply($reply);
        header("Location: thread.php?id=$forum_id&success=1");
        exit();
    }
}

// Afficher les erreurs si présentes
if (isset($_GET['error']) && $_GET['error'] === 'content' && isset($_SESSION['error_message'])) {
  echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin:20px">';
  echo '<i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($_SESSION['error_message']);
  echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
  echo '</div>';
  unset($_SESSION['error_message']);
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
    .nested-reply{margin-left:3rem;margin-top:0.5rem;background:#f8fffe;border-left:3px solid #81c784}
    .meta{font-size:0.85rem;color:#666}
    .reply-form textarea{min-height:120px}
    .badge-solution{background:#28a745;color:#fff;padding:0.25rem 0.5rem;border-radius:12px;font-size:0.75rem}
    .badge-cat{background:#f0f7f0;color:var(--green);padding:0.25rem 0.5rem;border-radius:12px;font-weight:600;font-size:0.75rem}
    .btn-like,.btn-reply{background:transparent;border:1px solid #ddd;border-radius:20px;padding:0.25rem 0.75rem;font-size:0.85rem;cursor:pointer;transition:all 0.3s;text-decoration:none;display:inline-block}
    .btn-like:hover,.btn-reply:hover{background:var(--green);color:#fff;border-color:var(--green)}
    .btn-reply{border-color:#2196F3;color:#2196F3}
    .btn-reply:hover{background:#2196F3;border-color:#2196F3}
    footer{background:#222;color:#ccc;text-align:center;padding:2rem 0;margin-top:2rem}
    footer strong{color:var(--yellow)}
    .btn-like.liked{background:var(--green);color:#fff;border-color:var(--green);cursor:not-allowed}
    .btn-like.liked:hover{background:var(--green);color:#fff;opacity:0.8}
    .reply-form-inline{display:none;margin-top:1rem;padding:1rem;background:#f0f9ff;border-radius:8px;border:1px solid #2196F3}
  </style>
</head>
<body>

<?php  ?>
  
  <!-- Nav -->
  <nav class="top-nav">
    <!-- ... votre nav existante ... -->
  </nav>

  <!-- Nav -->
  <nav class="top-nav">
  <div class="nav-menu">
    <a href="forums.php" class="active">Forum</a>
    <a href="chatbot.php">
        <i class="fas fa-robot"></i> Assistant Bien-être
    </a>
    <a href="../BackOffice/index.html" class="nav-link">Dashboard</a>
</div>
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
        <i class="fas fa-eye"></i> <strong><?php echo $forum['views']; ?></strong> vues •
        <i class="fas fa-comments"></i> <strong><?php echo $replyC->listRepliesByForum($forum_id)->rowCount(); ?></strong> réponses
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
        <div class="<?php echo $replyClass; ?>" id="reply-<?php echo $reply['id']; ?>">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <strong><?php echo htmlspecialchars($reply['author']); ?></strong> 
              <span class="meta">• <?php echo date('d/m/Y à H:i', strtotime($reply['created_at'])); ?></span>
              <?php if ($reply['is_solution']): ?>
                <span class="badge-solution"><i class="fas fa-check"></i> Solution</span>
              <?php endif; ?>
            </div>
            <div class="d-flex gap-2 align-items-center">
              <?php
              $like_key = "liked_reply_" . $reply['id'];
              $has_liked = isset($_SESSION[$like_key]);
              $like_class = $has_liked ? 'btn-like liked' : 'btn-like';
              // ✅ Vérifier si c'est l'auteur (par nom)
              $isOwner = ($reply['author'] === $currentUser);
              ?>
              
              <a href="like.php?reply_id=<?php echo $reply['id']; ?>&forum_id=<?php echo $forum_id; ?>" 
                 class="<?php echo $like_class; ?>"
                 <?php if ($has_liked): ?>
                   onclick="return confirm('Vous avez déjà aimé cette réponse !');"
                 <?php endif; ?>>
                <i class="fas fa-heart"></i> <?php echo $reply['likes']; ?>
              </a>
              
              <!-- Bouton Répondre -->
              <?php if (!$forum['is_locked']): ?>
              <button class="btn-reply" onclick="toggleReplyForm(<?php echo $reply['id']; ?>)">
                <i class="fas fa-reply"></i> Répondre
              </button>
              <?php endif; ?>

                  <!-- ✅ Bouton Modifier (seulement pour l'auteur) -->
    <?php if ($isOwner && !$isAdminUser): ?>
    <a href="editReply.php?id=<?php echo $reply['id']; ?>&forum_id=<?php echo $forum_id; ?>" 
       class="btn-like" style="background:transparent;border:1px solid #2196F3;color:#2196F3"
       title="Modifier votre réponse">
        <i class="fas fa-edit"></i>
    </a>
    <?php endif; ?>
              
    <!-- ✅ Bouton Supprimer (pour l'auteur OU admin) -->
    <?php if ($isOwner ): ?>
    <a href="deleteReply.php?id=<?php echo $reply['id']; ?>&forum_id=<?php echo $forum_id; ?>" 
       class="btn-like" 
       style="background:transparent;border:1px solid #dc3545;color:#dc3545"
       onclick="return confirm('Voulez-vous vraiment supprimer cette réponse ?')"
       title="<?php echo $isAdminUser ? 'Supprimer (Admin)' : 'Supprimer votre réponse'; ?>">
        <i class="fas fa-trash"></i>
        <?php if ($isAdminUser): ?>
          <small style="font-size:0.7em">Admin</small>
        <?php endif; ?>
    </a>
    <?php endif; ?>
              
              <span class="meta">#<?php echo $reply['id']; ?></span>
            </div>
          </div>
          
          <p style="margin-top:0.75rem;margin-bottom:0;white-space:pre-wrap"><?php echo htmlspecialchars($reply['content']); ?></p>
          
          <!-- Formulaire de réponse inline (caché par défaut) -->
          <?php if (!$forum['is_locked']): ?>
          <div id="reply-form-<?php echo $reply['id']; ?>" class="reply-form-inline">
            <form method="POST">
              <input type="hidden" name="parent_id" value="<?php echo $reply['id']; ?>">
              <div class="mb-2">
                <textarea name="content" class="form-control" rows="3" placeholder="Écrire votre réponse..." required></textarea>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                  <i class="fas fa-paper-plane"></i> Envoyer
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="toggleReplyForm(<?php echo $reply['id']; ?>)">
                  Annuler
                </button>
              </div>
            </form>
          </div>
          <?php endif; ?>

              <!-- ✅ Bouton Signaler (pour les autres utilisateurs) -->
    <?php if (!$isOwner ): ?>
    <a href="reportReply.php?id=<?php echo $reply['id']; ?>&forum_id=<?php echo $forum_id; ?>" 
       class="btn-like" 
       style="background:transparent;border:1px solid #ff9800;color:#ff9800"
       title="Signaler comme inapproprié">
        <i class="fas fa-flag"></i>
    </a>
    <?php endif; ?>
    
    <span class="meta">#<?php echo $reply['id']; ?></span>
</div>
          
          <!-- Réponses imbriquées -->
          <?php
          $nestedReplies = $replyC->getRepliesByParentId($reply['id']);
          if (count($nestedReplies) > 0):
          ?>
          <div class="nested-replies" style="margin-top:1rem">
            <?php foreach($nestedReplies as $nested): ?>
            <div class="nested-reply reply-card">
              <div class="d-flex justify-content-between">
                <div>
                  <strong><?php echo htmlspecialchars($nested['author']); ?></strong>
                  <span class="meta">• <?php echo date('d/m/Y à H:i', strtotime($nested['created_at'])); ?></span>
                </div>
                <div class="d-flex gap-2">
                  <?php
                  $nested_like_key = "liked_reply_" . $nested['id'];
                  $nested_has_liked = isset($_SESSION[$nested_like_key]);
                  $nested_like_class = $nested_has_liked ? 'btn-like liked' : 'btn-like';
                  ?>
                  <a href="like.php?reply_id=<?php echo $nested['id']; ?>&forum_id=<?php echo $forum_id; ?>" class="<?php echo $nested_like_class; ?>">
                    <i class="fas fa-heart"></i> <?php echo $nested['likes']; ?>
                  </a>
                  <a href="deleteReply.php?id=<?php echo $nested['id']; ?>&forum_id=<?php echo $forum_id; ?>" 
                     class="btn-like" style="background:transparent;border:1px solid #dc3545;color:#dc3545"
                     onclick="return confirm('Supprimer cette réponse ?')">
                    <i class="fas fa-trash"></i>
                  </a>
                </div>
              </div>
              <p style="margin-top:0.5rem;margin-bottom:0;white-space:pre-wrap"><?php echo htmlspecialchars($nested['content']); ?></p>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <?php
        }
        
        if (!$hasReplies) {
          echo '<p class="text-muted">Aucune réponse pour l\'instant. Soyez le premier à répondre !</p>';
        }
        ?>
      </div>
    </section>

    <!-- Formulaire de réponse principal -->
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
     <!-- ✅ NOUVEAU : Système de filtrage de contenu -->
  <script src="contentFilter.js"></script>
  <script>
// Fonction pour afficher/masquer le formulaire de réponse
function toggleReplyForm(replyId) {
  const form = document.getElementById('reply-form-' + replyId);
  if (form.style.display === 'none' || form.style.display === '') {
    // Masquer tous les autres formulaires
    document.querySelectorAll('.reply-form-inline').forEach(f => f.style.display = 'none');
    // Afficher le formulaire cliqué
    form.style.display = 'block';
    // Focus sur le textarea
    form.querySelector('textarea').focus();
  } else {
    form.style.display = 'none';
  }
}

// Afficher un message si un like a été effectué
const urlParams = new URLSearchParams(window.location.search);
const likeStatus = urlParams.get('like_status');

if (likeStatus === 'success') {
  const notification = document.createElement('div');
  notification.className = 'alert alert-success';
  notification.style.position = 'fixed';
  notification.style.top = '20px';
  notification.style.right = '20px';
  notification.style.zIndex = '9999';
  notification.innerHTML = '<i class="fas fa-heart"></i> Vous avez aimé cette réponse !';
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.remove();
    window.history.replaceState({}, '', window.location.pathname + '?id=<?php echo $forum_id; ?>');
  }, 3000);
} else if (likeStatus === 'already_liked') {
  const notification = document.createElement('div');
  notification.className = 'alert alert-warning';
  notification.style.position = 'fixed';
  notification.style.top = '20px';
  notification.style.right = '20px';
  notification.style.zIndex = '9999';
  notification.innerHTML = '<i class="fas fa-info-circle"></i> Vous avez déjà aimé cette réponse !';
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.remove();
    window.history.replaceState({}, '', window.location.pathname + '?id=<?php echo $forum_id; ?>');
  }, 3000);
}
  </script>
</body>
</html>