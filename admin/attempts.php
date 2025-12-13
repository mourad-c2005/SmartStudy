<?php
session_start();
if (empty($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}

require_once "../config/config.php";
require_once "../app/core/Database.php";
require_once "../app/core/Model.php";
require_once "../app/models/Attempt.php";

$attemptModel = new Attempt();
$attempts = $attemptModel->getAllAttempts();

// Tri des attempts
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'score';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';

if (!empty($attempts)) {
    usort($attempts, function($a, $b) use ($sortBy, $order) {
        $aVal = $a[$sortBy] ?? 0;
        $bVal = $b[$sortBy] ?? 0;

        // Gestion numérique pour score et time_seconds
        if (in_array($sortBy, ['score', 'time_seconds'])) {
            $aVal = intval($aVal);
            $bVal = intval($bVal);
        }

        $cmp = ($aVal > $bVal) ? 1 : (($aVal < $bVal) ? -1 : 0);
        return ($order === 'desc') ? -$cmp : $cmp;
    });
}

// Fonction pour générer URL de tri avec toggle
function getSortUrl($column) {
    $currentSort = $_GET['sort'] ?? 'score';
    $currentOrder = $_GET['order'] ?? 'desc';
    $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
    return "attempts.php?sort=$column&order=$newOrder";
}

// Indicateur de tri (↑ ou ↓)
function getSortIndicator($column) {
    $currentSort = $_GET['sort'] ?? 'score';
    $currentOrder = $_GET['order'] ?? 'desc';
    if ($currentSort !== $column) return '';
    return ($currentOrder === 'asc') ? ' ↑' : ' ↓';
}

// Suppression si on reçoit un ID
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $attemptModel->delete($id);
    header("Location: attempts.php");
    exit;
}

// Calculer les statistiques
$stats = [
    'total' => count($attempts),
    'avgScore' => 0,
    'avgTime' => 0,
    'successRate' => 0,
    'totalQuestions' => 0,
    'totalTime' => 0
];

if (!empty($attempts)) {
    $totalScore = 0;
    $totalTime = 0;
    $successCount = 0;
    $totalQuestions = 0;

    foreach ($attempts as $a) {
        $totalScore += intval($a['score']);
        $totalTime += intval($a['time_seconds']);
        $totalQuestions += intval($a['total_questions']);
        // Considérer comme réussi si score >= 50% de total_questions
        if (intval($a['score']) >= intval($a['total_questions']) / 2) {
            $successCount++;
        }
    }

    $stats['avgScore'] = round($totalScore / count($attempts), 2);
    $stats['avgTime'] = round($totalTime / count($attempts), 0);
    $stats['successRate'] = round(($successCount / count($attempts)) * 100, 1);
    $stats['totalQuestions'] = $totalQuestions;
    $stats['totalTime'] = round($totalTime / 60, 1); // En minutes
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentatives des étudiants - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* CSS INTÉGRÉ POUR ATTEMPTS.PHP */
        :root {
            --green: #4CAF50;
            --yellow: #FFEB3B;
            --light: #E8F5E8;
            --white: #ffffff;
            --dark: #2e7d32;
            --blue: #2196F3;
            --red: #e53935;
        }
        
        * { 
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--light);
            color: #333;
            margin: 0;
            padding: 0;
            padding-bottom: 60px;
        }
        
        /* Header - Utilisez le même que dans header.php */
        
        .main-content {
            padding: 2rem 5%;
            min-height: calc(100vh - 70px - 60px);
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            color: var(--green);
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        /* Statistiques */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        
        .stat-card {
            background: var(--white);
            padding: 1.8rem;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            border-left: 5px solid var(--green);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            margin: 0 0 0.8rem 0;
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-card h3 i {
            color: var(--green);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1;
        }
        
        .stat-unit {
            font-size: 1rem;
            color: #999;
            margin-left: 0.3rem;
            font-weight: 400;
        }
        
        /* Table */
        .table-container {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            padding: 1.5rem;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        
        th {
            background: var(--green);
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 1rem;
            font-size: 0.95rem;
            border: none;
        }
        
        th a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: 0.3s;
        }
        
        th a:hover {
            opacity: 0.9;
        }
        
        th a .sort-icon {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            color: #555;
            font-size: 0.95rem;
        }
        
        tr:hover td {
            background: rgba(76, 175, 80, 0.05);
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        /* Badges et indicateurs */
        .score-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            min-width: 70px;
        }
        
        .score-high {
            background: #E8F5E9;
            color: var(--dark);
        }
        
        .score-medium {
            background: #FFF3E0;
            color: #F57C00;
        }
        
        .score-low {
            background: #FFEBEE;
            color: var(--red);
        }
        
        .time-badge {
            background: #E3F2FD;
            color: var(--blue);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        /* Actions */
        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .action-delete {
            background: #FFEBEE;
            color: var(--red);
            border: 1px solid #FFCDD2;
        }
        
        .action-delete:hover {
            background: var(--red);
            color: white;
            transform: translateY(-1px);
        }
        
        .action-view {
            background: #E8F5E9;
            color: var(--dark);
            border: 1px solid #C8E6C9;
        }
        
        .action-view:hover {
            background: var(--green);
            color: white;
            transform: translateY(-1px);
        }
        
        /* Message vide */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #888;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #666;
        }
        
        /* Filtres */
        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            background: var(--white);
            color: #666;
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid #eee;
            transition: all 0.3s;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--green);
            color: white;
            border-color: var(--green);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                padding: 1.5rem;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stat-value {
                font-size: 2rem;
            }
            
            .table-container {
                padding: 1rem;
            }
            
            th, td {
                padding: 0.8rem;
            }
        }
        
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .filter-btn {
                width: 100%;
                text-align: center;
            }
            
            .actions-cell {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-history"></i> Tentatives des étudiants</h1>
        <p>Consultez et gérez toutes les tentatives de quiz des étudiants</p>
    </div>

    <!-- Statistiques -->
    <div class="stats-container">
        <div class="stat-card">
            <h3><i class="fas fa-clipboard-list"></i> Total de tentatives</h3>
            <div class="stat-value"><?= $stats['total'] ?></div>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-chart-line"></i> Score moyen</h3>
            <div class="stat-value"><?= $stats['avgScore'] ?><span class="stat-unit">pts</span></div>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-clock"></i> Temps moyen</h3>
            <div class="stat-value"><?= $stats['avgTime'] ?><span class="stat-unit">sec</span></div>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-trophy"></i> Taux de réussite</h3>
            <div class="stat-value"><?= $stats['successRate'] ?><span class="stat-unit">%</span></div>
        </div>
    </div>

    <!-- Filtres rapides -->
    <div class="filters">
        <a href="<?= getSortUrl('score') ?>" class="filter-btn <?= ($_GET['sort'] ?? '') == 'score' ? 'active' : '' ?>">
            <i class="fas fa-sort-amount-down"></i> Par score
        </a>
        <a href="<?= getSortUrl('time_seconds') ?>" class="filter-btn <?= ($_GET['sort'] ?? '') == 'time_seconds' ? 'active' : '' ?>">
            <i class="fas fa-clock"></i> Par temps
        </a>
        <a href="<?= getSortUrl('created_at') ?>" class="filter-btn <?= ($_GET['sort'] ?? '') == 'created_at' ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i> Par date
        </a>
        <a href="<?= getSortUrl('student_name') ?>" class="filter-btn <?= ($_GET['sort'] ?? '') == 'student_name' ? 'active' : '' ?>">
            <i class="fas fa-user"></i> Par étudiant
        </a>
    </div>

    <!-- Table des tentatives -->
    <div class="table-container">
        <?php if(!empty($attempts)): ?>
            <table>
                <thead>
                    <tr>
                        <th><a href="<?= getSortUrl('id') ?>">ID<span class="sort-icon"><?= getSortIndicator('id') ?></span></a></th>
                        <th><a href="<?= getSortUrl('chapitre_titre') ?>">Chapitre<span class="sort-icon"><?= getSortIndicator('chapitre_titre') ?></span></a></th>
                        <th><a href="<?= getSortUrl('student_name') ?>">Élève<span class="sort-icon"><?= getSortIndicator('student_name') ?></span></a></th>
                        <th><a href="<?= getSortUrl('score') ?>">Score<span class="sort-icon"><?= getSortIndicator('score') ?></span></a></th>
                        <th><a href="<?= getSortUrl('time_seconds') ?>">Temps<span class="sort-icon"><?= getSortIndicator('time_seconds') ?></span></a></th>
                        <th><a href="<?= getSortUrl('created_at') ?>">Date<span class="sort-icon"><?= getSortIndicator('created_at') ?></span></a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($attempts as $a): 
                        // Déterminer la couleur du badge de score
                        $scorePercent = ($a['total_questions'] > 0) ? ($a['score'] / $a['total_questions']) * 100 : 0;
                        $scoreClass = $scorePercent >= 70 ? 'score-high' : ($scorePercent >= 50 ? 'score-medium' : 'score-low');
                    ?>
                        <tr>
                            <td><strong>#<?= $a['id'] ?></strong></td>
                            <td><?= htmlspecialchars($a['chapitre_titre'] ?? $a['id_chapitre']) ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fas fa-user-circle" style="color: var(--green);"></i>
                                    <?= htmlspecialchars($a['student_name']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="score-badge <?= $scoreClass ?>">
                                    <?= $a['score'] ?>/<?= $a['total_questions'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="time-badge">
                                    <i class="fas fa-clock"></i> <?= $a['time_seconds'] ?>s
                                </span>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt" style="color: #999; margin-right: 0.5rem;"></i>
                                <?= date('d/m/Y H:i', strtotime($a['created_at'])) ?>
                            </td>
                            <td class="actions-cell">
                                <a href="#" class="action-btn action-view">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <a href="attempts.php?delete=<?= $a['id'] ?>" 
                                   class="action-btn action-delete" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tentative ?');">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucune tentative</h3>
                <p>Les étudiants n'ont pas encore passé de quiz.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Animation simple pour les cartes stats
    document.addEventListener('DOMContentLoaded', function() {
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = (index * 0.1) + 's';
            card.classList.add('animate-in');
        });
    });
</script>

</body>
</html>