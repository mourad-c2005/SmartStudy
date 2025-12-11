<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
// Helpers d'affichage
$jours = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];

// Regroupement des enregistrements par jour_semaine
$byDay = [];
foreach ($jours as $j) { $byDay[$j] = []; }
foreach (($plannings ?? []) as $p) {
    $j = strtolower(trim($p['jour_semaine'] ?? ''));
    if (isset($byDay[$j])) {
        $byDay[$j][] = $p;
    }
}

// Calcule l'état de charge d'une journée sur base du nb de "séances"
function etat_journee(array $itemsPourLeJour): string {
    $nbSeances = 0;
    foreach ($itemsPourLeJour as $it) {
        if (($it['type'] ?? '') === 'seance') $nbSeances++;
    }
    if ($nbSeances === 0) return 'libre';
    if ($nbSeances <= 2) return 'moyenne';
    return 'chargée';
}
?>

<h2 class="section-title">Mon Planning (Emploi du temps)</h2>
<p class="section-subtitle">
    Organise ta semaine : chaque colonne correspond à un jour.
</p>

<div class="d-flex justify-content-end mb-3">
    <a href="index.php?controller=planning&action=create" class="btn btn-green">
        + Ajouter un planning
    </a>
    <a href="index.php?controller=planning&action=createSeance" class="btn btn-primary ms-2">
        + Ajouter une séance d'étude
    </a>
    <a href="index.php?controller=planning&action=resetWeek"
       class="btn btn-outline-danger ms-2"
       onclick="return confirm('Tu es sûr de vouloir effacer tous les plannings et séances (nouvelle semaine) ?');">
        New week
    </a>
</div>

<div class="row g-3">
    <?php foreach ($jours as $jour): ?>
        <?php
        $items = $byDay[$jour] ?? [];
        $etat  = etat_journee($items);
        ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card-soft h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h5 class="mb-0"><?= ucfirst($jour) ?></h5>
                    <span class="badge bg-secondary">Journée <?= htmlspecialchars($etat) ?></span>
                </div>

                <?php if (empty($items)): ?>
                    <p class="text-muted mb-0">Aucune entrée.</p>
                <?php else: ?>
                    <?php foreach ($items as $it): ?>
                        <div class="border rounded p-2 mb-2">
                            <div class="d-flex flex-wrap justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($it['heure'] ?? '') ?></strong>
                                    <?php if (!empty($it['duree'])): ?>
                                        <span class="text-muted">(<?= htmlspecialchars($it['duree']) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-uppercase small">
                                    <span class="badge <?= ($it['type'] ?? '') === 'seance' ? 'bg-primary' : 'bg-success' ?>">
                                        <?= ($it['type'] ?? '') === 'seance' ? 'Séance' : 'Planning' ?>
                                    </span>
                                </div>
                            </div>

                            <div class="mt-1">
                                <?php if (!empty($it['matiere'])): ?>
                                    <div><strong>Matière :</strong> <?= htmlspecialchars($it['matiere']) ?></div>
                                <?php endif; ?>

                                <?php if (!empty($it['theme'])): ?>
                                    <div><strong>Thème :</strong> <?= htmlspecialchars($it['theme']) ?></div>
                                <?php endif; ?>

                                <?php if (!empty($it['objectif'])): ?>
                                    <div><strong>Objectif :</strong> <?= htmlspecialchars($it['objectif']) ?></div>
                                <?php endif; ?>

                                <?php if (!empty($it['priorite'])): ?>
                                    <div class="small"><strong>Priorité :</strong> <?= htmlspecialchars($it['priorite']) ?></div>
                                <?php endif; ?>

                                <?php if (($it['type'] ?? '') === 'seance'): ?>
                                    <div class="small mt-1 text-muted">
                                        <?php if (!empty($it['nom_prof'])): ?>
                                            Prof : <?= htmlspecialchars($it['nom_prof']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($it['lieu_etude'])): ?>
                                            &nbsp; | &nbsp; Lieu : <?= htmlspecialchars($it['lieu_etude']) ?>
                                        <?php endif; ?>
                                        <?php if (!empty($it['date_seance'])): ?>
                                            &nbsp; | &nbsp; Date : <?= htmlspecialchars($it['date_seance']) ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-1">
                                        <?php $num = (int)($it['num_seance'] ?? 0); ?>
                                        <span class="badge bg-light text-dark">Séance n° <?= $num ?></span>
                                        <?php if ($num === 4): ?>
                                            <span class="badge bg-danger ms-1">Payer le prof</span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($it['chapitre_avant'])): ?>
                                        <div class="small mt-1"><em>Chapitre avant :</em> <?= htmlspecialchars($it['chapitre_avant']) ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (!empty($statsParMatiere ?? [])): ?>
    <div class="card-soft mt-4">
        <h5>Statistiques par matière</h5>
        <ul class="mb-0">
            <?php foreach ($statsParMatiere as $matiere => $nb): ?>
                <li><?= htmlspecialchars($matiere) ?> : <?= (int)$nb ?> séance(s)</li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
