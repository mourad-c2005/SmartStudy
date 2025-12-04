<?php
// app/views/cours/index.php
// $data['chapitre'], $data['cours']
?>
<h2>Cours du chapitre : <?= htmlspecialchars($data['chapitre']['titre']) ?></h2>
<a href="<?= URLROOT ?>/Cours/add/<?= $data['chapitre']['id'] ?>" class="btn-start">+ Ajouter un cours</a>

<table class="table-list">
<tr><th>ID</th><th>Titre</th><th>Actions</th></tr>
<?php foreach ($data['cours'] as $c): ?>
<tr>
  <td><?= $c['id'] ?></td>
  <td><?= htmlspecialchars($c['titre']) ?></td>
  <td>
    <a href="<?= URLROOT ?>/Cours/show/<?= $c['id'] ?>">Voir</a> |
    <a href="<?= URLROOT ?>/Cours/edit/<?= $c['id'] ?>">Modifier</a> |
    <a href="<?= URLROOT ?>/Cours/delete/<?= $c['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
  </td>
</tr>
<?php endforeach; ?>
</table>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
