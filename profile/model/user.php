<?php
// model/User.php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function create($data) {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO users 
            (nom, email, password, role, date_naissance, etablissement, niveau, twitter, linkedin, github, date_creation) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([
                $data['nom'],
                $data['email'],
                $hash,
                $data['role'],
                $data['date_naissance'] ?? null,
                $data['etablissement'] ?? null,
                $data['niveau'] ?? null,
                $data['twitter'] ?? null,
                $data['linkedin'] ?? null,
                $data['github'] ?? null
            ]);

            if (!$success) {
                error_log("Échec INSERT: " . json_encode($stmt->errorInfo()));
                return false;
            }

            $id = $this->pdo->lastInsertId();
            return $this->find($id);
        } catch (Exception $e) {
            error_log("Erreur SQL create(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère un utilisateur par ID
     */
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT id, nom, email, role, date_creation FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function all() {
        $stmt = $this->pdo->prepare("SELECT id, nom, email, role, date_creation FROM users ORDER BY date_creation DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour un utilisateur
     */
    public function update($id, $data) {
        $sql = "UPDATE users SET nom = ?, email = ?, role = ? WHERE id = ?";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $data['nom'],
                $data['email'],
                $data['role'],
                $id
            ]);
        } catch (Exception $e) {
            error_log("Erreur SQL update(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un utilisateur
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        try {
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Erreur SQL delete(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Connexion utilisateur
     */
    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
}