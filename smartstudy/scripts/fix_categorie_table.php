<?php
/**
 * Fix categorie table structure
 * Adds missing columns if they don't exist
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    // Check if table exists and get its structure
    $result = $pdo->query("SHOW COLUMNS FROM categorie");
    $columns = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h2>Structure actuelle de la table 'categorie':</h2>";
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li>" . htmlspecialchars($col) . "</li>";
    }
    echo "</ul>";
    
    // Add missing columns
    if (!in_array('nom', $columns)) {
        $pdo->exec("ALTER TABLE categorie ADD COLUMN nom VARCHAR(255) NOT NULL AFTER id");
        echo "<p style='color: green;'>✓ Colonne 'nom' ajoutée</p>";
    }
    
    if (!in_array('section_id', $columns)) {
        $pdo->exec("ALTER TABLE categorie ADD COLUMN section_id INT(11) NOT NULL AFTER nom");
        echo "<p style='color: green;'>✓ Colonne 'section_id' ajoutée</p>";
    }
    
    // Show final structure
    $result = $pdo->query("DESCRIBE categorie");
    echo "<h2>Structure finale:</h2>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
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
    
    echo "<br><p style='color: green;'><strong>✓ Table 'categorie' est maintenant prête !</strong></p>";
    echo "<p><a href='index.php?controller=admin&action=dashboard'>Retour au tableau de bord</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Erreur:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    // If table doesn't exist, create it
    if (strpos($e->getMessage(), "doesn't exist") !== false) {
        echo "<p>La table n'existe pas. Création...</p>";
        try {
            $pdo->exec("CREATE TABLE `categorie` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `nom` VARCHAR(255) NOT NULL,
                `section_id` INT(11) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            echo "<p style='color: green;'>✓ Table 'categorie' créée avec succès !</p>";
        } catch (PDOException $e2) {
            echo "<p style='color: red;'>Erreur lors de la création: " . htmlspecialchars($e2->getMessage()) . "</p>";
        }
    }
}

