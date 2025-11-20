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

// Suppression si on reçoit un ID
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $attemptModel->delete($id);
    header("Location: attempts.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tentatives des étudiants</title>
    <style>
        body { font-family: Arial; margin:20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border:1px solid #ddd; padding:10px; text-align:center; }
        th { background:#4CAF50; color:white; }
        a { text-decoration:none; color:#2196F3; }
        a:hover { text-decoration:underline; }
        .delete { color:#e53935; font-weight:bold; }
    </style>
</head>
<body>

<h2>Tentatives des étudiants</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Chapitre</th>
        <th>Élève</th>
        <th>Score</th>
        <th>Temps (sec)</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php if(!empty($attempts)): ?>
        <?php foreach($attempts as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['chapitre_titre'] ?? $a['id_chapitre']) ?></td>
                <td><?= htmlspecialchars($a['student_name']) ?></td>
                <td><?= $a['score'] ?>/<?= $a['total_questions'] ?></td>
                <td><?= $a['time_seconds'] ?></td>
                <td><?= $a['created_at'] ?></td>
                <td>
                    
                    <a href="attempts.php?delete=<?= $a['id'] ?>" class="delete" onclick="return confirm('Supprimer cette tentative ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7">Aucune tentative pour le moment.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
