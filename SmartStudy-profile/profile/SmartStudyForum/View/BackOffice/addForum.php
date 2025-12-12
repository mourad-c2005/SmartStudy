<?php
require_once('../../Controller/ForumController.php');

$error = "";
$forumC = new ForumController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST["title"], $_POST["category"], $_POST["author"], $_POST["content"]) &&
        !empty($_POST["title"]) && !empty($_POST["category"]) && !empty($_POST["author"]) && !empty($_POST["content"])
    ) {
        $forum = new Forum(
            null,
            $_POST['title'],
            $_POST['category'],
            $_POST['author'],
            $_POST['content'],
            null,
            null,
            0,
            isset($_POST['is_pinned']),
            isset($_POST['is_locked'])
        );
        
        $result = $forumC->addForum($forum);
        if ($result) {
            header('Location: manageForums.php');
            exit;
        } else {
            $error = "Erreur lors de l'ajout du forum.";
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStudy+ | Ajouter un Forum</title>
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
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-plus"></i> Créer un nouveau forum</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="addForumForm">
                            <!-- Titre -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="Ex: Comment organiser mes sessions de révision ?" required>
                                <span id="title_error" class="text-sm"></span>
                            </div>

                            <!-- Catégorie -->
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">-- Choisir une catégorie --</option>
                                    <option value="Planning">Planning</option>
                                    <option value="Méthodes">Méthodes</option>
                                    <option value="Technique">Technique</option>
                                    <option value="Général">Général</option>
                                </select>
                                <span id="category_error" class="text-sm"></span>
                            </div>

                            <!-- Auteur -->
                            <div class="mb-3">
                                <label for="author" class="form-label">Auteur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="author" name="author" 
                                       placeholder="Nom de l'auteur" required>
                                <span id="author_error" class="text-sm"></span>
                            </div>

                            <!-- Contenu -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="8" 
                                          placeholder="Décrivez votre question ou sujet..." required></textarea>
                                <span id="content_error" class="text-sm"></span>
                            </div>

                            <!-- Options -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_pinned" name="is_pinned">
                                    <label class="form-check-label" for="is_pinned">
                                        <i class="fas fa-thumbtack text-warning"></i> Épingler ce sujet
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_locked" name="is_locked">
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
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Créer le forum
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="forumValidation.js"></script>
</body>
</html>