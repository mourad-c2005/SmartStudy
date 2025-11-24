<?php
/**
 * Create panier table for storing user carts
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    echo "<h1>Création de la table panier</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>";
    
    // Create panier table
    $sql = "CREATE TABLE IF NOT EXISTS `panier` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `formation_id` INT(11) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `formation_id` (`formation_id`),
        UNIQUE KEY `user_formation` (`user_id`, `formation_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<p class='success'>✓ Table 'panier' créée avec succès !</p>";
    
    // Show structure
    $result = $pdo->query("DESCRIBE panier");
    echo "<h2>Structure de la table 'panier':</h2>";
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
    
    echo "<hr><p class='success'><strong>✓ Table panier prête !</strong></p>";
    echo "<p><a href='index.php?controller=admin&action=dashboard'>Retour au tableau de bord</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
}

