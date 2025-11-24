<?php
/**
 * Create users and panier tables for user-specific carts
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    echo "<h1>Création des tables users et panier</h1>";
    echo "<style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        table { border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
    </style>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `prenom` VARCHAR(100) NOT NULL,
        `nom` VARCHAR(100) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `role` ENUM('etudiant', 'enseignant', 'admin') DEFAULT 'etudiant',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<p class='success'>✓ Table 'users' créée avec succès !</p>";
    
    // Create panier table
    $sql = "CREATE TABLE IF NOT EXISTS `panier` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `formation_id` INT(11) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`),
        KEY `formation_id` (`formation_id`),
        UNIQUE KEY `user_formation` (`user_id`, `formation_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "<p class='success'>✓ Table 'panier' créée avec succès !</p>";
    
    // Create admin user if not exists
    $adminEmail = 'admin@smartstudy.com';
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Admin', 'SmartStudy', $adminEmail, $adminPassword, 'admin']);
        echo "<p class='success'>✓ Utilisateur admin créé (admin@smartstudy.com / admin123)</p>";
    } else {
        echo "<p>ℹ Utilisateur admin existe déjà</p>";
    }
    
    // Show structure
    echo "<h2>Structure de la table 'users':</h2>";
    $result = $pdo->query("DESCRIBE users");
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
    
    echo "<h2>Structure de la table 'panier':</h2>";
    $result = $pdo->query("DESCRIBE panier");
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
    
    echo "<hr><p class='success'><strong>✓ Tables créées avec succès !</strong></p>";
    echo "<p><a href='index.php?controller=admin&action=dashboard'>Retour au tableau de bord</a> | <a href='index.php?controller=user&action=home'>Accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><small>Si l'erreur concerne une clé étrangère, assurez-vous que la table 'users' existe d'abord.</small></p>";
}

