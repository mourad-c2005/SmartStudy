<?php
// model/Profile.php
class Profile
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Garantit qu'un utilisateur a un profil (crée s'il n'existe pas) */
    public function ensureExists(int $userId): void
    {
        // Vérification avec PDO
        $check = $this->pdo->prepare("SELECT 1 FROM profile WHERE id = ?");
        $check->execute([$userId]);
        
        if ($check->fetchColumn()) {
            return; // déjà existant
        }

        // Récupération des données utilisateur avec PDO
        $stmt = $this->pdo->prepare("
            SELECT nom, email, date_naissance, etablissement, niveau, 
                   twitter, linkedin, github 
            FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return;
        }

        // Insertion avec PDO
        $sql = "INSERT INTO profile 
                (id, nom, email, date_naissance, etablissement, niveau, 
                 twitter, linkedin, github, date_creation, text, img_per)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), NULL, NULL)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $userId,
            $data['nom'],
            $data['email'],
            $data['date_naissance'] ?? null,
            $data['etablissement'] ?? null,
            $data['niveau'] ?? null,
            $data['twitter'] ?? null,
            $data['linkedin'] ?? null,
            $data['github'] ?? null
        ]);
    }

    /** Récupère le profil complet avec PDO */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profile WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }

    /** Met à jour le profil avec PDO + synchronise users */
    public function update(int $id, array $data): bool
    {
        try {
            // Début de transaction PDO
            $this->pdo->beginTransaction();

            // Mise à jour du profil avec PDO
            $sql = "UPDATE profile SET 
                    nom = ?, email = ?, text = ?, date_naissance = ?,
                    etablissement = ?, niveau = ?, twitter = ?, linkedin = ?, github = ?
                    WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['nom'], 
                $data['email'], 
                $data['text'] ?? null, 
                $data['date_naissance'] ?? null, 
                $data['etablissement'] ?? null, 
                $data['niveau'] ?? null,
                $data['twitter'] ?? null, 
                $data['linkedin'] ?? null, 
                $data['github'] ?? null,
                $id
            ]);

            // Synchronisation dans users avec PDO
            $userStmt = $this->pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
            $userStmt->execute([$data['nom'], $data['email'], $id]);

            // Validation de la transaction PDO
            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            // Annulation en cas d'erreur
            $this->pdo->rollBack();
            error_log("Erreur PDO update(): " . $e->getMessage());
            return false;
        }
    }
}
