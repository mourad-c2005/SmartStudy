<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Formations</title>
</head>
<body>

<header>
    <h1>SmartStudy+</h1>
    <nav>
        <a href="index.php?page=sections">Sections</a>
        <a href="index.php?page=panier">Panier</a>
    </nav>
</header>

<h2>Formations disponibles</h2>

<div class="grid">

<?php while ($row = $formations->fetch_assoc()): ?>
    <div class="formation-card">
        <img src="https://via.placeholder.com/300x180">
        <h3><?= $row['titre'] ?></h3>
        <p><?= $row['description'] ?></p>
        <span class="price"><?= $row['prix'] ?> €</span>

        <a href="index.php?page=panier&action=add&id=<?= $row['id_formation'] ?>">
            <button>Ajouter au Panier</button>
        </a>

        <a class="btn" href="<?= $row['lien'] ?>" target="_blank">Voir formation</a>
    </div>
<?php endwhile; ?>

</div>

<footer>© 2025 SmartStudy+</footer>

</body>
</html>
