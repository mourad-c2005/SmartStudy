<?php
require_once('../../Controller/ForumController.php');

$forumC = new ForumController();
$list = $forumC->listForums();

// Gérer les actions (épingler, verrouiller)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'pin') {
        $forumC->togglePin($id);
    } elseif ($_GET['action'] === 'lock') {
        $forumC->toggleLock($id);
    }
    header('Location: manageForums.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStudy+ | Gestion des Forums</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root{--green:#4CAF50;--light:#E8F5E8}
        body{font-family:'Open Sans',sans-serif;background:var(--light)}
        .navbar{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .card{border:none;box-shadow:0 4px 12px rgba(0,0,0,0.08);border-radius:12px}
        .table-hover tbody tr:hover{background:#f8fff8}
        .btn-action{padding:0.25rem 0.5rem;font-size:0.85rem;border-radius:6px}
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" style="color:var(--green)" href="../FrontOffice/forums.php">
            <i class="fas fa-comments"></i> SmartStudy+ Admin
        </a>
        <div class="ms-auto d-flex gap-2">
            <?php
            $db = ConfigForum::getConnexion();
            $sql = "SELECT COUNT(*) as count FROM reports WHERE status = 'pending'";
            $result = $db->query($sql)->fetch();
            $pendingCount = $result['count'];
            ?>
            <a href="moderationReports.php" class="btn btn-warning">
                <i class="fas fa-flag"></i> Signalements
                <?php if ($pendingCount > 0): ?>
                <span class="badge bg-danger"><?php echo $pendingCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="../FrontOffice/forums.php" class="btn btn-outline-success">
                <i class="fas fa-home"></i> Forum
            </a>
        </div>
    </div>
</nav>

    <div class="container">
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-list"></i> Gestion des Forums</h4>
                <a href="addForum.php" class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Nouveau Forum
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Auteur</th>
                                <th>Date</th>
                                <th>Vues</th>
                                <th>Réponses</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($list as $forum) {
                                $repliesCount = $forumC->countReplies($forum['id']);
                            ?>
                            <tr>
                                <td><?php echo $forum['id']; ?></td>
                                <td>
                                    <a href="../FrontOffice/thread.php?id=<?php echo $forum['id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($forum['title']); ?>
                                    </a>
                                    <?php if ($forum['is_pinned']): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-thumbtack"></i></span>
                                    <?php endif; ?>
                                    <?php if ($forum['is_locked']): ?>
                                        <span class="badge bg-danger"><i class="fas fa-lock"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-success"><?php echo htmlspecialchars($forum['category']); ?></span></td>
                                <td><?php echo htmlspecialchars($forum['author']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($forum['created_at'])); ?></td>
                                <td><i class="fas fa-eye text-muted"></i> <?php echo $forum['views']; ?></td>
                                <td><i class="fas fa-comments text-muted"></i> <?php echo $repliesCount; ?></td>
                                <td>
                                    <?php if ($forum['is_locked']): ?>
                                        <span class="badge bg-danger">Verrouillé</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="?action=pin&id=<?php echo $forum['id']; ?>" 
                                           class="btn btn-action <?php echo $forum['is_pinned'] ? 'btn-warning' : 'btn-outline-warning'; ?>" 
                                           title="<?php echo $forum['is_pinned'] ? 'Désépingler' : 'Épingler'; ?>">
                                            <i class="fas fa-thumbtack"></i>
                                        </a>
                                        <a href="?action=lock&id=<?php echo $forum['id']; ?>" 
                                           class="btn btn-action <?php echo $forum['is_locked'] ? 'btn-danger' : 'btn-outline-danger'; ?>" 
                                           title="<?php echo $forum['is_locked'] ? 'Déverrouiller' : 'Verrouiller'; ?>">
                                            <i class="fas fa-lock"></i>
                                        </a>
                                        <a href="updateForum.php?id=<?php echo $forum['id']; ?>" class="btn btn-action btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="deleteForum.php?id=<?php echo $forum['id']; ?>" 
                                           class="btn btn-action btn-outline-danger"
                                           onclick="return confirm('Voulez-vous vraiment supprimer ce forum ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-comments fa-3x text-success mb-3"></i>
                        <h3><?php echo $list->rowCount(); ?></h3>
                        <p class="text-muted">Total Forums</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-thumbtack fa-3x text-warning mb-3"></i>
                        <?php
                        $pinnedCount = 0;
                        foreach($forumC->listForums() as $f) {
                            if ($f['is_pinned']) $pinnedCount++;
                        }
                        ?>
                        <h3><?php echo $pinnedCount; ?></h3>
                        <p class="text-muted">Forums Épinglés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-lock fa-3x text-danger mb-3"></i>
                        <?php
                        $lockedCount = 0;
                        foreach($forumC->listForums() as $f) {
                            if ($f['is_locked']) $lockedCount++;
                        }
                        ?>
                        <h3><?php echo $lockedCount; ?></h3>
                        <p class="text-muted">Forums Verrouillés</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>