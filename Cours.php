<?php
// app/models/Cours.php
class Cours extends Model {
    public function getAllByChapitre($id_chapitre) {
        $stmt = $this->db->prepare("SELECT * FROM cours WHERE id_chapitre=? ORDER BY id ASC");
        $stmt->execute([$id_chapitre]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM cours WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function add($id_chapitre, $titre, $contenu, $lien_video) {
        $stmt = $this->db->prepare("INSERT INTO cours (id_chapitre, titre, contenu, lien_video) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$id_chapitre, $titre, $contenu, $lien_video]);
    }
    public function update($id, $titre, $contenu, $lien_video) {
        $stmt = $this->db->prepare("UPDATE cours SET titre=?, contenu=?, lien_video=? WHERE id=?");
        return $stmt->execute([$titre, $contenu, $lien_video, $id]);
    }
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM cours WHERE id=?");
        return $stmt->execute([$id]);
    }
}
