<?php
require_once __DIR__ . '/../core/Model.php';

class Attempt extends Model {

    // Enregistrer une nouvelle tentative
    public function create($id_cours, $student_name, $score, $total, $time_seconds) {
        $stmt = $this->db->prepare("
            INSERT INTO attempts (id_chapitre, student_name, score, total_questions, time_seconds, created_at)
            VALUES (:id_cours, :student_name, :score, :total, :time_seconds, NOW())
        ");
        $stmt->execute([
            ':id_cours' => $id_cours,
            ':student_name' => $student_name,
            ':score' => $score,
            ':total' => $total,
            ':time_seconds' => $time_seconds
        ]);
        return $this->db->lastInsertId();
    }

    // Ajouter les réponses d'une tentative
    public function addAnswer($attempt_id, $question_id, $selected) {
        $stmt = $this->db->prepare("
            INSERT INTO attempt_answers (attempt_id, question_id, selected)
            VALUES (:attempt_id, :question_id, :selected)
        ");
        $stmt->execute([
            ':attempt_id' => $attempt_id,
            ':question_id' => $question_id,
            ':selected' => $selected
        ]);
    }

    // Récupérer toutes les tentatives
    public function getAllAttempts() {
        $stmt = $this->db->query("
            SELECT a.*, c.titre AS chapitre_titre
            FROM attempts a
            LEFT JOIN cours c ON a.id_chapitre = c.id
            ORDER BY a.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une tentative spécifique
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM attempts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer les réponses d'une tentative
    public function getAnswers($attempt_id) {
        $stmt = $this->db->prepare("SELECT * FROM attempt_answers WHERE attempt_id = :attempt_id");
        $stmt->execute([':attempt_id' => $attempt_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Supprimer une tentative et ses réponses
    public function delete($attempt_id) {
        // Supprimer les réponses associées
        $stmt = $this->db->prepare("DELETE FROM attempt_answers WHERE attempt_id = :id");
        $stmt->execute([':id' => $attempt_id]);

        // Supprimer la tentative
        $stmt2 = $this->db->prepare("DELETE FROM attempts WHERE id = :id");
        $stmt2->execute([':id' => $attempt_id]);
    }

} // ← fin de la classe Attempt
