<?php
class Rapport {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }


    public function create($data) {
        $sql = "INSERT INTO rapport (email, titre, message, created_at, vu, pin) 
                VALUES (?, ?, ?, CURDATE(), 0, 0)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                $data['email'],
                $data['titre'],
                $data['message']
            ]);

            if (!$success) {
                error_log("Échec INSERT rapport: " . json_encode($stmt->errorInfo()));
                return false;
            }

            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            error_log("Erreur SQL create(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère un rapport par ID
     */
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM rapport WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les rapports
     */
    public function all() {
        $stmt = $this->pdo->prepare("SELECT * FROM rapport ORDER BY created_at DESC, pin DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les rapports non lus
     */
    public function getUnread() {
        $stmt = $this->pdo->prepare("SELECT * FROM rapport WHERE vu = 0 ORDER BY created_at DESC, pin DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les rapports épinglés
     */
    public function getPinned() {
        $stmt = $this->pdo->prepare("SELECT * FROM rapport WHERE pin = 1 ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marque un rapport comme lu
     */
    public function markAsRead($id) {
        $stmt = $this->pdo->prepare("UPDATE rapport SET vu = 1 WHERE id = ?");
        try {
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Erreur SQL markAsRead(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Marque un rapport comme non lu
     */
    public function markAsUnread($id) {
        $stmt = $this->pdo->prepare("UPDATE rapport SET vu = 0 WHERE id = ?");
        try {
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Erreur SQL markAsUnread(): " . $e->getMessage());
            return false;
        }
    }

 
    public function togglePin($id) {
        // Récupérer l'état actuel
        $current = $this->find($id);
        if (!$current) return false;

        $newPin = $current['pin'] == 1 ? 0 : 1;
        
        $stmt = $this->pdo->prepare("UPDATE rapport SET pin = ? WHERE id = ?");
        try {
            return $stmt->execute([$newPin, $id]);
        } catch (Exception $e) {
            error_log("Erreur SQL togglePin(): " . $e->getMessage());
            return false;
        }
    }


    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM rapport WHERE id = ?");
        try {
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Erreur SQL delete(): " . $e->getMessage());
            return false;
        }
    }


    public function getStats() {
        $stats = [];

        // Total des rapports
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM rapport");
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Rapports non lus
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as unread FROM rapport WHERE vu = 0");
        $stmt->execute();
        $stats['unread'] = $stmt->fetch(PDO::FETCH_ASSOC)['unread'];

        // Rapports épinglés
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as pinned FROM rapport WHERE pin = 1");
        $stmt->execute();
        $stats['pinned'] = $stmt->fetch(PDO::FETCH_ASSOC)['pinned'];

        // Rapports du mois
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as this_month FROM rapport WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $stmt->execute();
        $stats['this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['this_month'];

        return $stats;
    }

  
    public function search($query) {
        $stmt = $this->pdo->prepare("SELECT * FROM rapport 
                                   WHERE titre LIKE ? OR message LIKE ? OR email LIKE ? 
                                   ORDER BY created_at DESC, pin DESC");
        $searchTerm = "%$query%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM rapport WHERE email = ? ORDER BY created_at DESC");
        $stmt->execute([$email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // model/Profile.php
public function update($user_id, $data) {
    try {
        // Construire la requête SQL dynamiquement
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $fields[] = "$key = ?";
                $values[] = $value;
            } else {
                $fields[] = "$key = NULL";
            }
        }
        
        $values[] = $user_id;
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
        
    } catch (PDOException $e) {
        error_log("PDO Error in update: " . $e->getMessage());
        return false;
    }
}
}