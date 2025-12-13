<h1 style="text-align:center; margin-bottom:25px;">
    Cours – <span style="color:#4CAF50;"><?= htmlspecialchars($data['chapitre']['titre']) ?></span>
</h1>

<?php if (!empty($_SESSION['admin_logged']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
    <div style="text-align:center; margin-bottom:20px;">
        <a href="<?= URLROOT ?>/Cours/add/<?= urlencode($data['chapitre']['id']) ?>" 
           class="btn-start">+ Ajouter un cours</a>
    </div>
<?php endif; ?>

<div class="row g-4" style="padding:0 20px; display:flex; flex-wrap:wrap; gap:20px; justify-content:center;">

    <?php foreach ($data['cours'] as $c): ?>
    <div class="col-md-4 col-sm-6" style="flex:1 1 300px;">

        <div class="course-card" style="
            background:#fff;
            border-radius:18px;
            padding:20px;
            box-shadow:0 4px 18px rgba(0,0,0,0.08);
            transition:0.2s;
            text-align:center;
            display:flex;
            flex-direction:column;
            height:100%;
        ">
            <i class="fas fa-file-alt fa-3x" style="color:#4CAF50;margin-bottom:10px;"></i>

            <h4 style="color:#2e7d32;margin-top:10px; margin-bottom:10px;">
                <?= htmlspecialchars($c['titre']) ?>
            </h4>

            <p style="color:#555; font-size:0.9rem; min-height:45px; margin-bottom:20px; flex-grow:1;">
                <?= htmlspecialchars(substr($c['description'] ?? 'Pas de description', 0, 90)) ?>...
            </p>

            <!-- Bouton Voir -->
            <a href="<?= URLROOT ?>/Cours/show/<?= urlencode($c['id']) ?>"
               class="btn-start"
               style="padding:7px 14px; border-radius:20px; font-size:0.9rem; display:inline-block; margin-bottom:10px;">
               👁 Voir le cours
            </a>

            <?php if (!empty($_SESSION['admin_logged']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                <div style="margin-top:10px; display:flex; gap:10px; justify-content:center;">
                    <a href="<?= URLROOT ?>/Cours/edit/<?= urlencode($c['id']) ?>" 
                       style="color:#ff9800; font-weight:bold; text-decoration:none; padding:5px 10px; border-radius:20px; background:#FFF3E0; border:1px solid #FFE0B2;">
                       ✏ Modifier
                    </a>

                    <a href="<?= URLROOT ?>/Cours/delete/<?= urlencode($c['id']) ?>" 
                       onclick="return confirm('Supprimer ce cours ?');"
                       style="color:#e53935; font-weight:bold; text-decoration:none; padding:5px 10px; border-radius:20px; background:#FFEBEE; border:1px solid #FFCDD2;">
                       ❌ Supprimer
                    </a>
                </div>
            <?php endif; ?>

        </div>

    </div>
    <?php endforeach; ?>

    <?php if(empty($data['cours'])): ?>
        <p style="text-align:center; width:100%; margin-top:20px; color:#777;">Aucun cours disponible pour ce chapitre.</p>
    <?php endif; ?>

</div>