<?php
class Profile {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère un profil complet
     */
    public function getById($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    u.id, u.nom, u.email, u.date_naissance, 
                    u.etablissement, u.niveau, u.date_creation as user_date_creation,
                    p.text, p.twitter, p.linkedin, p.github, p.img_per,
                    p.date_creation as profile_date_creation
                FROM users u
                LEFT JOIN profile p ON u.id = p.id
                WHERE u.id = ?
            ");
            $stmt->execute([$user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return array_merge([
                    'text' => '',
                    'twitter' => '',
                    'linkedin' => '',
                    'github' => '',
                    'img_per' => '',
                    'profile_date_creation' => $result['user_date_creation'] ?? date('Y-m-d')
                ], $result);
            }
            
            // Fallback: juste l'utilisateur
            return $this->getUserOnly($user_id);
            
        } catch (PDOException $e) {
            error_log("PDO Error in getById: " . $e->getMessage());
            return $this->getUserOnly($user_id);
        }
    }

    /**
     * Récupère seulement l'utilisateur
     */
    private function getUserOnly($user_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nom, email, date_naissance, 
                       etablissement, niveau, date_creation as user_date_creation
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return array_merge($user, [
                    'text' => '',
                    'twitter' => '',
                    'linkedin' => '',
                    'github' => '',
                    'img_per' => '',
                    'profile_date_creation' => $user['user_date_creation'] ?? date('Y-m-d')
                ]);
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("PDO Error in getUserOnly: " . $e->getMessage());
            return false;
        }
    }

    /**
     * S'assure qu'un profil existe
     */
    public function ensureExists($user_id) {
        try {
            // Vérifier si l'utilisateur existe
            $userStmt = $this->pdo->prepare("SELECT id FROM users WHERE id = ?");
            $userStmt->execute([$user_id]);
            
            if (!$userStmt->fetch()) {
                error_log("Utilisateur $user_id n'existe pas");
                return false;
            }
            
            // Vérifier si le profil existe
            $profileStmt = $this->pdo->prepare("SELECT id FROM profile WHERE id = ?");
            $profileStmt->execute([$user_id]);
            
            if (!$profileStmt->fetch()) {
                // Créer un profil vide
                $insertStmt = $this->pdo->prepare("
                    INSERT INTO profile (id, date_creation) 
                    VALUES (?, NOW())
                ");
                $result = $insertStmt->execute([$user_id]);
                error_log("Profil créé pour $user_id: " . ($result ? "succès" : "échec"));
                return $result;
            }
            
            return true;
            
        } catch (PDOException $e) {
            error_log("PDO Error in ensureExists: " . $e->getMessage());
            
            // Si la table profile n'existe pas, on crée
            if (strpos($e->getMessage(), 'profile') !== false) {
                error_log("Table 'profile' n'existe pas");
                return $this->createProfileTable();
            }
            
            return false;
        }
    }

    /**
     * Crée la table profile si elle n'existe pas
     */
    private function createProfileTable() {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS profile (
                    id INT PRIMARY KEY,
                    text TEXT,
                    twitter VARCHAR(50),
                    linkedin VARCHAR(50),
                    github VARCHAR(50),
                    img_per VARCHAR(255),
                    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (id) REFERENCES users(id) ON DELETE CASCADE
                )
            ";
            $this->pdo->exec($sql);
            error_log("Table 'profile' créée avec succès");
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création table profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour un profil
     */
    public function update($user_id, $data) {
        try {
            $this->pdo->beginTransaction();
            
            // Debug
            error_log("=== DEBUT UPDATE PROFIL ===");
            error_log("User ID: $user_id");
            error_log("Données reçues: " . json_encode($data));
            
            // 1. Mettre à jour la table USERS
            $userUpdated = $this->updateUsersTable($user_id, $data);
            error_log("Update users: " . ($userUpdated ? "succès" : "échec"));
            
            // 2. Mettre à jour la table PROFILE
            $profileUpdated = $this->updateProfileTable($user_id, $data);
            error_log("Update profile: " . ($profileUpdated ? "succès" : "échec"));
            
            // 3. S'assurer que le profil existe
            $this->ensureExists($user_id);
            
            $this->pdo->commit();
            error_log("=== FIN UPDATE PROFIL - SUCCÈS ===");
            
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("=== ERREUR UPDATE PROFIL ===");
            error_log("Erreur: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Met à jour la table users
     */
    private function updateUsersTable($user_id, $data) {
        try {
            $fields = [];
            $values = [];
            
            // Champs de la table users
            $userFields = ['nom', 'email', 'date_naissance', 'etablissement', 'niveau'];
            
            foreach ($userFields as $field) {
                if (isset($data[$field])) {
                    $value = trim($data[$field]);
                    
                    if ($field === 'date_naissance') {
                        if (empty($value)) {
                            $fields[] = "$field = NULL";
                        } else {
                            $fields[] = "$field = ?";
                            $values[] = $value;
                        }
                    } else {
                        if (!empty($value)) {
                            $fields[] = "$field = ?";
                            $values[] = $value;
                        }
                    }
                }
            }
            
            if (!empty($fields)) {
                $values[] = $user_id;
                $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
                error_log("SQL Users: $sql");
                error_log("Values Users: " . json_encode($values));
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($values);
            }
            
            error_log("Aucun champ users à mettre à jour");
            return true;
            
        } catch (PDOException $e) {
            error_log("Erreur updateUsersTable: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Met à jour la table profile
     */
    private function updateProfileTable($user_id, $data) {
        try {
            $fields = [];
            $values = [];
            
            // Champs de la table profile
            $profileFields = ['text', 'twitter', 'linkedin', 'github', 'img_per'];
            
            foreach ($profileFields as $field) {
                if (isset($data[$field])) {
                    $value = trim($data[$field]);
                    $fields[] = "$field = ?";
                    $values[] = $value;
                    error_log("Champ profile '$field' = '$value'");
                }
            }
            
            if (!empty($fields)) {
                $values[] = $user_id;
                
                // Vérifier si le profil existe
                $checkStmt = $this->pdo->prepare("SELECT id FROM profile WHERE id = ?");
                $checkStmt->execute([$user_id]);
                
                if ($checkStmt->fetch()) {
                    // UPDATE
                    $sql = "UPDATE profile SET " . implode(', ', $fields) . " WHERE id = ?";
                } else {
                    // INSERT
                    $fieldNames = implode(', ', array_map(function($f) {
                        return explode(' = ', $f)[0];
                    }, $fields));
                    
                    $placeholders = implode(', ', array_fill(0, count($values) - 1, '?'));
                    $sql = "INSERT INTO profile (id, $fieldNames) VALUES (?, $placeholders)";
                }
                
                error_log("SQL Profile: $sql");
                error_log("Values Profile: " . json_encode($values));
                
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute($values);
            }
            
            error_log("Aucun champ profile à mettre à jour");
            return true;
            
        } catch (PDOException $e) {
            error_log("Erreur updateProfileTable: " . $e->getMessage());
            
            // Si la table n'existe pas, la créer
            if (strpos($e->getMessage(), 'profile') !== false) {
                error_log("Table profile n'existe pas, création...");
                if ($this->createProfileTable()) {
                    // Réessayer après création
                    return $this->updateProfileTable($user_id, $data);
                }
            }
            
            throw $e;
        }
    }

    /**
     * Version simple pour récupérer
     */
    public function getSimpleById($user_id) {
        return $this->getById($user_id);
    }
}
?>