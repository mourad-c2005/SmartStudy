<?php
/**
 * Fix all database tables - Remove incorrect constraints and create proper structure
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    echo "<h1>Correction complète de la base de données SmartStudy</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
    </style>";
    
    // Step 1: Drop and recreate sections table (remove foreign key constraint)
    echo "<h2>Étape 1: Correction de la table 'sections'</h2>";
    
    // Drop foreign key constraints first
    try {
        $pdo->exec("ALTER TABLE `sections` DROP FOREIGN KEY IF EXISTS `sections_ibfk_1`");
        echo "<p class='success'>✓ Contrainte de clé étrangère supprimée</p>";
    } catch (PDOException $e) {
        echo "<p class='info'>Aucune contrainte à supprimer ou erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Drop the table and recreate with correct structure
    $pdo->exec("DROP TABLE IF EXISTS `sections`");
    echo "<p class='info'>Table 'sections' supprimée</p>";
    
    $pdo->exec("CREATE TABLE `sections` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<p class='success'>✓ Table 'sections' créée avec la bonne structure (id, nom)</p>";
    
    // Step 2: Ensure categorie table is correct
    echo "<hr><h2>Étape 2: Vérification de la table 'categorie'</h2>";
    
    $categorieExists = $pdo->query("SHOW TABLES LIKE 'categorie'")->rowCount() > 0;
    
    if ($categorieExists) {
        $columns = $pdo->query("SHOW COLUMNS FROM categorie")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('nom', $columns)) {
            $pdo->exec("ALTER TABLE `categorie` ADD COLUMN `nom` VARCHAR(255) NOT NULL AFTER `id`");
            echo "<p class='success'>✓ Colonne 'nom' ajoutée à 'categorie'</p>";
        }
        
        if (!in_array('section_id', $columns)) {
            $pdo->exec("ALTER TABLE `categorie` ADD COLUMN `section_id` INT(11) NOT NULL AFTER `nom`");
            echo "<p class='success'>✓ Colonne 'section_id' ajoutée à 'categorie'</p>";
        }
    } else {
        $pdo->exec("CREATE TABLE `categorie` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `nom` VARCHAR(255) NOT NULL,
            `section_id` INT(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `section_id` (`section_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='success'>✓ Table 'categorie' créée</p>";
    }
    
    // Step 3: Ensure formation table is correct
    echo "<hr><h2>Étape 3: Vérification de la table 'formation'</h2>";
    
    $formationExists = $pdo->query("SHOW TABLES LIKE 'formation'")->rowCount() > 0;
    
    if (!$formationExists) {
        $pdo->exec("CREATE TABLE `formation` (
            `id_formation` INT(11) NOT NULL AUTO_INCREMENT,
            `titre` VARCHAR(255) NOT NULL,
            `url` VARCHAR(500) DEFAULT NULL,
            `prix` DECIMAL(10,2) DEFAULT 0.00,
            `id_categorie` INT(11) NOT NULL,
            PRIMARY KEY (`id_formation`),
            KEY `id_categorie` (`id_categorie`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "<p class='success'>✓ Table 'formation' créée</p>";
    } else {
        $columns = $pdo->query("SHOW COLUMNS FROM formation")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('titre', $columns)) {
            $pdo->exec("ALTER TABLE `formation` ADD COLUMN `titre` VARCHAR(255) NOT NULL AFTER `id_formation`");
            echo "<p class='success'>✓ Colonne 'titre' ajoutée à 'formation'</p>";
        }
        if (!in_array('url', $columns)) {
            $pdo->exec("ALTER TABLE `formation` ADD COLUMN `url` VARCHAR(500) DEFAULT NULL AFTER `titre`");
            echo "<p class='success'>✓ Colonne 'url' ajoutée à 'formation'</p>";
        }
        if (!in_array('prix', $columns)) {
            $pdo->exec("ALTER TABLE `formation` ADD COLUMN `prix` DECIMAL(10,2) DEFAULT 0.00 AFTER `url`");
            echo "<p class='success'>✓ Colonne 'prix' ajoutée à 'formation'</p>";
        }
        if (!in_array('id_categorie', $columns)) {
            $pdo->exec("ALTER TABLE `formation` ADD COLUMN `id_categorie` INT(11) NOT NULL AFTER `prix`");
            echo "<p class='success'>✓ Colonne 'id_categorie' ajoutée à 'formation'</p>";
        }
    }
    
    // Step 4: Show final structure of all tables
    echo "<hr><h2>Structure finale des tables</h2>";
    
    $tables = ['sections', 'categorie', 'formation'];
    
    foreach ($tables as $table) {
        $exists = $pdo->query("SHOW TABLES LIKE '$table'")->rowCount() > 0;
        
        if ($exists) {
            echo "<h3>Table: $table</h3>";
            $result = $pdo->query("DESCRIBE $table");
            echo "<table>";
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
        }
    }
    
    echo "<hr><h2 class='success'>✓ Toutes les tables ont été corrigées avec succès !</h2>";
    echo "<p><a href='index.php?controller=admin&action=dashboard' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Retour au tableau de bord</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Code: " . $e->getCode() . "</p>";
}

