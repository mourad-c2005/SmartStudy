<?php
// app/views/cours/add.php
// $data['chapitre']
?>
<h2>Ajouter un cours (chapitre: <?= htmlspecialchars($data['chapitre']['titre'] ?? '') ?>)</h2>
<form method="POST" class="form">
  <input type="hidden" name="id_chapitre" value="<?= htmlspecialchars($data['chapitre']['id']) ?>">
  <label>Titre</label>
  <input type="text" name="titre" required>
  <label>Contenu</label>
  <textarea name="contenu" rows="6"></textarea>
  <label>Lien vidéo (embed YouTube, ex: https://www.youtube.com/embed/ID)</label>
  <input type="text" name="lien_video" placeholder="https://www.youtube.com/embed/..." >
  <button class="btn-start" type="submit">Ajouter</button>
</form>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
