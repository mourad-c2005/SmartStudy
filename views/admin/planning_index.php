<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-header-line d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="section-title mb-1">Gestion des Plannings & Séances d'étude</h2>
        <p class="section-subtitle mb-0">
            Visualise, modifie ou supprime rapidement tous les plannings et séances d’étude.
        </p>
    </div>

    <div class="d-flex gap-2">
        <a href="index.php?controller=planning&action=create" class="btn btn-green">
            + Ajouter un planning
        </a>
        <a href="index.php?controller=planning&action=createSeance" class="btn btn-primary">
            + Ajouter une séance d'étude
        </a>
    </div>
</div>

<div class="card-soft table-wrapper-admin">
    <table class="table table-admin align-middle mb-0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Jour</th>
            <th>Date</th>
            <th>Heure</th>
            <th>Durée</th>
            <th>Matière</th>
            <th>Thème / Lieu / Prof</th>
            <th>Difficulté</th>
            <th>Priorité</th>
            <th>Objectif / Chapitre</th>
            <th class="text-end">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // petites fonctions utilitaires pour les badges
        function badgeDiff($value) {
            $value = strtolower(trim((string)$value));
            if ($value === 'facile')   return ['Facile',   'badge-diff-facile'];
            if ($value === 'moyenne' || $value === 'moy') return ['Moyenne', 'badge-diff-moy'];
            if ($value === 'difficile') return ['Difficile', 'badge-diff-diff'];
            if ($value === '') return ['—', 'badge-muted'];
            return [$value, 'badge-muted'];
        }

        function badgePrio($value) {
            $value = strtolower(trim((string)$value));
            if ($value === 'basse')   return ['Basse',   'badge-prio-low'];
            if ($value === 'moyenne' || $value === 'moy') return ['Moyenne', 'badge-prio-mid'];
            if ($value === 'haute')   return ['Haute',   'badge-prio-high'];
            if ($value === '') return ['—', 'badge-muted'];
            return [$value, 'badge-muted'];
        }
        ?>

        <?php foreach ($plannings as $p): ?>
            <?php $isSeance = (isset($p['type']) && $p['type'] === 'seance'); ?>

            <tr>
                <td class="text-muted fw-semibold">#<?= (int)$p['id'] ?></td>

                <td>
                    <?php if ($isSeance): ?>
                        <span class="badge-type badge-type-seance">Séance d'étude</span>
                    <?php else: ?>
                        <span class="badge-type badge-type-planning">Planning</span>
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($p['jour_semaine']) ?></td>

                <td>
                    <?= !empty($p['date_seance'])
                        ? htmlspecialchars($p['date_seance'])
                        : '—'
                    ?>
                </td>

                <td><?= htmlspecialchars($p['heure']) ?></td>

                <td><?= htmlspecialchars($p['duree']) ?></td>

                <td class="fw-semibold"><?= htmlspecialchars($p['matiere']) ?></td>

                <td>
                    <?php if ($isSeance): ?>
                        <div class="small text-muted">
                            <div><strong>Lieu :</strong> <?= htmlspecialchars($p['lieu_etude'] ?? '-') ?></div>
                            <div><strong>Prof :</strong> <?= htmlspecialchars($p['nom_prof'] ?? '-') ?></div>
                        </div>
                    <?php else: ?>
                        <span class="text-muted small">
                            <?= htmlspecialchars($p['theme'] ?? '-') ?>
                        </span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php
                    [$labelDiff, $classDiff] = badgeDiff($p['difficulte'] ?? '');
                    ?>
                    <span class="badge-pill <?= $classDiff ?>">
                        <?= htmlspecialchars($labelDiff) ?>
                    </span>
                </td>

                <td>
                    <?php
                    [$labelPrio, $classPrio] = badgePrio($p['priorite'] ?? '');
                    ?>
                    <span class="badge-pill <?= $classPrio ?>">
                        <?= htmlspecialchars($labelPrio) ?>
                    </span>
                </td>

                <td>
                    <?php if ($isSeance): ?>
                        <div class="small text-muted">
                            <div><strong>Chapitre :</strong> <?= htmlspecialchars($p['chapitre_avant'] ?? '-') ?></div>
                            <div><strong>Séance n°</strong> <?= (int)($p['num_seance'] ?? 0) ?></div>
                        </div>
                    <?php else: ?>
                        <span class="small text-muted">
                            <?= htmlspecialchars($p['objectif'] ?? '-') ?>
                        </span>
                    <?php endif; ?>
                </td>

                <td class="text-end">
                    <div class="btn-group-admin">
                        <!-- Bouton Modifier (à brancher plus tard sur edit si tu veux) -->
                        <a href="index.php?controller=adminplanning&action=edit&id=<?= (int)$p['id'] ?>"
                            class="btn btn-sm btn-outline-primary"
                            title="Modifier ce planning">
                            Modifier
                        </a>

                        <a href="index.php?controller=adminplanning&action=delete&id=<?= (int)$p['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer cet élément ?');">
                            Supprimer
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($statsParMatiere ?? [])): ?>
    <div class="card-soft mt-4">
        <h5 class="mb-2">Statistiques par matière</h5>
        <ul class="mb-0">
            <?php foreach ($statsParMatiere as $matiere => $nb): ?>
                <li><?= htmlspecialchars($matiere) ?> : <?= (int)$nb ?> séance(s)</li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
