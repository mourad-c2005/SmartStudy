<?php
// app/views/matieres/add.php
?>
<h2>Ajouter une matière</h2>
<form method="POST" class="form">
  <label>Nom</label>
  <input type="text" name="nom" required>
  <label>Description</label>
  <textarea name="description"></textarea>
  <button class="btn-start" type="submit">Ajouter</button>
</form>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
