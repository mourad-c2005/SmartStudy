<?php
// model/Profile.php
class Profile
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Garantit qu’un utilisateur a un profil (crée s’il n’existe pas) */
    public function ensureExists(int $userId): void
{
    $check = $this->pdo->prepare("SELECT 1 FROM profile WHERE id = ?");
    $check->execute([$userId]);
    if ($check->fetchColumn()) return; // déjà existant

    // Récupérer les données depuis users
    $stmt = $this->pdo->prepare("
        SELECT nom, email, role, date_naissance, etablissement, niveau, 
               twitter, linkedin, github 
        FROM users WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) return;

    // Préparer les valeurs (NULL si vide)
    $date_naissance = $data['date_naissance'] ?: null;
    $etablissement  = $data['etablissement'] ?: null;
    $niveau         = $data['niveau'] ?: null;
    $twitter        = $data['twitter'] ?: null;
    $linkedin       = $data['linkedin'] ?: null;
    $github         = $data['github'] ?: null;

    $sql = "INSERT INTO profile 
            (id, nom, email, role, date_naissance, etablissement, niveau, 
             twitter, linkedin, github, date_creation, text, img_per)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), NULL, NULL)";

    $this->pdo->prepare($sql)->execute([
        $userId,
        $data['nom'],
        $data['email'],
        $data['role'] ?? 'etudiant',
        $date_naissance,
        $etablissement,
        $niveau,
        $twitter,
        $linkedin,
        $github
    ]);
}

    /** Récupère le profil complet */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profile WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** Met à jour le profil + synchronise users (nom, email, role) */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE profile SET 
                nom = ?, email = ?, text = ?, role = ?, date_naissance = ?,
                etablissement = ?, niveau = ?, twitter = ?, linkedin = ?, github = ?
                WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            $data['nom'], $data['email'], $data['text'] ?? null, $data['role'],
            $data['date_naissance'], $data['etablissement'], $data['niveau'],
            $data['twitter'] ?? null, $data['linkedin'] ?? null, $data['github'] ?? null,
            $id
        ]);

        // Synchroniser aussi dans users (important pour le login)
        if ($ok) {
            $this->pdo->prepare("UPDATE users SET nom = ?, email = ?, role = ? WHERE id = ?")
                      ->execute([$data['nom'], $data['email'], $data['role'], $id]);
        }
        return $ok;
    }
}