<?php
require_once('../../Controller/ForumController.php');
require_once('../../Controller/ReplyController.php');

$forumC = new ForumController();
$replyC = new ReplyController();
$db = ConfigForum::getConnexion();

// Statistiques globales
$totalForums = $db->query("SELECT COUNT(*) as count FROM forums")->fetch()['count'];
$totalReplies = $db->query("SELECT COUNT(*) as count FROM replies")->fetch()['count'];
$totalViews = $db->query("SELECT SUM(views) as total FROM forums")->fetch()['total'];
$totalLikes = $db->query("SELECT SUM(likes) as total FROM replies")->fetch()['total'];

// Forums les plus actifs
$mostActive = $db->query("
    SELECT f.title, f.category, COUNT(r.id) as reply_count, f.views
    FROM forums f
    LEFT JOIN replies r ON f.id = r.forum_id
    GROUP BY f.id
    ORDER BY reply_count DESC
    LIMIT 5
")->fetchAll();

// Utilisateurs les plus actifs
$topUsers = $db->query("
    SELECT author, COUNT(*) as post_count
    FROM (
        SELECT author FROM forums
        UNION ALL
        SELECT author FROM replies
    ) as all_posts
    GROUP BY author
    ORDER BY post_count DESC
    LIMIT 5
")->fetchAll();

// Activité par catégorie
$categoryStats = $db->query("
    SELECT category, COUNT(*) as forum_count, SUM(views) as total_views
    FROM forums
    GROUP BY category
    ORDER BY forum_count DESC
")->fetchAll();

// Évolution des posts (7 derniers jours)
$activityData = $db->query("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM (
        SELECT created_at FROM forums
        UNION ALL
        SELECT created_at FROM replies
    ) as all_posts
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques | SmartStudy+ Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root{--green:#4CAF50;--light:#E8F5E8}
        body{font-family:'Open Sans',sans-serif;background:var(--light)}
        .stat-card{background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 4px 12px rgba(0,0,0,0.08);text-align:center;transition:transform 0.3s}
        .stat-card:hover{transform:translateY(-5px);box-shadow:0 8px 20px rgba(0,0,0,0.12)}
        .stat-icon{font-size:3rem;margin-bottom:1rem}
        .stat-value{font-size:2.5rem;font-weight:bold;color:var(--green)}
        .stat-label{color:#666;font-size:0.9rem;text-transform:uppercase}
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-success" href="manageForums.php">
                <i class="fas fa-chart-line"></i> SmartStudy+ Statistiques
            </a>
            <div class="ms-auto">
                <a href="manageForums.php" class="btn btn-outline-success">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Statistiques globales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-primary"><i class="fas fa-comments"></i></div>
                    <div class="stat-value"><?php echo $totalForums; ?></div>
                    <div class="stat-label">Forums créés</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-success"><i class="fas fa-reply"></i></div>
                    <div class="stat-value"><?php echo $totalReplies; ?></div>
                    <div class="stat-label">Réponses totales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-info"><i class="fas fa-eye"></i></div>
                    <div class="stat-value"><?php echo number_format($totalViews); ?></div>
                    <div class="stat-label">Vues totales</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-danger"><i class="fas fa-heart"></i></div>
                    <div class="stat-value"><?php echo $totalLikes; ?></div>
                    <div class="stat-label">Likes totaux</div>
                </div>
            </div>
        </div>

        <!-- Graphique d'activité -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-area"></i> Activité des 7 derniers jours</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Forums les plus actifs -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-fire"></i> Top 5 Forums actifs</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($mostActive as $forum): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($forum['title']); ?></strong>
                                    <br><small class="text-muted"><?php echo $forum['category']; ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success"><?php echo $forum['reply_count']; ?> réponses</span>
                                    <br><small class="text-muted"><?php echo $forum['views']; ?> vues</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Utilisateurs les plus actifs -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Top 5 Contributeurs</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php 
                            $medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];
                            foreach ($topUsers as $index => $user): 
                            ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span style="font-size:1.5rem"><?php echo $medals[$index]; ?></span>
                                    <strong><?php echo htmlspecialchars($user['author']); ?></strong>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $user['post_count']; ?> posts
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques par catégorie -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> Statistiques par catégorie</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="60"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Graphique d'activité
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($activityData as $day): ?>
                        '<?php echo date('d/m', strtotime($day['date'])); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Posts créés',
                    data: [
                        <?php foreach ($activityData as $day): ?>
                            <?php echo $day['count']; ?>,
                        <?php endforeach; ?>
                    ],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Graphique par catégorie
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php foreach ($categoryStats as $cat): ?>
                        '<?php echo $cat['category']; ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Nombre de forums',
                    data: [
                        <?php foreach ($categoryStats as $cat): ?>
                            <?php echo $cat['forum_count']; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: [
                        'rgba(76, 175, 80, 0.8)',
                        'rgba(33, 150, 243, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(156, 39, 176, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>