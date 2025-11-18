<?php
// app/views/matieres/edit.php
// $data['matiere']
?>
<h2>Modifier la matière</h2>
<form method="POST" class="form">
  <label>Nom</label>
  <input type="text" name="nom" value="<?= htmlspecialchars($data['matiere']['nom']) ?>" required>
  <label>Description</label>
  <textarea name="description"><?= htmlspecialchars($data['matiere']['description']) ?></textarea>
  <button class="btn-start" type="submit">Mettre à jour</button>
</form>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
