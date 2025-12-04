<?php
// app/models/Quiz.php
class Quiz extends Model {
    public function getAllByChapitre($id_chapitre) {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id_chapitre=? ORDER BY id ASC");
        $stmt->execute([$id_chapitre]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function add($id_chapitre, $question, $rep1, $rep2, $rep3, $rep4, $correcte) {
        $stmt = $this->db->prepare("INSERT INTO quiz (id_chapitre, question, rep1, rep2, rep3, rep4, correcte) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$id_chapitre, $question, $rep1, $rep2, $rep3, $rep4, $correcte]);
    }
    public function update($id, $question, $rep1, $rep2, $rep3, $rep4, $correcte) {
        $stmt = $this->db->prepare("UPDATE quiz SET question=?, rep1=?, rep2=?, rep3=?, rep4=?, correcte=? WHERE id=?");
        return $stmt->execute([$question, $rep1, $rep2, $rep3, $rep4, $correcte, $id]);
    }
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM quiz WHERE id=?");
        return $stmt->execute([$id]);
    }
}
