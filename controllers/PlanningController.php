<?php
// controllers/PlanningController.php

require_once __DIR__ . '/../models/Planning.php';
require_once __DIR__ . '/../services/PlanningService.php';

class PlanningController
{
    /** @var PDO */
    private $pdo;

    public function __construct()
    {
        // ⚠️ ADAPTE le nom de ta base, l'utilisateur et le mot de passe
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=smartstudy;charset=utf8',
            'root',
            ''
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // =========================
    //  FRONT - PLANNING GLOBAL
    // =========================

    // Affiche la grille du planning (views/front/planning_index.php)
    public function index()
    {
        // 1. Récupération de tous les plannings (et éventuellement des séances)
        $plannings = Planning::all();

        // 2. Organisation des plannings par jour de la semaine
        $planningsByDay = [];
        foreach ($plannings as $p) {
            $dayKey = strtolower($p['jour_semaine']);
            if (!isset($planningsByDay[$dayKey])) {
                $planningsByDay[$dayKey] = [];
            }
            $planningsByDay[$dayKey][] = $p;
        }

        // 3. Récupération des statistiques via le Service
        $statsParMatiere = Planning::statsParMatiere();

        // 4. Affichage de la vue
        require __DIR__ . '/../views/front/planning_index.php';
    }

    // Affiche le formulaire de création d’un PLANNING (views/front/planning_form.php)
    public function create()
    {
        $old    = [];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $old  = $data;

            // 1. Validation via le Service
            $errors = PlanningService::validate($data);

            if (empty($errors)) {
                // 2. Création via le Modèle
                Planning::create($data);

                // Redirection après succès (PRG)
                header('Location: index.php?controller=planning&action=index');
                exit;
            }
        }

        require __DIR__ . '/../views/front/planning_form.php';
    }

    // Affiche les détails d'un planning (views/front/planning_show.php)
    public function show()
    {
        $id       = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $planning = Planning::find($id);

        require __DIR__ . '/../views/front/planning_show.php';
    }

    // =========================
    //  FRONT - SEANCE D’ETUDE
    // ==================
        // =========================
    //  FRONT - SEANCE D’ETUDE
    // =========================

    public function createSeance()
    {
        $errors = [];
        $old    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = $_POST;

            // Récupération des champs
            $jour_semaine = trim($_POST['jour_semaine'] ?? '');

            $date_jour   = trim($_POST['date_jour'] ?? '');
            $date_mois   = trim($_POST['date_mois'] ?? '');
            $date_annee  = trim($_POST['date_annee'] ?? '');

            $heure       = trim($_POST['heure'] ?? '');
            $duree       = trim($_POST['duree'] ?? '');
            $lieu_etude  = trim($_POST['lieu_etude'] ?? '');
            $matiere     = trim($_POST['matiere'] ?? '');
            $nom_prof    = trim($_POST['nom_prof'] ?? '');
            $num_seance  = $_POST['num_seance'] ?? '';
            $chapitre    = trim($_POST['chapitre_avant'] ?? '');

            $date_seance = null;

            // --------- VALIDATIONS ---------

            // Jour
            if ($jour_semaine === '') {
                $errors[] = "Le jour est obligatoire.";
            }

            // Heure HH:MM (sans HTML5)
            if ($heure === '') {
                $errors[] = "L'heure est obligatoire.";
            } elseif (!preg_match('/^[0-2][0-9]:[0-5][0-9]$/', $heure)) {
                $errors[] = "Heure invalide (format attendu : HH:MM).";
            }

            // Durée
            if ($duree === '') {
                $errors[] = "La durée est obligatoire.";
            }

            // Matière
            if ($matiere === '') {
                $errors[] = "La matière est obligatoire.";
            }

            // Numéro de séance 1..4
            if ($num_seance === '' || (int)$num_seance < 1 || (int)$num_seance > 4) {
                $errors[] = "Le numéro de séance doit être compris entre 1 et 4.";
            }

            // Date (3 selects)
            if ($date_jour !== '' || $date_mois !== '' || $date_annee !== '') {

                if ($date_jour === '' || $date_mois === '' || $date_annee === '') {
                    $errors[] = "La date de la séance est incomplète.";
                } elseif (!checkdate((int)$date_mois, (int)$date_jour, (int)$date_annee)) {
                    $errors[] = "La date de la séance est invalide.";
                } else {
                    // On stocke en JJ-MM-AAAA (comme ton placeholder)
                    $date_seance = sprintf('%02d-%02d-%04d',
                        (int)$date_jour,
                        (int)$date_mois,
                        (int)$date_annee
                    );
                }
            }

            // --------- SI PAS D’ERREUR → INSERT ---------
            if (empty($errors)) {
                $sql = "INSERT INTO planning (
                            jour_semaine,
                            date_seance,
                            heure,
                            duree,
                            matiere,
                            lieu_etude,
                            nom_prof,
                            chapitre_avant,
                            num_seance,
                            type
                        ) VALUES (
                            :jour_semaine,
                            :date_seance,
                            :heure,
                            :duree,
                            :matiere,
                            :lieu_etude,
                            :nom_prof,
                            :chapitre_avant,
                            :num_seance,
                            'seance'
                        )";

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':jour_semaine'   => $jour_semaine,
                    ':date_seance'    => $date_seance, // peut être NULL
                    ':heure'          => $heure,
                    ':duree'          => $duree,
                    ':matiere'        => $matiere,
                    ':lieu_etude'     => $lieu_etude,
                    ':nom_prof'       => $nom_prof,
                    ':chapitre_avant' => $chapitre,
                    ':num_seance'     => (int)$num_seance,
                ]);

                // Redirection après succès
                header('Location: index.php?controller=planning&action=index');
                exit;
            }
        }

        // Affichage du formulaire (GET ou POST avec erreurs)
        $errors_local = $errors;
        $old_local    = $old;
        $errors = $errors_local;
        $old    = $old_local;

        require __DIR__ . '/../views/front/seance_form.php';
    }

    public function resetWeek()
{
    // Efface tout le contenu de la table "planning"
    // TRUNCATE est rapide, mais peut être bloqué si FK ; on essaie TRUNCATE puis on repasse en DELETE si besoin.
    try {
        $this->pdo->exec("TRUNCATE TABLE planning");
    } catch (PDOException $e) {
        $this->pdo->exec("DELETE FROM planning");
    }

    // Redirection vers l’index
    header('Location: index.php?controller=planning&action=index');
    exit;
}

}
