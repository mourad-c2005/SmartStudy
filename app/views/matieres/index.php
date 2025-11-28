<h1>Liste des matières</h1>

<?php if (!empty($_SESSION['admin_logged'])): ?>
    <a href="<?= URLROOT ?>/Matiere/add" class="btn-start">+ Ajouter une matière</a>
<?php endif; ?>

<table class="table-list">
    <tr>
        <th>ID</th><th>Nom</th><th>Description</th><th>Actions</th>
    </tr>

    <?php foreach ($data['matieres'] as $m): ?>
    <tr>
        <td><?= $m['id'] ?></td>
        <td><?= htmlspecialchars($m['nom']) ?></td>
        <td><?= htmlspecialchars($m['description']) ?></td>
        <td>
            <a href="<?= URLROOT ?>/Chapitre/index/<?= $m['id'] ?>">Chapitres</a>

            <?php if (!empty($_SESSION['admin_logged'])): ?>
                | <a href="<?= URLROOT ?>/Matiere/edit/<?= $m['id'] ?>">Modifier</a>
                | <a href="<?= URLROOT ?>/Matiere/delete/<?= $m['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
