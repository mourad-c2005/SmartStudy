<?php
require_once('../../Controller/ReplyController.php');

$replyC = new ReplyController();
$db = ConfigForum::getConnexion();

// Récupérer TOUTES les réponses avec infos du forum
$sql = "SELECT r.*, f.title as forum_title 
        FROM replies r
        JOIN forums f ON r.forum_id = f.id
        ORDER BY r.created_at DESC";
$replies = $db->query($sql);

// Gérer les actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reply_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'delete') {
        $replyC->deleteReply($reply_id);
    } elseif ($action === 'solution') {
        $replyC->markAsSolution($reply_id);
    } elseif ($action === 'unsolution') {
        $replyC->unmarkAsSolution($reply_id);
    }
    
    header('Location: manageReplies.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réponses | SmartStudy+ Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root{--green:#4CAF50;--light:#E8F5E8}
        body{font-family:'Open Sans',sans-serif;background:var(--light)}
        .navbar{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .card{border:none;box-shadow:0 4px 12px rgba(0,0,0,0.08);border-radius:12px}
        .reply-card{border-left:4px solid var(--green);padding:1rem;margin-bottom:1rem;background:#fff;border-radius:8px}
        .reply-card.solution{border-left-color:#28a745;background:#f0fff4}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" style="color:var(--green)" href="manageForums.php">
                <i class="fas fa-reply-all"></i> SmartStudy+ - Gestion Réponses
            </a>
            <div class="ms-auto">
                <a href="manageForums.php" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-comments"></i> Toutes les réponses 
                    <span class="badge bg-light text-dark"><?php echo $replies->rowCount(); ?></span>
                </h4>
            </div>
            <div class="card-body">
                <?php
                $hasReplies = false;
                foreach($replies as $reply) {
                    $hasReplies = true;
                    $cardClass = $reply['is_solution'] ? 'reply-card solution' : 'reply-card';
                ?>
                <div class="<?php echo $cardClass; ?>">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <strong><i class="fas fa-user"></i> <?php echo htmlspecialchars($reply['author']); ?></strong>
                                <?php if ($reply['is_solution']): ?>
                                <span class="badge bg-success"><i class="fas fa-check"></i> Solution</span>
                                <?php endif; ?>
                                <span class="badge bg-primary"><?php echo $reply['likes']; ?> ❤️</span>
                            </div>
                            
                            <p class="mb-2">
                                <strong><i class="fas fa-comments"></i> Forum :</strong> 
                                <a href="../FrontOffice/thread.php?id=<?php echo $reply['forum_id']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($reply['forum_title']); ?>
                                    <i class="fas fa-external-link-alt small"></i>
                                </a>
                            </p>
                            
                            <div class="alert alert-light mb-2">
                                <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                            </div>
                            
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                <?php echo date('d/m/Y à H:i', strtotime($reply['created_at'])); ?>
                            </small>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="d-flex flex-column gap-2">
                                <?php if (!$reply['is_solution']): ?>
                                <a href="?action=solution&id=<?php echo $reply['id']; ?>" 
                                   class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Marquer Solution
                                </a>
                                <?php else: ?>
                                <a href="?action=unsolution&id=<?php echo $reply['id']; ?>" 
                                   class="btn btn-secondary btn-sm">
                                    <i class="fas fa-times"></i> Retirer Solution
                                </a>
                                <?php endif; ?>
                                
                                <a href="?action=delete&id=<?php echo $reply['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Supprimer cette réponse ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                                
                                <a href="../FrontOffice/thread.php?id=<?php echo $reply['forum_id']; ?>" 
                                   target="_blank"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
                
                if (!$hasReplies) {
                    echo '<div class="text-center py-5">';
                    echo '<i class="fas fa-inbox fa-3x text-muted mb-3"></i>';
                    echo '<p class="text-muted">Aucune réponse pour le moment.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>