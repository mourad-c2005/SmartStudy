<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="views/front/assets/style.css">
    <title>Panier</title>
</head>
<body>

<h1>Votre panier</h1>

<?php if (empty($items)): ?>
    <p>Votre panier est vide.</p>

<?php else: ?>

<table>
    <tr>
        <th>Titre</th>
        <th>Prix</th>
        <th>Action</th>
    </tr>

    <?php foreach ($items as $it): ?>
    <tr>
        <td><?= htmlspecialchars($it['titre']) ?></td>
        <td><?= number_format($it['prix'], 2) ?> €</td>
        <td>
            <a class="btn delete" href="index.php?page=cart&delete=<?= $it['id'] ?>">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Total : <?= number_format($total, 2) ?> €</h3>

<a class="btn pay" href="index.php?page=pay">Payer maintenant</a>

<?php endif; ?>

<a class="btn" href="index.php?page=sections">⬅ Retour aux sections</a>

</body>
</html>
