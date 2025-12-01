<?php
require_once('../../Controller/ForumController.php');

$error = '';
$forum = null;
$forumC = new ForumController();

// Récupérer le forum à modifier
if (isset($_GET['id'])) {
    $forum = $forumC->showForum($_GET['id']);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    if (
        !empty($_POST['id']) && !empty($_POST['title']) && 
        !empty($_POST['category']) && !empty($_POST['author']) && !empty($_POST['content'])
    ) {
        $forumObj = new Forum(
            $_POST['id'],
            $_POST['title'],
            $_POST['category'],
            $_POST['author'],
            $_POST['content'],
            null,
            null,
            null,
            isset($_POST['is_pinned']),
            isset($_POST['is_locked'])
        );
        
        $result = $forumC->updateForum($forumObj, $_POST['id']);
        if ($result) {
            header('Location: manageForums.php');
            exit();
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
    } else {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStudy+ | Modifier un Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root{--green:#4CAF50;--light:#E8F5E8}
        body{font-family:'Open Sans',sans-serif;background:var(--light)}
        .navbar{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .card{border:none;box-shadow:0 4px 12px rgba(0,0,0,0.08);border-radius:12px}
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" style="color:var(--green)" href="manageForums.php">
                <i class="fas fa-comments"></i> SmartStudy+ Admin
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-edit"></i> Modifier le forum</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($forum): ?>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $forum['id']; ?>">

                            <!-- Titre -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($forum['title']); ?>" required>
                            </div>

                            <!-- Catégorie -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="Planning" <?php echo $forum['category'] === 'Planning' ? 'selected' : ''; ?>>Planning</option>
                                    <option value="Méthodes" <?php echo $forum['category'] === 'Méthodes' ? 'selected' : ''; ?>>Méthodes</option>
                                    <option value="Technique" <?php echo $forum['category'] === 'Technique' ? 'selected' : ''; ?>>Technique</option>
                                    <option value="Général" <?php echo $forum['category'] === 'Général' ? 'selected' : ''; ?>>Général</option>
                                </select>
                            </div>

                            <!-- Auteur -->
                            <div class="mb-3">
                                <label for="author" class="form-label">Auteur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="author" name="author" 
                                       value="<?php echo htmlspecialchars($forum['author']); ?>" required>
                            </div>

                            <!-- Contenu -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="8" required><?php echo htmlspecialchars($forum['content']); ?></textarea>
                            </div>

                            <!-- Options -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_pinned" name="is_pinned" 
                                           <?php echo $forum['is_pinned'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_pinned">
                                        <i class="fas fa-thumbtack text-warning"></i> Épingler ce sujet
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_locked" name="is_locked"
                                           <?php echo $forum['is_locked'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_locked">
                                        <i class="fas fa-lock text-danger"></i> Verrouiller ce sujet
                                    </label>
                                </div>
                            </div>

                            <!-- Boutons -->
                            <div class="text-end">
                                <a href="manageForums.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Forum introuvable.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>