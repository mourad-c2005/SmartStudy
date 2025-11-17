<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Catégories</title>
</head>
<body>

<header>
    <h1>SmartStudy+</h1>
    <nav>
        <a href="index.php?page=sections">Sections</a>
        <a href="index.php?page=panier">Panier</a>
    </nav>
</header>

<h2>Catégories de cette Section</h2>

<div class="grid">

<?php while ($row = $categories->fetch_assoc()): ?>
    <div class="card">
        <h3><?= $row['nom'] ?></h3>
        <a href="index.php?page=formations&id=<?= $row['id_categorie'] ?>">Voir Formations</a>
    </div>
<?php endwhile; ?>

</div>

<footer>© 2025 SmartStudy+</footer>

</body>
</html>
