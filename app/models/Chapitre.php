<?php
class Chapitre extends Model {

    public function getAllByMatiere($id_matiere) {
        $stmt = $this->db->prepare("SELECT * FROM chapitres WHERE id_matiere=? ORDER BY id ASC");
        $stmt->execute([$id_matiere]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM chapitres WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($id_matiere, $titre) {
        $stmt = $this->db->prepare("INSERT INTO chapitres (id_matiere, titre) VALUES (?, ?)");
        return $stmt->execute([$id_matiere, $titre]);
    }

    public function update($id, $titre) {
        $stmt = $this->db->prepare("UPDATE chapitres SET titre=? WHERE id=?");
        return $stmt->execute([$titre, $id]);
    }

    public function delete($id) {
        // Supprimer les quiz liés
        $this->db->prepare("DELETE FROM quiz WHERE id_chapitre=?")->execute([$id]);
        // Supprimer les cours liés
        $this->db->prepare("DELETE FROM cours WHERE id_chapitre=?")->execute([$id]);
        // Supprimer le chapitre
        $stmt = $this->db->prepare("DELETE FROM chapitres WHERE id=?");
        return $stmt->execute([$id]);
    }
}
