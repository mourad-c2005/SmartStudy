<?php
// app/views/cours/show.php
// $data['cours']
$c = $data['cours'];
?>
<div class="welcome-card">
  <h1><?= htmlspecialchars($c['titre']) ?></h1>
  <div class="cours-content"><?= nl2br(htmlspecialchars($c['contenu'])) ?></div>

  <?php if (!empty($c['lien_video'])): ?>
    <div class="video-wrap" style="margin-top:20px;">
      <iframe width="100%" height="420" src="<?= htmlspecialchars($c['lien_video']) ?>" frameborder="0" allowfullscreen></iframe>
    </div>
  <?php endif; ?>

  <a href="<?= URLROOT ?>/Quiz/index/<?= htmlspecialchars($c['id_chapitre']) ?>" class="btn-start mt-4">Faire le Quiz</a>
</div>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
