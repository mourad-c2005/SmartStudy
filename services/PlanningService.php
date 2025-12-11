<?php

class PlanningService
{
    public static function validate(array $data): array
    {
        $errors = [];

        // Jour
        $jours = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];
        $jour = strtolower(trim($data['jour_semaine'] ?? ''));
        if (!in_array($jour, $jours)) {
            $errors[] = "Jour invalide.";
        }

        // Heure (HH:MM)
        $heure = trim($data['heure'] ?? '');
        if (!preg_match('/^[0-2][0-9]:[0-5][0-9]$/', $heure)) {
            $errors[] = "Heure invalide (format attendu : HH:MM).";
        }

        // Durée (1H, 1H30, etc.)
        $duree = trim($data['duree'] ?? '');
        if (!preg_match('/^[0-9]+H(30)?$/', $duree)) {
            $errors[] = "Durée invalide (exemples : 1H, 1H30, 2H).";
        }

        // Matière
        if (empty(trim($data['matiere'] ?? ''))) {
            $errors[] = "La matière est obligatoire.";
        }

        // Thème
        if (empty(trim($data['theme'] ?? ''))) {
            $errors[] = "Le thème / chapitre est obligatoire.";
        }

        // Difficulté
        $diffs = ['easy','moy','dif'];
        if (!empty($data['difficulte']) && !in_array($data['difficulte'], $diffs)) {
            $errors[] = "Difficulté invalide.";
        }

        // Priorité
        $prios = ['faible','moyenne','haute'];
        if (!empty($data['priorite']) && !in_array($data['priorite'], $prios)) {
            $errors[] = "Priorité invalide.";
        }

        // Objectif
        if (empty(trim($data['objectif'] ?? ''))) {
            $errors[] = "L'objectif de la séance est obligatoire.";
        }

        return $errors;
    }
}
