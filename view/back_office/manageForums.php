<?php
// En haut de SmartStudyForum/View/BackOffice/manageForums.php
session_start();

// ✅ Vérifier si admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../../../view/login.php');
    exit();
}
require_once('../../Controller/ForumController.php');

$forumC = new ForumController();
$list = $forumC->listForums();

// Convertir le PDOStatement en tableau
$forumsArray = $list->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Gestion des Forums - Admin SmartStudy+</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background: #F8FFF8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .top-nav {
            background: #fff;
            padding: 16px 5%;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
            overflow: hidden;
        }
        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: #4CAF50;
            text-decoration: none;
            float: left;
            line-height: 42px;
        }
        .nav-actions {
            float: right;
            overflow: hidden;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        .btn-stats {
            background: #2196F3;
            color: white;
        }
        .btn-stats:hover {
            background: #1976D2;
        }
        .btn-reports {
            background: #FF9800;
            color: white;
        }
        .btn-reports:hover {
            background: #F57C00;
        }
        .btn-forum {
            background: #4CAF50;
            color: white;
        }
        .btn-forum:hover {
            background: #45a049;
        }
        .badge-count {
            background: #f44336;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 5px;
        }
        .main {
            padding: 32px 5%;
            clear: both;
        }
        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #4CAF50;
            margin-bottom: 16px;
        }
        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            padding: 24px;
            margin-bottom: 32px;
        }
        .card-header {
            overflow: hidden;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .card-header h3 {
            float: left;
            margin: 0;
            font-size: 1.4rem;
            color: #333;
            font-family: 'Montserrat', sans-serif;
        }
        .btn-add {
            float: right;
            background: #2e7d32;
            color: white;
            padding: 10px 20px;
        }
        .btn-add:hover {
            background: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        table th {
            color: #4CAF50;
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid #4CAF50;
            font-weight: 600;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover td {
            background: #f9fff9;
        }
        .table-link {
            color: #333;
            text-decoration: none;
            font-weight: 600;
        }
        .table-link:hover {
            color: #4CAF50;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-right: 5px;
        }
        .badge-category {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .badge-pinned {
            background: #fff3e0;
            color: #F57C00;
        }
        .badge-locked {
            background: #ffebee;
            color: #c62828;
        }
        .badge-active {
            background: #e8f5e8;
            color: #2e7d32;
        }
        .btn-action {
            background: #2196F3;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin: 2px;
            font-size: 0.9rem;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-action:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }
        .btn-pin {
            background: #FF9800;
        }
        .btn-pin:hover {
            background: #F57C00;
        }
        .btn-lock {
            background: #f44336;
        }
        .btn-lock:hover {
            background: #d32f2f;
        }
        .btn-edit {
            background: #2196F3;
        }
        .btn-edit:hover {
            background: #1976D2;
        }
        .btn-delete {
            background: #757575;
        }
        .btn-delete:hover {
            background: #616161;
        }
        .stats-container {
            overflow: hidden;
            margin-bottom: 32px;
        }
        .stat-card {
            float: left;
            width: 30%;
            margin-right: 5%;
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .stat-card:last-child {
            margin-right: 0;
        }
        .stat-card h5 {
            margin: 0 0 16px 0;
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-card h3 {
            margin: 0 0 8px 0;
            font-size: 2rem;
            color: #4CAF50;
        }
        .stat-card p {
            margin: 0;
            color: #999;
            font-size: 0.9rem;
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #4CAF50;
        }
        .stat-icon-warning {
            color: #FF9800;
        }
        .stat-icon-danger {
            color: #f44336;
        }
        .clear {
            clear: both;
        }
        .table-container {
            overflow-x: auto;
        }
        footer {
            background: #222;
            color: #ccc;
            text-align: center;
            padding: 24px;
            font-size: 0.9rem;
            clear: both;
            margin-top: 40px;
        }
        footer strong {
            color: #FFEB3B;
        }
        @media (max-width: 768px) {
            .top-nav {
                padding: 12px 4%;
            }
            .logo {
                float: none;
                display: block;
                text-align: center;
                margin-bottom: 10px;
            }
            .nav-actions {
                float: none;
                text-align: center;
            }
            .btn {
                margin: 5px;
                padding: 6px 12px;
            }
            .main {
                padding: 20px 4%;
            }
            .card {
                padding: 16px;
            }
            .stat-card {
                float: none;
                width: 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }
            .stat-card:last-child {
                margin-bottom: 0;
            }
            .table-container {
                margin: 0 -16px;
                padding: 0 16px;
            }
            table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>

<!-- Top Navigation -->
<div class="top-nav">
    <a href="index.php" class="logo">
        <i class="fas fa-comments"></i> SmartStudy+ Admin
    </a>
    
    <div class="nav-actions">
        <?php
        // Compteur de signalements en attente
        $db = ConfigForum::getConnexion();
        $sql = "SELECT COUNT(*) as count FROM reports WHERE status = 'pending'";
        $result = $db->query($sql)->fetch();
        $pendingCount = $result['count'] ?? 0;
        ?>
        
        <a href="statistics.php" class="btn btn-stats">
            <i class="fas fa-chart-bar"></i> Statistiques
        </a>
        
        <a href="moderationReports.php" class="btn btn-reports">
            <i class="fas fa-flag"></i> Signalements
            <?php if ($pendingCount > 0): ?>
                <span class="badge-count"><?php echo $pendingCount; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="index.php" class="btn btn-forum">
            <i class="fas fa-home"></i> 
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="main">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Gestion des Forums</h3>
            <a href="addForum.php" class="btn btn-add">
                <i class="fas fa-plus"></i> Nouveau Forum
            </a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Auteur</th>
                        <th>Date</th>
                        <th>Vues</th>
                        <th>Réponses</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($forumsArray)): ?>
                        <?php foreach($forumsArray as $forum): 
                            $repliesCount = $forumC->countReplies($forum['id']);
                        ?>
                        <tr>
                            <td><strong>#<?php echo $forum['id']; ?></strong></td>
                            <td>
                                <a href="../FrontOffice/thread.php?id=<?php echo $forum['id']; ?>" class="table-link">
                                    <?php echo htmlspecialchars($forum['title']); ?>
                                </a>
                                <div style="margin-top: 5px;">
                                    <?php if ($forum['is_pinned']): ?>
                                        <span class="badge badge-pinned">
                                            <i class="fas fa-thumbtack"></i> Épinglé
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($forum['is_locked']): ?>
                                        <span class="badge badge-locked">
                                            <i class="fas fa-lock"></i> Verrouillé
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-category">
                                    <?php echo htmlspecialchars($forum['category']); ?>
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-user" style="color: #666; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($forum['author']); ?>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt" style="color: #666; margin-right: 5px;"></i>
                                <?php echo date('d/m/Y', strtotime($forum['created_at'])); ?>
                            </td>
                            <td>
                                <i class="fas fa-eye" style="color: #666; margin-right: 5px;"></i>
                                <?php echo $forum['views']; ?>
                            </td>
                            <td>
                                <i class="fas fa-comments" style="color: #666; margin-right: 5px;"></i>
                                <?php echo $repliesCount; ?>
                            </td>
                            <td>
                                <?php if ($forum['is_locked']): ?>
                                    <span class="badge badge-locked">
                                        <i class="fas fa-lock"></i> Verrouillé
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-active">
                                        <i class="fas fa-check-circle"></i> Actif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div>
                                    <a href="?action=pin&id=<?php echo $forum['id']; ?>" 
                                       class="btn-action btn-pin" 
                                       title="<?php echo $forum['is_pinned'] ? 'Désépingler' : 'Épingler'; ?>">
                                        <i class="fas fa-thumbtack"></i>
                                    </a>
                                    <a href="?action=lock&id=<?php echo $forum['id']; ?>" 
                                       class="btn-action btn-lock" 
                                       title="<?php echo $forum['is_locked'] ? 'Déverrouiller' : 'Verrouiller'; ?>">
                                        <i class="fas fa-lock"></i>
                                    </a>
                                    <a href="updateForum.php?id=<?php echo $forum['id']; ?>" 
                                       class="btn-action btn-edit"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="deleteForum.php?id=<?php echo $forum['id']; ?>" 
                                       class="btn-action btn-delete"
                                       title="Supprimer"
                                       onclick="return confirm('Voulez-vous vraiment supprimer ce forum ? Cette action est irréversible.');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: #666;">
                                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; display: block; color: #ddd;"></i>
                                <p>Aucun forum disponible pour le moment.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-container">
        <?php
        // Calcul des statistiques
        $totalForums = count($forumsArray);
        
        $pinnedCount = 0;
        $lockedCount = 0;
        foreach($forumsArray as $f) {
            if (isset($f['is_pinned']) && $f['is_pinned']) $pinnedCount++;
            if (isset($f['is_locked']) && $f['is_locked']) $lockedCount++;
        }
        ?>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <h3><?php echo $totalForums; ?></h3>
            <h5>Forums Totaux</h5>
            <p><?php echo $totalForums; ?> forums créés</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon stat-icon-warning">
                <i class="fas fa-thumbtack"></i>
            </div>
            <h3><?php echo $pinnedCount; ?></h3>
            <h5>Forums Épinglés</h5>
            <p><?php echo $pinnedCount; ?> forums prioritaires</p>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon stat-icon-danger">
                <i class="fas fa-lock"></i>
            </div>
            <h3><?php echo $lockedCount; ?></h3>
            <h5>Forums Verrouillés</h5>
            <p><?php echo $lockedCount; ?> forums fermés</p>
        </div>
        <div class="clear"></div>
    </div>
</div>

<footer>
    <p>SmartStudy+ © 2025 – Nature • Croissance • Sérénité</p>
    <p>Développé par <strong>BLUEPIXEL 2032</strong></p>
</footer>

</body>
</html>