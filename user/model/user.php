<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch (PDOException $e) {
            error_log("PDO Error in emailExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function create($data) {
        try {
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users 
                    (nom, email, password, role, date_naissance, etablissement, niveau, twitter, linkedin, github, date_creation, autorisation) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), 1)";

            $stmt = $this->pdo->prepare($sql);
            
            $params = [
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
            ];

            $success = $stmt->execute($params);

            if ($success) {
                $id = $this->pdo->lastInsertId();
                return $this->find($id);
            }
            
            return false;

        } catch (PDOException $e) {
            error_log("PDO Error in create: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère un utilisateur par ID
     */
    public function find($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nom, email, role, date_creation, autorisation FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDO Error in find: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère un utilisateur par email
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nom, email, role, autorisation FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDO Error in findByEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les utilisateurs
     */
    public function all() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nom, email, role, date_creation, autorisation FROM users ORDER BY date_creation DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDO Error in all: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Met à jour un utilisateur
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE users SET nom = ?, email = ?, role = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                $data['nom'],
                $data['email'],
                $data['role'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("PDO Error in update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Active ou désactive un utilisateur
     */
    public function setAutorisation($id, $autorisation) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET autorisation = ? WHERE id = ?");
            return $stmt->execute([$autorisation ? 1 : 0, $id]);
        } catch (PDOException $e) {
            error_log("PDO Error in setAutorisation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si un utilisateur est autorisé à se connecter
     */
    public function isAuthorized($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT autorisation FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result && $result['autorisation'] == 1;
        } catch (PDOException $e) {
            error_log("PDO Error in isAuthorized: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un utilisateur
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("PDO Error in delete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Connexion utilisateur - vérifie l'autorisation
     */
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Vérifier l'autorisation
                if ($user['autorisation'] != 1) {
                    return false; // Utilisateur non autorisé
                }
                unset($user['password']);
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            error_log("PDO Error in login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les utilisateurs avec leur statut d'autorisation
     */
    public function getAllWithAutorisation() {
        try {
            $stmt = $this->pdo->prepare("SELECT id, nom, email, role, date_creation, autorisation FROM users ORDER BY date_creation DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDO Error in getAllWithAutorisation: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crée un token de réinitialisation de mot de passe
     */
    public function createPasswordResetToken($email, $token_hash, $expiry) {
        try {
            // First, check if the columns exist
            $checkStmt = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'reset_token_hash'");
            if ($checkStmt->rowCount() == 0) {
                error_log("ERROR: reset_token_hash column does not exist!");
                return false;
            }

            $sql = "UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $success = $stmt->execute([$token_hash, $expiry, $email]);
            
            return $success && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("PDO Error in createPasswordResetToken: " . $e->getMessage());
            
            // Check if it's a column missing error
            if (strpos($e->getMessage(), 'reset_token_hash') !== false) {
                error_log("CRITICAL: reset_token_hash column is missing from database!");
            }
            
            return false;
        }
    }

    /**
     * Vérifie et récupère un utilisateur par son token de réinitialisation
     */
    public function findByResetToken($token) {
        try {
            // Check if token is provided
            if (empty($token)) {
                error_log("Empty token provided to findByResetToken");
                return false;
            }

            $tokenHash = hash('sha256', $token);
            $now = date('Y-m-d H:i:s');

            // Debug logging
            error_log("Searching for token hash: " . $tokenHash);
            error_log("Current time: " . $now);

            $stmt = $this->pdo->prepare("
                SELECT id, nom, email 
                FROM users 
                WHERE reset_token_hash = ? 
                AND reset_token_expires_at > ? 
                AND autorisation = 1
            ");
            
            $stmt->execute([$tokenHash, $now]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                error_log("User found with reset token: " . $user['email']);
            } else {
                error_log("No user found with this reset token");
            }
            
            return $user ?: false;
        } catch (PDOException $e) {
            error_log("PDO Error in findByResetToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Réinitialise le mot de passe avec un token valide
     */
    public function resetPasswordWithToken($token, $newPassword) {
        try {
            $this->pdo->beginTransaction();

            // First find the user by token
            $user = $this->findByResetToken($token);
            
            if (!$user) {
                $this->pdo->rollBack();
                error_log("No user found for password reset token");
                return false;
            }

            $hash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL 
                WHERE id = ?
            ");
            
            $success = $stmt->execute([$hash, $user['id']]);
            
            if ($success) {
                $this->pdo->commit();
                error_log("Password successfully reset for user: " . $user['email']);
                return true;
            }
            
            $this->pdo->rollBack();
            error_log("Failed to update password for user: " . $user['email']);
            return false;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("PDO Error in resetPasswordWithToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie la structure de la base de données
     */
    public function checkDatabaseStructure() {
        try {
            $requiredColumns = [
                'reset_token_hash',
                'reset_token_expires_at'
            ];
            
            $missingColumns = [];
            
            foreach ($requiredColumns as $column) {
                $stmt = $this->pdo->prepare("SHOW COLUMNS FROM users LIKE ?");
                $stmt->execute([$column]);
                if ($stmt->rowCount() == 0) {
                    $missingColumns[] = $column;
                }
            }
            
            return $missingColumns;
            
        } catch (PDOException $e) {
            error_log("PDO Error in checkDatabaseStructure: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Ajoute les colonnes manquantes pour la réinitialisation de mot de passe
     */
    public function addMissingColumns() {
        try {
            $this->pdo->beginTransaction();

            // Check and add reset_token_hash
            $checkHash = $this->pdo->prepare("SHOW COLUMNS FROM users LIKE 'reset_token_hash'");
            $checkHash->execute();
            if ($checkHash->rowCount() == 0) {
                $this->pdo->exec("ALTER TABLE users ADD reset_token_hash VARCHAR(64) NULL");
                error_log("Added reset_token_hash column");
            }

            // Check and add reset_token_expires_at
            $checkExpires = $this->pdo->prepare("SHOW COLUMNS FROM users LIKE 'reset_token_expires_at'");
            $checkExpires->execute();
            if ($checkExpires->rowCount() == 0) {
                $this->pdo->exec("ALTER TABLE users ADD reset_token_expires_at DATETIME NULL");
                error_log("Added reset_token_expires_at column");
            }

            $this->pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("PDO Error in addMissingColumns: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Debug method to check token in database
     */
    public function debugToken($token) {
        try {
            $tokenHash = hash('sha256', $token);
            $now = date('Y-m-d H:i:s');
            
            echo "<h3>Debug Token Information:</h3>";
            echo "Raw Token: " . $token . "<br>";
            echo "Token Length: " . strlen($token) . "<br>";
            echo "Hashed Token: " . $tokenHash . "<br>";
            echo "Current Time: " . $now . "<br>";
            
            // Check if token exists in database
            $stmt = $this->pdo->prepare("
                SELECT email, reset_token_hash, reset_token_expires_at 
                FROM users 
                WHERE reset_token_hash = ?
            ");
            $stmt->execute([$tokenHash]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                echo "<p style='color: green;'>✓ Token found in database!</p>";
                echo "Email: " . $result['email'] . "<br>";
                echo "Expires: " . $result['reset_token_expires_at'] . "<br>";
                echo "Valid: " . ($result['reset_token_expires_at'] > $now ? 'YES' : 'NO');
            } else {
                echo "<p style='color: red;'>✗ Token NOT found in database!</p>";
                
                // Check all tokens in database for debugging
                $allStmt = $this->pdo->query("SELECT email, reset_token_hash, reset_token_expires_at FROM users WHERE reset_token_hash IS NOT NULL");
                $allTokens = $allStmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($allTokens) > 0) {
                    echo "<p>Existing tokens in database:</p>";
                    foreach ($allTokens as $token) {
                        echo "Email: " . $token['email'] . " - Hash: " . substr($token['reset_token_hash'], 0, 20) . "...<br>";
                    }
                } else {
                    echo "<p>No tokens found in database at all.</p>";
                }
            }
            
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
