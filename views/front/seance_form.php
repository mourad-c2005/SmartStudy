<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$errors = $errors ?? [];
$old    = $old ?? [];

// Jours de la semaine
$joursLabels = [
    'lundi'     => 'Lundi',
    'mardi'     => 'Mardi',
    'mercredi'  => 'Mercredi',
    'jeudi'     => 'Jeudi',
    'vendredi'  => 'Vendredi',
    'samedi'    => 'Samedi',
    'dimanche'  => 'Dimanche',
];

// Durées possibles (tu peux adapter)
$durees = [
    '15 min'   => '15 min',
    '30 min'   => '30 min',
    '45 min'   => '45 min',
    '1 h'      => '1 h',
    '1 h 30'   => '1 h 30',
    '2 h'      => '2 h',
];

// Matières possibles (à adapter si tu veux)
$matieres = [
    'Maths',
    'Physique',
    'Informatique',
    'Français',
    'Anglais',
    'SVT',
    'Histoire-Géo',
    'Philosophie',
];

// Valeurs pré-remplies pour la date (en cas d’erreur de saisie)
$jjOld = isset($old['date_jour'])   ? (int)$old['date_jour']   : 0;
$mmOld = isset($old['date_mois'])   ? (int)$old['date_mois']   : 0;
$aaOld = isset($old['date_annee'])  ? (int)$old['date_annee']  : 0;
?>

<div class="page-section">
    <h2 class="section-title text-center mb-1">Créer une séance d'étude</h2>
    <p class="section-subtitle text-center mb-4">
        Planifie une séance précise avec un professeur, un lieu et un chapitre à réviser.
    </p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php?controller=planning&action=createSeance" class="card-soft p-4">

        <div class="row">
            <!-- Jour -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="jour_semaine">Jour</label>
                <select name="jour_semaine" id="jour_semaine" class="form-control">
                    <option value="">-- Choisir --</option>
                    <?php foreach ($joursLabels as $value => $label): ?>
                        <option value="<?= $value ?>"
                            <?= (isset($old['jour_semaine']) && $old['jour_semaine'] === $value) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date (JJ / MM / AAAA) -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Date de la séance (JJ-MM-AAAA)</label>
                <div class="d-flex gap-2">
                    <!-- Jour -->
                    <select name="date_jour" class="form-control">
                        <option value="">JJ</option>
                        <?php for ($d = 1; $d <= 31; $d++): ?>
                            <option value="<?= $d ?>" <?= ($jjOld === $d ? 'selected' : '') ?>>
                                <?= sprintf('%02d', $d) ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <!-- Mois -->
                    <select name="date_mois" class="form-control">
                        <option value="">MM</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($mmOld === $m ? 'selected' : '') ?>>
                                <?= sprintf('%02d', $m) ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <!-- Année -->
                    <select name="date_annee" class="form-control">
                        <option value="">AAAA</option>
                        <?php
                        $yearNow = (int)date('Y');
                        for ($y = $yearNow; $y <= $yearNow + 3; $y++): ?>
                            <option value="<?= $y ?>" <?= ($aaOld === $y ? 'selected' : '') ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <small class="text-muted">Ex : 15 / 01 / <?= date('Y') + 1 ?></small>
            </div>
        </div>

        <div class="row">
            <!-- Heure -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="heure">Heure (HH:MM)</label>
                <input type="text"
                       name="heure"
                       id="heure"
                       class="form-control"
                       placeholder="Ex : 18:00"
                       value="<?= htmlspecialchars($old['heure'] ?? '') ?>">
            </div>

            <!-- Durée -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="duree">Durée</label>
                <select name="duree" id="duree" class="form-control">
                    <option value="">-- Choisir la durée --</option>
                    <?php foreach ($durees as $val): ?>
                        <option value="<?= $val ?>"
                            <?= (isset($old['duree']) && $old['duree'] === $val) ? 'selected' : '' ?>>
                            <?= $val ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- Lieu d'étude -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="lieu_etude">Lieu d'étude</label>
                <input type="text"
                       name="lieu_etude"
                       id="lieu_etude"
                       class="form-control"
                       placeholder="Ex : Bibliothèque, Salle B12..."
                       value="<?= htmlspecialchars($old['lieu_etude'] ?? '') ?>">
            </div>

            <!-- Matière -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="matiere">Matière</label>
                <select name="matiere" id="matiere" class="form-control">
                    <option value="">-- Choisir une matière --</option>
                    <?php foreach ($matieres as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>"
                            <?= (isset($old['matiere']) && $old['matiere'] === $m) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- Nom du professeur -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="nom_prof">Nom du professeur</label>
                <input type="text"
                       name="nom_prof"
                       id="nom_prof"
                       class="form-control"
                       placeholder="Ex : Mr Dupont"
                       value="<?= htmlspecialchars($old['nom_prof'] ?? '') ?>">
            </div>

            <!-- Numéro de la séance -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="num_seance">Numéro de la séance (1 à 4)</label>
                <select name="num_seance" id="num_seance" class="form-control">
                    <option value="">-- Choisir --</option>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <option value="<?= $i ?>"
                            <?= (isset($old['num_seance']) && (int)$old['num_seance'] === $i) ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <!-- Chapitre à réviser -->
        <div class="mb-3">
            <label class="form-label" for="chapitre_avant">Chapitre à réviser avant la séance</label>
            <textarea name="chapitre_avant"
                      id="chapitre_avant"
                      rows="3"
                      class="form-control"
                      placeholder="Ex : Chapitre 3 - Dérivées et applications"><?= htmlspecialchars($old['chapitre_avant'] ?? '') ?></textarea>
        </div>

        <div class="form-actions mt-4 d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-primary">
                Enregistrer la séance
            </button>
            <a href="index.php?controller=planning&action=index" class="btn btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
