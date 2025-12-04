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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tentatives Quiz - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        a {
            color: #2196F3;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<h2>Liste des tentatives des étudiants</h2>

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
    <?php if (!empty($attempts)): ?>
        <?php foreach ($attempts as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['id_chapitre']) ?></td>
                <td><?= htmlspecialchars($a['student_name']) ?></td>
                <td><?= $a['score'] ?>/<?= $a['total_questions'] ?></td>
                <td><?= $a['time_seconds'] ?></td>
                <td><?= $a['created_at'] ?></td>
                <td>
                    <a href="answers.php?attempt_id=<?= $a['id'] ?>">Voir réponses</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7">Aucune tentative pour le moment.</td></tr>
    <?php endif; ?>
</table>

<?php include 'footer.php'; ?>

</body>
</html>
