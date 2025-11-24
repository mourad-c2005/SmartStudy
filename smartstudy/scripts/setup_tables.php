<?php
/**
 * Complete Database Setup Script
 * Creates all required tables with proper structure
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    // Create sections table (plural as per your database)
    $sql = "CREATE TABLE IF NOT EXISTS `sections` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "✓ Table 'sections' créée/mise à jour<br>";
    
    // Create categorie table
    $sql = "CREATE TABLE IF NOT EXISTS `categorie` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(255) NOT NULL,
        `section_id` INT(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `section_id` (`section_id`),
        FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "✓ Table 'categorie' créée/mise à jour<br>";
    
    // Create formation table
    $sql = "CREATE TABLE IF NOT EXISTS `formation` (
        `id_formation` INT(11) NOT NULL AUTO_INCREMENT,
        `titre` VARCHAR(255) NOT NULL,
        `url` VARCHAR(500) DEFAULT NULL,
        `prix` DECIMAL(10,2) DEFAULT 0.00,
        `id_categorie` INT(11) NOT NULL,
        PRIMARY KEY (`id_formation`),
        KEY `id_categorie` (`id_categorie`),
        FOREIGN KEY (`id_categorie`) REFERENCES `categorie`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "✓ Table 'formation' créée/mise à jour<br>";
    
    echo "<br><h2 style='color: green;'>✓ Toutes les tables ont été créées avec succès !</h2>";
    echo "<p><a href='index.php?controller=admin&action=dashboard'>Retour au tableau de bord</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Erreur lors de la création des tables</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Try to show what columns exist
    try {
        $result = $pdo->query("DESCRIBE categorie");
        echo "<h3>Structure actuelle de la table 'categorie':</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e2) {
        echo "<p>Impossible de lire la structure de la table: " . htmlspecialchars($e2->getMessage()) . "</p>";
    }
}

