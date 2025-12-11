<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
$errors = $errors ?? [];
$old    = $old    ?? [];

// Jours
$joursLabels = [
    'lundi'     => 'Lundi',
    'mardi'     => 'Mardi',
    'mercredi'  => 'Mercredi',
    'jeudi'     => 'Jeudi',
    'vendredi'  => 'Vendredi',
    'samedi'    => 'Samedi',
    'dimanche'  => 'Dimanche',
];

// Matières
$matieres = [
    'Math', 'Physique', 'Science', 'Algorithme', 'Programmation', 'Technique',
    'Arabe', 'Français', 'Anglais', 'Philosophie', 'Informatique', 'Économie',
    'Gestion', 'Histoire', 'Géographie', 'Architecture', 'Réseaux',
    'Base de données', 'Espagnol', 'Italien', 'Russe', 'Chinois',
];

$oldHeure    = $old['heure']   ?? '';
$oldDuree    = $old['duree']   ?? '';
$oldMatiere  = $old['matiere'] ?? '';
$oldNum      = isset($old['num_seance']) ? (int)$old['num_seance'] : 0;
?>

<h2 class="section-title">Créer une séance d'étude</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card-soft">
<form method="POST">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="jour_semaine">Jour *</label>
            <select name="jour_semaine" id="jour_semaine" class="form-control" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($joursLabels as $key => $label): ?>
                    <option value="<?= $key ?>" <?= (($old['jour_semaine'] ?? '') === $key) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label" for="date_seance">Date de la séance</label>
            <input type="date"
                   name="date_seance"
                   id="date_seance"
                   class="form-control"
                   value="<?= htmlspecialchars($old['date_seance'] ?? '') ?>">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="heure">Heure *</label>
            <input type="time" name="heure" id="heure" class="form-control"
                   value="<?= htmlspecialchars($oldHeure) ?>" required>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label" for="duree">Durée *</label>
            <select name="duree" id="duree" class="form-control" required>
                <option value="">-- Choisir la durée --</option>
                <?php
                $durees = ['0H30','1H','1H30','2H','2H30','3H','3H30','4H'];
                foreach ($durees as $d) {
                    $sel = ($oldDuree === $d) ? ' selected' : '';
                    echo '<option value="'.$d.'"'.$sel.'>'.$d.'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="lieu_etude">Lieu d'étude</label>
            <input type="text"
                   name="lieu_etude"
                   id="lieu_etude"
                   class="form-control"
                   placeholder="Ex : Bibliothèque, Salle B12..."
                   value="<?= htmlspecialchars($old['lieu_etude'] ?? '') ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label" for="matiere">Matière *</label>
            <select name="matiere" id="matiere" class="form-control" required>
                <option value="">-- Choisir une matière --</option>
                <?php foreach ($matieres as $m): ?>
                    <option value="<?= htmlspecialchars($m) ?>" <?= ($oldMatiere === $m) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="nom_prof">Nom du professeur</label>
            <input type="text"
                   name="nom_prof"
                   id="nom_prof"
                   class="form-control"
                   placeholder="Ex : Mr Dupont"
                   value="<?= htmlspecialchars($old['nom_prof'] ?? '') ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label" for="num_seance">Numéro de la séance (1 à 4) *</label>
            <select name="num_seance" id="num_seance" class="form-control" required>
                <option value="">-- Choisir --</option>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <option value="<?= $i ?>" <?= ($oldNum === $i) ? 'selected' : '' ?>>
                        Séance <?= $i ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label" for="chapitre_avant">Chapitre à réviser avant la séance</label>
        <textarea name="chapitre_avant"
                  id="chapitre_avant"
                  class="form-control"
                  rows="2"><?= htmlspecialchars($old['chapitre_avant'] ?? '') ?></textarea>
    </div>

    <div class="form-actions mt-4">
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
