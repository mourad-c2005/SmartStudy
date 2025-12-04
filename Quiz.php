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

    // Ajouter un quiz
    public function add($id_cours, $id_chapitre, $question, $rep1, $rep2, $rep3, $rep4, $correcte) {
        $stmt = $this->db->prepare(
            "INSERT INTO quiz (id_cours, id_chapitre, question, rep1, rep2, rep3, rep4, correcte) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$id_cours, $id_chapitre, $question, $rep1, $rep2, $rep3, $rep4, $correcte]);
    }

    // Modifier un quiz existant
    public function update($id, $question, $rep1, $rep2, $rep3, $rep4, $correcte) {
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
}
