<?php
/**
 * Simple script to create users and panier tables
 * Run this in your browser: http://localhost/SmartStudy/smartstudy/scripts/create_tables_now.php
 */

require_once __DIR__ . "/../config/Database.php";

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Création des tables</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #4CAF50; }
        .success { color: green; font-weight: bold; padding: 10px; background: #e8f5e9; border-left: 4px solid green; margin: 10px 0; }
        .error { color: red; font-weight: bold; padding: 10px; background: #ffebee; border-left: 4px solid red; margin: 10px 0; }
        .info { color: blue; padding: 10px; background: #e3f2fd; border-left: 4px solid blue; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px 0 0; }
        .btn:hover { background: #45a049; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Création des tables users et panier</h1>
        
        <?php
        try {
            $pdo = Database::connect();
            echo "<div class='success'>✓ Connexion à la base de données réussie</div>";
            
            // Step 1: Create users table
            echo "<h2>Étape 1 : Création de la table 'users'</h2>";
            
            $sql = "CREATE TABLE IF NOT EXISTS `users` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `prenom` VARCHAR(100) NOT NULL,
                `nom` VARCHAR(100) NOT NULL,
                `email` VARCHAR(255) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `role` ENUM('etudiant', 'enseignant', 'admin') DEFAULT 'etudiant',
                `profile_picture` VARCHAR(255) NULL DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            $pdo->exec($sql);
            echo "<div class='success'>✓ Table 'users' créée avec succès !</div>";
            
            // Check if profile_picture column exists, if not add it
            $columns = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_picture'")->rowCount();
            if ($columns == 0) {
                $pdo->exec("ALTER TABLE `users` ADD COLUMN `profile_picture` VARCHAR(255) NULL DEFAULT NULL AFTER `role`");
                echo "<div class='info'>ℹ Colonne 'profile_picture' ajoutée à la table 'users'</div>";
            }
            
            // Step 2: Create panier table
            echo "<h2>Étape 2 : Création de la table 'panier'</h2>";
            
            // First, drop panier table if it exists (to avoid foreign key issues)
            try {
                $pdo->exec("DROP TABLE IF EXISTS `panier`");
            } catch (PDOException $e) {
                // Ignore if table doesn't exist
            }
            
            $sql = "CREATE TABLE `panier` (
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
            echo "<div class='success'>✓ Table 'panier' créée avec succès !</div>";
            
            // Step 3: Create admin user
            echo "<h2>Étape 3 : Création de l'utilisateur admin</h2>";
            
            $adminEmail = 'admin@smartstudy.com';
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$adminEmail]);
            
            if (!$stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO users (prenom, nom, email, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute(['Admin', 'SmartStudy', $adminEmail, $adminPassword, 'admin']);
                echo "<div class='success'>✓ Utilisateur admin créé</div>";
                echo "<div class='info'>📧 Email: admin@smartstudy.com<br>🔑 Mot de passe: admin123</div>";
            } else {
                echo "<div class='info'>ℹ Utilisateur admin existe déjà</div>";
            }
            
            // Step 4: Show table structures
            echo "<h2>Structure des tables créées</h2>";
            
            echo "<h3>Table 'users':</h3>";
            $result = $pdo->query("DESCRIBE users");
            echo "<table>";
            echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<h3>Table 'panier':</h3>";
            $result = $pdo->query("DESCRIBE panier");
            echo "<table>";
            echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Final success message
            echo "<div class='success' style='font-size: 1.2em; padding: 20px; margin-top: 20px;'>";
            echo "✅ <strong>Toutes les tables ont été créées avec succès !</strong><br>";
            echo "Vous pouvez maintenant vous inscrire et vous connecter.";
            echo "</div>";
            
            echo "<div style='margin-top: 30px;'>";
            echo "<a href='index.php?controller=auth&action=signup' class='btn'>📝 Aller à l'inscription</a>";
            echo "<a href='index.php?controller=auth&action=login' class='btn'>🔐 Aller à la connexion</a>";
            echo "<a href='index.php?controller=user&action=home' class='btn'>🏠 Retour à l'accueil</a>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div class='error'>";
            echo "❌ <strong>Erreur lors de la création des tables:</strong><br>";
            echo htmlspecialchars($e->getMessage()) . "<br>";
            echo "<small>Code d'erreur: " . $e->getCode() . "</small>";
            echo "</div>";
            
            echo "<div class='info' style='margin-top: 20px;'>";
            echo "<strong>Vérifications à faire:</strong><br>";
            echo "1. Vérifiez que MySQL/MariaDB est démarré dans XAMPP<br>";
            echo "2. Vérifiez que la base de données 'smartstudy' existe<br>";
            echo "3. Vérifiez les paramètres de connexion dans config/Database.php";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>

