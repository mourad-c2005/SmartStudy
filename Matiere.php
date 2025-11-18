<?php
// app/models/Matiere.php
class Matiere extends Model {
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM matieres ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM matieres WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function add($nom, $description) {
        $stmt = $this->db->prepare("INSERT INTO matieres (nom, description) VALUES (?, ?)");
        return $stmt->execute([$nom, $description]);
    }
    public function update($id, $nom, $description) {
        $stmt = $this->db->prepare("UPDATE matieres SET nom = ?, description = ? WHERE id = ?");
        return $stmt->execute([$nom, $description, $id]);
    }
    public function delete($id) {
        // delete chapitres, cours, quiz associés (simple cascade)
        $this->db->prepare("DELETE FROM quiz WHERE id_chapitre IN (SELECT id FROM chapitres WHERE id_matiere=?)")->execute([$id]);
        $this->db->prepare("DELETE FROM cours WHERE id_chapitre IN (SELECT id FROM chapitres WHERE id_matiere=?)")->execute([$id]);
        $this->db->prepare("DELETE FROM chapitres WHERE id_matiere=?")->execute([$id]);
        $stmt = $this->db->prepare("DELETE FROM matieres WHERE id=?");
        return $stmt->execute([$id]);
    }
}
