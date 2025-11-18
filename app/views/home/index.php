<?php
// app/views/home/index.php
// $data['matieres']
?>
<div class="welcome-card">
  <h1>Bienvenue sur SmartRevision+</h1>
  <p>Choisis une matière puis un chapitre pour commencer la révision. Les cours contiennent du texte et une vidéo de support. Chaque cours a un quiz de 20 questions.</p>
  <a href="<?= URLROOT ?>/Matiere/index" class="btn-start">Voir les matières</a>
</div>

<div class="row g-4">
  <?php foreach ($data['matieres'] as $m): ?>
    <div class="col-md-3">
      <a href="<?= URLROOT ?>/Chapitre/index/<?= $m['id'] ?>" class="text-decoration-none">
        <div class="module-card">
          <i class="fas fa-book fa-3x"></i>
          <h5><?= htmlspecialchars($m['nom']) ?></h5>
          <p><?= htmlspecialchars($m['description']) ?></p>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
