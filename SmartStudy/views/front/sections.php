<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="views/front/assets/style.css">
    <title>Sections</title>
</head>

<body>

<h1>Choisissez une Section</h1>

<?php foreach ($sections as $s): ?>
    <h2><?= htmlspecialchars($s['nom']) ?></h2>

    <a class="btn" href="index.php?page=categories&id=<?= $s['id']; ?>">
        Voir catégories
    </a>

    <hr>
<?php endforeach; ?>

</body>
</html>
