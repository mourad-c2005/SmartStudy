<?php
require_once('../../sessionHelper.php');
require_once('../../Controller/ReplyController.php');

$replyC = new ReplyController();
$error = '';
$reply = null;
$currentUser = getCurrentUser();

// Récupérer la réponse
if (isset($_GET['id'])) {
    $reply = $replyC->showReply($_GET['id']);
    
    // ✅ Vérifier que c'est bien l'auteur (par nom)
    if ($reply['author'] !== $currentUser) {
        die('❌ Vous ne pouvez modifier que vos propres réponses.');
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $forum_id = intval($_POST['forum_id']);
    
    if (!empty($_POST['content'])) {
        $replyObj = new Reply(
            $_POST['id'],
            $forum_id,
            $reply['parent_id'],
            $reply['author'], // Garder l'auteur original
            $_POST['content'],
            null,
            null,
            $reply['is_solution'],
            $reply['likes']
        );
        
        $result = $replyC->updateReply($replyObj, $_POST['id']);
        if ($result) {
            header("Location: thread.php?id=$forum_id&edit=success");
            exit();
        } else {
            $error = "Erreur lors de la modification.";
        }
    } else {
        $error = 'Le contenu ne peut pas être vide.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la réponse | SmartStudy+</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root{--green:#4CAF50;--light:#E8F5E8}
        body{font-family:'Open Sans',sans-serif;background:var(--light)}
        .card{border:none;box-shadow:0 4px 12px rgba(0,0,0,0.08);border-radius:12px}
    </style>
</head>
<body>
    <?php include('userSelector.php'); ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-edit"></i> Modifier votre réponse</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($reply): ?>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $reply['id']; ?>">
                            <input type="hidden" name="forum_id" value="<?php echo $_GET['forum_id']; ?>">

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Vous modifiez votre réponse en tant que <strong><?php echo htmlspecialchars($currentUser); ?></strong>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="8" required><?php echo htmlspecialchars($reply['content']); ?></textarea>
                            </div>

                            <div class="text-end">
                                <a href="thread.php?id=<?php echo $_GET['forum_id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-warning">Réponse introuvable.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>