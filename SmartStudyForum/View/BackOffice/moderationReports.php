<?php
require_once('../../sessionHelper.php');
require_once('../../config.php');

$db = ConfigForum::getConnexion();

// Récupérer les signalements avec les infos
$sql = "SELECT r.*, rep.content as reply_content, rep.author as reply_author, f.title as forum_title, rep.forum_id
        FROM reports r
        JOIN replies rep ON r.reply_id = rep.id
        JOIN forums f ON rep.forum_id = f.id
        ORDER BY r.created_at DESC";
$reports = $db->query($sql);

// Gérer les actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $report_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'dismiss') {
        $sql = "UPDATE reports SET status = 'dismissed' WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $report_id]);
    } elseif ($action === 'approve') {
        // Supprimer la réponse signalée
        $sql = "SELECT reply_id FROM reports WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $report_id]);
        $report = $stmt->fetch();
        
        if ($report) {
            // Supprimer la réponse
            $sqlDel = "DELETE FROM replies WHERE id = :reply_id";
            $stmtDel = $db->prepare($sqlDel);
            $stmtDel->execute(['reply_id' => $report['reply_id']]);
            
            // Marquer comme traité
            $sqlUpdate = "UPDATE reports SET status = 'reviewed' WHERE id = :id";
            $stmtUpdate = $db->prepare($sqlUpdate);
            $stmtUpdate->execute(['id' => $report_id]);
        }
    }
    
    header('Location: moderationReports.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération | SmartStudy+ Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root{--green:#4CAF50;--light:#E8F5E8}
        body{font-family:'Open Sans',sans-serif;background:var(--light);margin:0;padding:0}
        .navbar{background:#fff;box-shadow:0 2px 10px rgba(0,0,0,0.1)}
        .card{border:none;box-shadow:0 4px 12px rgba(0,0,0,0.08);border-radius:12px}
        .report-card{border-left:4px solid #ff9800;padding:1.5rem;margin-bottom:1rem;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.05)}
        .report-card.pending{border-left-color:#ff9800;background:#fff8f0}
        .report-card.reviewed{border-left-color:#4caf50;background:#f1f8f4}
        .report-card.dismissed{border-left-color:#9e9e9e;background:#f5f5f5}
        .report-content{background:#f9f9f9;padding:1rem;border-radius:8px;margin:0.5rem 0;border-left:3px solid #ddd}
        .action-buttons{display:flex;gap:0.5rem;flex-direction:column}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" style="color:var(--green)" href="manageForums.php">
                <i class="fas fa-shield-alt"></i> SmartStudy+ Modération
            </a>
            <div class="ms-auto">
                <a href="manageForums.php" class="btn btn-outline-success me-2">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Signalements en attente -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-flag"></i> Signalements en attente
                    <?php
                    $pendingReports = array_filter(iterator_to_array($reports), function($r) { 
                        return $r['status'] === 'pending'; 
                    });
                    $pendingCount = count($pendingReports);
                    ?>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-danger"><?php echo $pendingCount; ?></span>
                    <?php endif; ?>
                </h4>
            </div>
            <div class="card-body">
                <?php
                $hasPending = false;
                // Reset le curseur
                $reports->execute();
                foreach ($reports as $report) {
                    if ($report['status'] === 'pending') {
                        $hasPending = true;
                ?>
                <div class="report-card pending">
                    <div class="row">
                        <div class="col-md-9">
                            <h6 class="text-danger mb-2">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Signalé par <strong><?php echo htmlspecialchars($report['reporter_name']); ?></strong>
                            </h6>
                            
                            <p class="mb-2">
                                <strong><i class="fas fa-comments"></i> Forum :</strong> 
                                <a href="../FrontOffice/thread.php?id=<?php echo $report['forum_id']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($report['forum_title']); ?>
                                    <i class="fas fa-external-link-alt small"></i>
                                </a>
                            </p>
                            
                            <p class="mb-2">
                                <strong><i class="fas fa-user"></i> Auteur de la réponse :</strong> 
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($report['reply_author']); ?></span>
                            </p>
                            
                            <div class="report-content">
                                <strong>Contenu signalé :</strong>
                                <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($report['reply_content'])); ?></p>
                            </div>
                            
                            <div class="alert alert-warning mt-2 mb-0">
                                <strong><i class="fas fa-info-circle"></i> Raison du signalement :</strong><br>
                                <?php echo nl2br(htmlspecialchars($report['reason'])); ?>
                            </div>
                            
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                Signalé le <?php echo date('d/m/Y à H:i', strtotime($report['created_at'])); ?>
                            </small>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="action-buttons">
                                <a href="?action=approve&id=<?php echo $report['id']; ?>" 
                                   class="btn btn-danger btn-sm w-100"
                                   onclick="return confirm('⚠️ Confirmer la suppression de cette réponse ?\n\nCette action est irréversible.')">
                                    <i class="fas fa-trash-alt"></i> Supprimer la réponse
                                </a>
                                <a href="?action=dismiss&id=<?php echo $report['id']; ?>" 
                                   class="btn btn-secondary btn-sm w-100"
                                   onclick="return confirm('Rejeter ce signalement ?\n\nLa réponse ne sera pas supprimée.')">
                                    <i class="fas fa-times"></i> Rejeter le signalement
                                </a>
                                <a href="../FrontOffice/thread.php?id=<?php echo $report['forum_id']; ?>" 
                                   target="_blank"
                                   class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-eye"></i> Voir le topic
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    }
                }
                
                if (!$hasPending) {
                    echo '<div class="text-center py-5">';
                    echo '<i class="fas fa-check-circle fa-3x text-success mb-3"></i>';
                    echo '<p class="text-muted">Aucun signalement en attente. Tout est propre ! ✨</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- Historique -->
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Historique des signalements traités</h5>
            </div>
            <div class="card-body">
                <?php
                $hasHistory = false;
                $reports->execute(); // Reset
                foreach ($reports as $report) {
                    if ($report['status'] !== 'pending') {
                        $hasHistory = true;
                        $statusClass = $report['status'] === 'reviewed' ? 'reviewed' : 'dismissed';
                        $statusText = $report['status'] === 'reviewed' ? 'Supprimé' : 'Rejeté';
                        $statusIcon = $report['status'] === 'reviewed' ? 'check' : 'times';
                        $statusBg = $report['status'] === 'reviewed' ? 'success' : 'secondary';
                ?>
                <div class="report-card <?php echo $statusClass; ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div style="flex:1">
                            <span class="badge bg-<?php echo $statusBg; ?> me-2">
                                <i class="fas fa-<?php echo $statusIcon; ?>"></i> <?php echo $statusText; ?>
                            </span>
                            <strong><?php echo htmlspecialchars($report['forum_title']); ?></strong>
                            <br>
                            <small class="text-muted">
                                Signalé par <strong><?php echo htmlspecialchars($report['reporter_name']); ?></strong> 
                                • <?php echo date('d/m/Y', strtotime($report['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php
                    }
                }
                
                if (!$hasHistory) {
                    echo '<p class="text-muted text-center py-3">Aucun historique disponible.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <div style="height:50px"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>