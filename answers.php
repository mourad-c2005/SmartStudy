<?php
session_start();
if (empty($_SESSION['admin_logged'])) { header('Location: login.php'); exit; }

require_once "../config/config.php";
require_once "../app/core/Database.php";
require_once "../app/core/Model.php";
require_once "../app/models/Attempt.php";

$attemptModel = new Attempt();
$attempt_id = intval($_GET['attempt_id'] ?? 0);

if (!$attempt_id) { echo "Tentative introuvable"; exit; }

$answers = $attemptModel->getAnswers($attempt_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réponses d'une tentative</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<h2>Réponses de la tentative #<?= $attempt_id ?></h2>

<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>Question</th>
    <th>Réponses</th>
    <th>Réponse choisie</th>
    <th>Bonne réponse</th>
</tr>

<?php foreach ($answers as $a): ?>
<tr>
    <td><?= htmlspecialchars($a['question']) ?></td>
    <td>
        A) <?= htmlspecialchars($a['rep1']) ?><br>
        B) <?= htmlspecialchars($a['rep2']) ?><br>
        C) <?= htmlspecialchars($a['rep3']) ?><br>
        D) <?= htmlspecialchars($a['rep4']) ?>
    </td>
    <td>
        <?php
        if ($a['selected'] === null) {
            echo "-";
        } else {
            echo ["A","B","C","D"][$a['selected']];
        }
        ?>
    </td>
    <td><?= ["A","B","C","D"][$a['correcte']] ?></td>
</tr>
<?php endforeach; ?>

</table>
<?php include 'footer.php'; ?>
</body>
</html>
