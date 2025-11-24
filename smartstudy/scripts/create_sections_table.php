<?php
/**
 * Create proper sections table for SmartStudy
 * This will create a new table or migrate existing data
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    echo "<h1>Création de la table sections pour SmartStudy</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
    </style>";
    
    // Check if sections table exists with wrong structure
    $tableExists = $pdo->query("SHOW TABLES LIKE 'sections'")->rowCount() > 0;
    
    if ($tableExists) {
        $columns = $pdo->query("SHOW COLUMNS FROM sections")->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('title', $columns) && !in_array('nom', $columns)) {
            echo "<p class='info'>La table 'sections' existe mais avec une structure différente (title au lieu de nom).</p>";
            echo "<p class='info'>Option 1: Renommer la table existante et créer une nouvelle</p>";
            
            // Rename existing table
            try {
                $pdo->exec("RENAME TABLE `sections` TO `sections_old`");
                echo "<p class='success'>✓ Table 'sections' renommée en 'sections_old'</p>";
            } catch (PDOException $e) {
                echo "<p class='error'>Erreur lors du renommage: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
    
    // Create the proper sections table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `sections` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "<p class='success'>✓ Table 'sections' créée avec la bonne structure (id, nom)</p>";
    
    // Show final structure
    echo "<h2>Structure finale de la table 'sections':</h2>";
    $result = $pdo->query("DESCRIBE sections");
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
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
    
    echo "<hr><h2 class='success'>✓ Table 'sections' est maintenant prête !</h2>";
    echo "<p><a href='index.php?controller=admin&action=dashboard' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Retour au tableau de bord</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
}

