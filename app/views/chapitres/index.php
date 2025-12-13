<?php 
// Get the subject name safely
$matiereName = '';
if (isset($data['matiere']) && is_array($data['matiere']) && isset($data['matiere']['nom'])) {
    $matiereName = htmlspecialchars($data['matiere']['nom']);
}
?>

<h1 style="text-align:center; margin-bottom:25px;">
    Chapitres  
    <?php if (!empty($matiereName)): ?>
        <span style="color:#4CAF50;"><?= $matiereName ?></span>
    <?php else: ?>
        <span style="color:#666;">[Matière non spécifiée]</span>
    <?php endif; ?>
</h1>

<?php if (!empty($_SESSION['admin_logged']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
    <div style="text-align:center; margin-bottom:20px;">
        <?php if (isset($data['matiere']['id'])): ?>
            <a href="<?= URLROOT ?>/Chapitre/add/<?= htmlspecialchars($data['matiere']['id']) ?>" 
               class="btn-start">+ Ajouter un chapitre</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="row g-4" style="padding:0 20px;">
    <?php if (isset($data['chapitres']) && is_array($data['chapitres']) && !empty($data['chapitres'])): ?>
        <?php foreach ($data['chapitres'] as $c): ?>
            <?php if (is_array($c) && isset($c['id'], $c['titre'])): ?>
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

                    <?php if (!empty($_SESSION['admin_logged']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <div style="margin-top:15px; display:flex; gap:10px; justify-content:center;">
                        <a href="<?= URLROOT ?>/Chapitre/edit/<?= urlencode($c['id']) ?>" 
                           style="color:#ff9800; font-weight:bold; text-decoration:none; padding:5px 10px; border-radius:20px; background:#FFF3E0; border:1px solid #FFE0B2;">
                           ✏ Modifier
                        </a>

                        <a href="<?= URLROOT ?>/Chapitre/delete/<?= urlencode($c['id']) ?>" 
                           onclick="return confirm('Supprimer ce chapitre ?');"
                           style="color:#e53935; font-weight:bold; text-decoration:none; padding:5px 10px; border-radius:20px; background:#FFEBEE; border:1px solid #FFCDD2;">
                           ❌ Supprimer
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12" style="text-align:center; padding:40px;">
            <p style="color:#666; font-size:1.1rem;">Aucun chapitre disponible pour le moment.</p>
        </div>
    <?php endif; ?>
</div>