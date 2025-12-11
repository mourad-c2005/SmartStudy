<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2 class="section-title">Détails du planning</h2>

<?php if (!$planning): ?>
    <div class="alert-danger">Aucun planning trouvé.</div>
<?php else: ?>

<?php 
    $isSeance = isset($planning['type']) && $planning['type'] === 'seance';
?>

<div class="card-soft" style="max-width: 700px; margin:auto;">

    <h3 class="mb-3">
        <?= htmlspecialchars($planning['matiere']) ?>
        <?php if ($isSeance): ?>
            <span style="
                background:#2563eb;
                color:white;
                padding:4px 10px;
                border-radius:8px;
                font-size:0.8rem;
                margin-left:10px;">
                Séance d'étude
            </span>
        <?php endif; ?>
    </h3>

    <p><strong>Jour :</strong> <?= htmlspecialchars($planning['jour_semaine']) ?></p>

    <?php if ($isSeance && !empty($planning['date_seance'])): ?>
        <p><strong>Date :</strong> <?= htmlspecialchars($planning['date_seance']) ?></p>
    <?php endif; ?>

    <p><strong>Heure :</strong> <?= htmlspecialchars($planning['heure']) ?></p>

    <?php if (!$isSeance): ?>
        <p><strong>Durée :</strong> <?= htmlspecialchars($planning['duree']) ?></p>
    <?php endif; ?>

    <p><strong>Matière :</strong> <?= htmlspecialchars($planning['matiere']) ?></p>

    <?php if (!$isSeance): ?>
        <p><strong>Thème :</strong> <?= htmlspecialchars($planning['theme']) ?></p>
        <p><strong>Difficulté :</strong> <?= htmlspecialchars($planning['difficulte']) ?></p>
        <p><strong>Priorité :</strong> <?= htmlspecialchars($planning['priorite']) ?></p>
        <p><strong>Objectif :</strong> <?= htmlspecialchars($planning['objectif']) ?></p>
    <?php else: ?>
        <p><strong>Lieu :</strong> <?= htmlspecialchars($planning['lieu_etude'] ?? '-') ?></p>
        <p><strong>Professeur :</strong> <?= htmlspecialchars($planning['nom_prof'] ?? '-') ?></p>
        <p><strong>Chapitre à revoir :</strong> <?= htmlspecialchars($planning['chapitre_avant'] ?? '-') ?></p>
        <p><strong>Numéro de séance :</strong> <?= htmlspecialchars($planning['num_seance'] ?? '-') ?></p>
    <?php endif; ?>

    <div class="mt-4">
        <a href="index.php?controller=planning&action=index" class="btn btn-secondary">Retour</a>
    </div>

</div>

<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
