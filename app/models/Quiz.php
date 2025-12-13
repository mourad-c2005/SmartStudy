<?php
// app/models/Quiz.php

require_once __DIR__ . "/../core/Model.php";

class Quiz extends Model {

    protected $table = "quiz";

    // Récupérer tous les quiz d’un cours
    public function getAllByCours($id_cours) {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id_cours=? ORDER BY id ASC");
        $stmt->execute([$id_cours]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les quiz d’un chapitre
    public function getAllByChapitre($id_chapitre) {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id_chapitre=? ORDER BY id ASC");
        $stmt->execute([$id_chapitre]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un quiz par ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ajouter un quiz
    public function add($id_cours, $id_chapitre, $question, $rep1, $rep2, $rep3 = null, $rep4 = null, $correcte = 1) {
        $stmt = $this->db->prepare(
            "INSERT INTO quiz (id_cours, id_chapitre, question, rep1, rep2, rep3, rep4, correcte) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$id_cours, $id_chapitre, $question, $rep1, $rep2, $rep3, $rep4, $correcte]);
    }

    // Modifier un quiz existant
    public function update($id, $question, $rep1, $rep2, $rep3 = null, $rep4 = null, $correcte = 1) {
        $stmt = $this->db->prepare(
            "UPDATE quiz 
             SET question=?, rep1=?, rep2=?, rep3=?, rep4=?, correcte=? 
             WHERE id=?"
        );
        return $stmt->execute([$question, $rep1, $rep2, $rep3, $rep4, $correcte, $id]);
    }

    // Supprimer un quiz
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM quiz WHERE id=?");
        return $stmt->execute([$id]);
    }
    /**
     * Méthode de jointure : récupérer les quiz d'un cours avec le titre du cours
     */
    public function getByCoursWithJoin($id_cours) {
        $stmt = $this->db->prepare("
            SELECT q.*, c.titre AS cours_titre
            FROM quiz q
            JOIN cours c ON q.id_cours = c.id
            WHERE q.id_cours = ?
            ORDER BY q.id ASC
        ");
        $stmt->execute([$id_cours]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Optionnel : récupérer un quiz avec infos cours + chapitre
     */
    public function getByIdWithCoursChapitre($id) {
        $stmt = $this->db->prepare("
            SELECT q.*, c.titre AS cours_titre, ch.titre AS chapitre_titre
            FROM quiz q
            JOIN cours c ON q.id_cours = c.id
            LEFT JOIN chapitre ch ON q.id_chapitre = ch.id
            WHERE q.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>