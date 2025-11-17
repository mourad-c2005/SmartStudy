<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="views/front/assets/style.css">
    <title>Catégories</title>
</head>
<body>

<h1>Catégories</h1>

<div class="grid">

<?php foreach ($categories as $c): ?>
    <div class="card">
        <h3><?= htmlspecialchars($c['nom']) ?></h3>

        <a class="btn" href="index.php?page=formations&id=<?= $c['id'] ?>">
            Voir Formations
        </a>
    </div>
<?php endforeach; ?>

</div>

<a class="btn" href="index.php?page=sections">⬅ Retour</a>

</body>
</html>
