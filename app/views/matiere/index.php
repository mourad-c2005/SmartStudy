<?php
// app/views/matieres/index.php
// $data['matieres']
?>
<h2>Liste des matières</h2>
<a href="<?= URLROOT ?>/Matiere/add" class="btn-start">+ Ajouter une matière</a>
<table class="table-list">
  <tr><th>ID</th><th>Nom</th><th>Description</th><th>Actions</th></tr>
  <?php foreach ($data['matieres'] as $m): ?>
  <tr>
    <td><?= $m['id'] ?></td>
    <td><?= htmlspecialchars($m['nom']) ?></td>
    <td><?= htmlspecialchars($m['description']) ?></td>
    <td>
      <a href="<?= URLROOT ?>/Chapitre/index/<?= $m['id'] ?>">Chapitres</a> |
      <a href="<?= URLROOT ?>/Matiere/edit/<?= $m['id'] ?>">Modifier</a> |
      <a href="<?= URLROOT ?>/Matiere/delete/<?= $m['id'] ?>" onclick="return confirm('Supprimer la matière ?')">Supprimer</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
