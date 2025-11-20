<?php
// app/views/quiz/result.php

// sécuriser les variables pour éviter les erreurs
$cours = $data['cours'] ?? ['titre' => 'Cours inconnu', 'id_chapitre' => 0];
$score = $data['score'] ?? 0;
$total = $data['total'] ?? 0;
$time = $data['time_seconds'] ?? 0;
?>
<h2>Résultat du quiz : <?= htmlspecialchars($cours['titre']) ?></h2>

<p>Score : <?= $score ?> / <?= $total ?></p>
<p>Temps : <?= $time ?> secondes</p>

<a href="<?= URLROOT ?>/Cours/index/<?= $cours['id_chapitre'] ?>" class="btn-start">
    Revenir aux cours
</a>
