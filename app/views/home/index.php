<?php 
// app/views/home/index.php
// $data['matieres'] contient toutes les matières
?>

<div class="welcome-card">
    <h1>Bienvenue sur <span style="color:#4CAF50;">SmartRevision+</span></h1>
    <p>
        Choisissez une matière puis un chapitre pour commencer votre plan de révision.<br>
        Chaque cours contient un support PDF, une vidéo explicative, et un quiz de 20 questions.
    </p>

    <a href="<?= URLROOT ?>/Matiere/index" class="btn-start">Voir les matières</a>
</div>

<div class="row g-4" style="margin-top: 25px;">
    <?php foreach ($data['matieres'] as $m): ?>
        <div class="col-md-3 col-sm-6">
            <a href="<?= URLROOT ?>/Chapitre/index/<?= urlencode($m['id']) ?>" class="text-decoration-none">
                <div class="module-card" style="
                    background:#fff;
                    border-radius:18px;
                    padding:20px;
                    box-shadow:0 4px 18px rgba(0,0,0,0.08);
                    transition:0.2s;
                    text-align:center;">
                    
                    <i class="fas fa-book fa-3x" style="color:#4CAF50;margin-bottom:10px;"></i>

                    <h5 style="color:#2e7d32;font-weight:700;margin-top:10px;">
                        <?= htmlspecialchars($m['nom']) ?>
                    </h5>

                    <p style="color:#555;font-size:0.9rem;">
                        <?= htmlspecialchars($m['description']) ?>
                    </p>

                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

</main>

<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
