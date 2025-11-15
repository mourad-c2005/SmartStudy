<?php
require_once __DIR__ . '/../core/Model.php';

class Attempt extends Model {

    // Récupérer toutes les tentatives
    public function getAllAttempts() {
        $stmt = $this->db->prepare("
            SELECT a.*, c.titre AS chapitre_titre
            FROM attempts a
            LEFT JOIN chapitres c ON a.id_chapitre = c.id
            ORDER BY a.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Créer une nouvelle tentative
    public function create($id_chapitre, $student_name, $score, $total_questions, $time_seconds) {
        $stmt = $this->db->prepare("
            INSERT INTO attempts (id_chapitre, student_name, score, total_questions, time_seconds)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$id_chapitre, $student_name, $score, $total_questions, $time_seconds]);
        return $this->db->lastInsertId();
    }

    // Ajouter la réponse d'un quiz
    public function addAnswer($attempt_id, $quiz_id, $selected) {
        $stmt = $this->db->prepare("
            INSERT INTO attempt_answers (attempt_id, quiz_id, selected)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$attempt_id, $quiz_id, $selected]);
    }

    // Récupérer les réponses d'une tentative
    public function getAnswers($attempt_id) {
        $stmt = $this->db->prepare("SELECT * FROM attempt_answers WHERE attempt_id=?");
        $stmt->execute([$attempt_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
