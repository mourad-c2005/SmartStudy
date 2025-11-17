<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Panier</title>
</head>
<body>

<header>
    <h1>SmartStudy+</h1>
    <nav>
        <a href="index.php?page=sections">Sections</a>
        <a href="index.php?page=formations">Formations</a>
    </nav>
</header>

<h2>Votre Panier</h2>

<table>
    <tr>
        <th>Formation</th>
        <th>Prix</th>
        <th>Action</th>
    </tr>

    <?php foreach ($formations_panier as $f): ?>
    <tr>
        <td><?= $f['titre'] ?></td>
        <td><?= $f['prix'] ?> €</td>
        <td><a href="index.php?page=panier&action=remove&id=<?= $f['id_formation'] ?>"><button>Supprimer</button></a></td>
    </tr>
    <?php endforeach; ?>
</table>

<p class="total">Total : <?= $total ?> €</p>

<div style="text-align:center;">
    <button>Payer Maintenant</button>
</div>

<footer>© 2025 SmartStudy+</footer>

</body>
</html>
