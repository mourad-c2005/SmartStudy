<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="views/front/assets/style.css">
    <title>Formations</title>
</head>
<body>

<h1>Formations disponibles</h1>

<div class="grid">

<?php foreach ($formations as $f): ?>
    <div class="card">

        <h3><?= htmlspecialchars($f['titre']) ?></h3>
        <p><?= htmlspecialchars($f['description']) ?></p>

        <p class="price"><?= number_format($f['prix'], 2) ?> €</p>

        <a class="btn" href="index.php?page=addToCart&id=<?= $f['id_formation'] ?>">
            Ajouter au panier
        </a>

        <br><br>

        <a class="btn" href="<?= $f['lien'] ?>" target="_blank">
            Voir la formation →
        </a>
    </div>
<?php endforeach; ?>

</div>

<a class="btn" href="index.php?page=categories&id=<?= $_GET['id'] ?>">⬅ Retour</a>

</body>
</html>
