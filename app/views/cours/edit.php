<?php
// app/views/cours/edit.php
// $data['cours']
$c = $data['cours'];
?>
<h2>Modifier le cours</h2>
<form method="POST" class="form">
  <label>Titre</label>
  <input type="text" name="titre" value="<?= htmlspecialchars($c['titre']) ?>" required>
  <label>Contenu</label>
  <textarea name="contenu" rows="6"><?= htmlspecialchars($c['contenu']) ?></textarea>
  <label>Lien vidéo (embed)</label>
  <input type="text" name="lien_video" value="<?= htmlspecialchars($c['lien_video']) ?>">
  <button class="btn-start" type="submit">Mettre à jour</button>
</form>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
