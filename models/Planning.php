<?php
// models/Planning.php

require_once __DIR__ . '/../config/Database.php';

class Planning
{
    /**
     * Récupère tous les plannings / séances triés
     */
    public static function all(): array
    {
        $pdo = Database::getPdo();

        $sql = "SELECT * FROM planning 
                ORDER BY FIELD(jour_semaine, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'),
                         heure ASC";
        $stmt = $pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve un planning / une séance par ID
     */
    public static function find(int $id): ?array
    {
        $pdo = Database::getPdo();

        $stmt = $pdo->prepare("SELECT * FROM planning WHERE id = ?");
        $stmt->execute([$id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Crée un planning (type = 'planning')
     */
    public static function create(array $data): bool
    {
        $pdo = Database::getPdo();

        $sql = "INSERT INTO planning 
                    (jour_semaine, heure, duree, matiere, theme, difficulte, priorite, objectif, type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'planning')";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $data['jour_semaine'],
            $data['heure'],
            $data['duree'],
            $data['matiere'],
            $data['theme'],
            $data['difficulte'] ?? null,
            $data['priorite']   ?? null,
            $data['objectif']   ?? null,
        ]);
    }

    /**
     * Met à jour un planning (pas les séances)
     */
    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getPdo();

        $sql = "UPDATE planning
                SET jour_semaine = ?, 
                    heure        = ?, 
                    duree        = ?, 
                    matiere      = ?, 
                    theme        = ?, 
                    difficulte   = ?, 
                    priorite     = ?, 
                    objectif     = ?
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $data['jour_semaine'],
            $data['heure'],
            $data['duree'],
            $data['matiere'],
            $data['theme'],
            $data['difficulte'] ?? null,
            $data['priorite']   ?? null,
            $data['objectif']   ?? null,
            $id,
        ]);
    }

    /**
     * Supprime un planning ou une séance
     */
    public static function delete(int $id): bool
    {
        $pdo = Database::getPdo();

        $stmt = $pdo->prepare("DELETE FROM planning WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Statistiques par matière (pour l'admin)
     * Retourne un tableau associatif : [ 'Math' => 3, 'Espagnol' => 2, ... ]
     */
    public static function statsParMatiere(): array
    {
        $pdo = Database::getPdo();

        $sql = "SELECT matiere, COUNT(*) AS total
                FROM planning
                GROUP BY matiere";

        $stmt = $pdo->query($sql);

        // PDO::FETCH_KEY_PAIR => [ matière => total ]
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
