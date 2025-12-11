<?php require __DIR__ . '/../layouts/header.php'; ?>

<?php
// Sécurisation des variables
$errors = $errors ?? [];
$old    = $old    ?? [];

// On détecte si on est en mode édition ou création
$isEdit = isset($old['id']) && !empty($old['id']);

$action = $isEdit
    ? 'index.php?controller=adminplanning&action=update'
    : 'index.php?controller=adminplanning&action=create';

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

$selectedDay = strtolower($old['jour_semaine'] ?? '');

// Matières (tu peux en rajouter si tu veux)
$matieres = [
    'Math', 'Physique', 'Science', 'Algorithme', 'Programmation', 'Technique',
    'Arabe', 'Français', 'Anglais', 'Philosophie', 'Informatique', 'Économie',
    'Gestion', 'Histoire', 'Géographie', 'Architecture', 'Réseaux',
    'Base de données', 'Espagnol', 'Italien', 'Russe', 'Chinois',
];
?>

<h2 class="section-title">
    <?= $isEdit ? 'Modifier un planning (Admin)' : 'Créer un planning (Admin)' ?>
</h2>

<?php if (!empty($errors)): ?>
    <div class="alert-danger">
        <strong>Oups ! Il y a des erreurs :</strong>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="<?= $action ?>">

        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int)$old['id'] ?>">
        <?php endif; ?>

        <!-- Ligne 1 : Jour / Heure / Durée -->
        <div class="row">
            <!-- Jour -->
            <div class="col-md-4 mb-3">
                <label class="form-label" for="jour_semaine">Jour</label>
                <select name="jour_semaine" id="jour_semaine" class="form-control">
                    <option value="">-- Choisir un jour --</option>
                    <?php foreach ($joursLabels as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($selectedDay === $key) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Heure en texte (pas de HTML5) -->
            <div class="col-md-4 mb-3">
                <label class="form-label" for="heure">Heure (format HH:MM)</label>
                <input type="text"
                       name="heure"
                       id="heure"
                       class="form-control"
                       placeholder="Ex : 08:30"
                       value="<?= htmlspecialchars($old['heure'] ?? '') ?>">
            </div>

            <!-- Durée (select texte) -->
            <div class="col-md-4 mb-3">
                <label class="form-label" for="duree">Durée</label>
                <select name="duree" id="duree" class="form-control">
                    <option value="">-- Choisir la durée --</option>
                    <?php
                    $durees = ['0H30','1H','1H30','2H','2H30','3H','3H30','4H'];
                    foreach ($durees as $d):
                        $sel = (($old['duree'] ?? '') === $d) ? 'selected' : '';
                    ?>
                        <option value="<?= $d ?>" <?= $sel ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Ligne 2 : Matière / Thème -->
        <div class="row">
            <!-- Matière -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="matiere">Matière</label>
                <select name="matiere" id="matiere" class="form-control">
                    <option value="">-- Choisir une matière --</option>
                    <?php foreach ($matieres as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>"
                            <?= (($old['matiere'] ?? '') === $m) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Thème -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="theme">Thème / Chapitre</label>
                <input type="text"
                       name="theme"
                       id="theme"
                       class="form-control"
                       placeholder="Ex : Équations différentielles"
                       value="<?= htmlspecialchars($old['theme'] ?? '') ?>">
            </div>
        </div>

        <!-- Ligne 3 : Difficulté / Priorité -->
        <div class="row">
            <!-- Difficulté -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="difficulte">Difficulté</label>
                <select name="difficulte" id="difficulte" class="form-control">
                    <option value="">-- Choisir --</option>
                    <option value="easy" <?= (($old['difficulte'] ?? '') === 'easy') ? 'selected' : '' ?>>Facile (easy)</option>
                    <option value="moy"  <?= (($old['difficulte'] ?? '') === 'moy')  ? 'selected' : '' ?>>Moyenne (moy)</option>
                    <option value="dif"  <?= (($old['difficulte'] ?? '') === 'dif')  ? 'selected' : '' ?>>Difficile (dif)</option>
                </select>
            </div>

            <!-- Priorité -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="priorite">Priorité</label>
                <select name="priorite" id="priorite" class="form-control">
                    <option value="">-- Choisir --</option>
                    <option value="faible"  <?= (($old['priorite'] ?? '') === 'faible')  ? 'selected' : '' ?>>Faible</option>
                    <option value="moyenne" <?= (($old['priorite'] ?? '') === 'moyenne') ? 'selected' : '' ?>>Moyenne</option>
                    <option value="haute"   <?= (($old['priorite'] ?? '') === 'haute')   ? 'selected' : '' ?>>Haute</option>
                </select>
            </div>
        </div>

        <!-- Objectif -->
        <div class="mb-3">
            <label class="form-label" for="objectif">Objectif de la séance</label>
            <textarea name="objectif"
                      id="objectif"
                      class="form-control"
                      rows="2"
                      placeholder="Ex : Comprendre les intégrales par parties"><?= htmlspecialchars($old['objectif'] ?? '') ?></textarea>
        </div>

        <!-- Boutons -->
        <div class="form-actions mt-4">
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le planning' ?>
            </button>
            <a href="index.php?controller=adminplanning&action=index" class="btn btn-secondary">
                Annuler
            </a>
        </div>

    </form>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
