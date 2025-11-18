<?php
// app/views/chapitres/edit.php
// $data['chapitre']
?>
<h2>Modifier Chapitre</h2>
<form method="POST" class="form">
  <label>Titre</label>
  <input type="text" name="titre" value="<?= htmlspecialchars($data['chapitre']['titre']) ?>" required>
  <button class="btn-start" type="submit">Mettre à jour</button>
</form>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
