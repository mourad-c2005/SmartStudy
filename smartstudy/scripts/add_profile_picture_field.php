<?php
/**
 * Add profile_picture field to users table
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    echo "<h1>Ajout du champ profile_picture</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✓ Le champ 'profile_picture' existe déjà.</p>";
    } else {
        // Add profile_picture column
        $sql = "ALTER TABLE `users` ADD COLUMN `profile_picture` VARCHAR(255) NULL DEFAULT NULL AFTER `role`";
        $pdo->exec($sql);
        echo "<p class='success'>✓ Champ 'profile_picture' ajouté avec succès !</p>";
    }
    
    echo "<hr><p><a href='index.php?controller=user&action=home'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
}

