<?php
// app/views/chapitres/index.php
// $data['matieres'], $data['chapitres'], $data['matiere']
?>
<h2>Chapitres de : <?= htmlspecialchars($data['matiere']['nom']) ?></h2>
<a href="<?= URLROOT ?>/Chapitre/add/<?= $data['matiere']['id'] ?>" class="btn-start">+ Ajouter un chapitre</a>

<table class="table-list">
  <tr><th>ID</th><th>Titre</th><th>Actions</th></tr>
  <?php foreach ($data['chapitres'] as $c): ?>
  <tr>
    <td><?= $c['id'] ?></td>
    <td><?= htmlspecialchars($c['titre']) ?></td>
    <td>
      <a href="<?= URLROOT ?>/Cours/index/<?= $c['id'] ?>">Cours</a> |
      <a href="<?= URLROOT ?>/Quiz/index/<?= $c['id'] ?>">Quiz</a> |
      <a href="<?= URLROOT ?>/Chapitre/edit/<?= $c['id'] ?>">Modifier</a> |
      <a href="<?= URLROOT ?>/Chapitre/delete/<?= $c['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
