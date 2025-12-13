<?php
// app/models/Matiere.php

class Matiere extends Model {

    // Récupérer toutes les matières
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM matieres ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer une matière par ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM matieres WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ajouter une matière
    public function add($nom, $description) {
        $stmt = $this->db->prepare("INSERT INTO matieres (nom, description) VALUES (?, ?)");
        return $stmt->execute([$nom, $description]);
    }

    // Mettre à jour une matière
    public function update($id, $nom, $description) {
        $stmt = $this->db->prepare("UPDATE matieres SET nom = ?, description = ? WHERE id = ?");
        return $stmt->execute([$nom, $description, $id]);
    }

    // Supprimer une matière et tout ce qui est lié
    public function delete($id) {
        // Supprimer les quiz liés aux cours de cette matière
        $this->db->prepare("
            DELETE FROM quiz 
            WHERE id_chapitre IN (
                SELECT id FROM chapitres WHERE id_matiere=?
            )
        ")->execute([$id]);

        // Supprimer les cours liés aux chapitres de cette matière
        $this->db->prepare("
            DELETE FROM cours 
            WHERE id_chapitre IN (
                SELECT id FROM chapitres WHERE id_matiere=?
            )
        ")->execute([$id]);

        // Supprimer les chapitres
        $this->db->prepare("DELETE FROM chapitres WHERE id_matiere=?")->execute([$id]);

        // Supprimer la matière
        $stmt = $this->db->prepare("DELETE FROM matieres WHERE id=?");
        return $stmt->execute([$id]);
    }
}
