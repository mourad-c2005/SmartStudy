<h1 style="text-align:center; margin-bottom:25px;">
    Chapitres – <span style="color:#4CAF50;"><?= htmlspecialchars($data['matiere']['nom']) ?></span>
</h1>

<?php if (!empty($_SESSION['admin_logged'])): ?>
    <div style="text-align:center; margin-bottom:20px;">
        <a href="<?= URLROOT ?>/Chapitre/add/<?= $data['matiere']['id'] ?>" 
           class="btn-start">+ Ajouter un chapitre</a>
    </div>
<?php endif; ?>

<div class="row g-4" style="padding:0 20px;">

    <?php foreach ($data['chapitres'] as $c): ?>
    <div class="col-md-4 col-sm-6">
        <div class="chapter-card" style="
            background:#fff;
            border-radius:18px;
            padding:20px;
            box-shadow:0 4px 18px rgba(0,0,0,0.08);
            transition:0.2s;
            text-align:center;
        ">
            <i class="fas fa-folder-open fa-3x" style="color:#4CAF50;"></i>

            <h4 style="margin-top:15px; color:#2e7d32;">
                <?= htmlspecialchars($c['titre']) ?>
            </h4>

            <div style="margin-top:15px;">
                <!-- Voir cours -->
                <a href="<?= URLROOT ?>/Cours/index/<?= urlencode($c['id']) ?>" 
                   class="btn-start" 
                   style="padding:5px 12px; border-radius:20px; font-size:0.9rem;">
                    📘 Cours
                </a>

                <!-- Quiz étudiant -->
                <a href="<?= URLROOT ?>/Quiz/index/<?= urlencode($c['id']) ?>" 
                   class="btn-start" 
                   style="background:#2196F3; padding:5px 12px; border-radius:20px; font-size:0.9rem;">
                    📝 Quiz
                </a>
            </div>

            <?php if (!empty($_SESSION['admin_logged'])): ?>
            <div style="margin-top:15px;">
                <a href="<?= URLROOT ?>/Chapitre/edit/<?= urlencode($c['id']) ?>" 
                   style="color:#ff9800; margin-right:10px; font-weight:bold;">
                   ✏ Modifier
                </a>

                <a href="<?= URLROOT ?>/Chapitre/delete/<?= urlencode($c['id']) ?>" 
                   onclick="return confirm('Supprimer ce chapitre ?');"
                   style="color:#e53935; font-weight:bold;">
                   ❌ Supprimer
                </a>
            </div>
            <?php endif; ?>

        </div>
    </div>
    <?php endforeach; ?>

</div>
