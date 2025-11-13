<?php
// app/views/chapitres/add.php
// $data['matiere']
?>
<h2>Ajouter un chapitre pour <?= htmlspecialchars($data['matiere']['nom'] ?? '') ?></h2>
<form method="POST" class="form">
  <input type="hidden" name="id_matiere" value="<?= htmlspecialchars($data['matiere']['id']) ?>">
  <label>Titre</label>
  <input type="text" name="titre" required>
  <button class="btn-start" type="submit">Ajouter</button>
</form>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
