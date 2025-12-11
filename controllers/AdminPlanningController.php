<?php

require_once __DIR__ . '/../models/Planning.php';
require_once __DIR__ . '/../services/PlanningService.php';

class AdminPlanningController
{
    public function index()
    {
        $plannings = Planning::all();
        $statsParMatiere = Planning::statsParMatiere();
        require __DIR__ . '/../views/admin/planning_index.php';
    }

    public function delete()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            Planning::delete($id);
        }
        header("Location: index.php?controller=adminplanning&action=index");
        exit;
    }

    /** ➤ Modifier un planning (utilise le même formulaire que créer) */
    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            die("ID invalide");
        }

        $planning = Planning::find($id);
        if (!$planning) {
            die("Planning introuvable");
        }

        $old = $planning;
        $errors = [];

        // On réutilise le même fichier que create()
        require __DIR__ . '/../views/admin/planning_form.php';
    }

    /** ➤ Traitement du formulaire */
    public function update()
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            die("ID invalide");
        }

        $data = $_POST;
        $errors = PlanningService::validate($data);

        if (!empty($errors)) {
            $old = $data;
            require __DIR__ . '/../views/admin/planning_form.php';
            return;
        }

        Planning::update($id, $data);

        header("Location: index.php?controller=adminplanning&action=index");
        exit;
    }

    /** ➤ Pour ajouter un planning, inchangé */
    public function create()
    {
        $old = [];
        $errors = [];
        require __DIR__ . '/../views/admin/planning_form.php';
    }
}
