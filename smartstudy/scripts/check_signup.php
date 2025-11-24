<?php
/**
 * Check if signup system is ready
 */

require_once __DIR__ . "/../config/Database.php";

echo "<h1>Vérification du système d'inscription</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
</style>";

try {
    $pdo = Database::connect();
    
    // Check database connection
    echo "<h2>1. Connexion à la base de données</h2>";
    echo "<p class='success'>✓ Connexion réussie</p>";
    
    // Check if users table exists
    echo "<h2>2. Vérification de la table 'users'</h2>";
    $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p class='success'>✓ La table 'users' existe</p>";
        
        // Show table structure
        $result = $pdo->query("DESCRIBE users");
        echo "<h3>Structure de la table 'users':</h3>";
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
        
        // Count users
        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        echo "<p class='info'>Nombre d'utilisateurs : $count</p>";
        
    } else {
        echo "<p class='error'>✗ La table 'users' n'existe pas</p>";
        echo "<p><strong>Solution :</strong> Exécutez <a href='create_users_and_panier_tables.php'>create_users_and_panier_tables.php</a> pour créer la table.</p>";
    }
    
    // Check if panier table exists
    echo "<h2>3. Vérification de la table 'panier'</h2>";
    $panierExists = $pdo->query("SHOW TABLES LIKE 'panier'")->rowCount() > 0;
    
    if ($panierExists) {
        echo "<p class='success'>✓ La table 'panier' existe</p>";
    } else {
        echo "<p class='error'>✗ La table 'panier' n'existe pas</p>";
        echo "<p><strong>Solution :</strong> Exécutez <a href='create_users_and_panier_tables.php'>create_users_and_panier_tables.php</a> pour créer la table.</p>";
    }
    
    // Check signup files
    echo "<h2>4. Vérification des fichiers</h2>";
    $files = [
        '../views/auth/signup.php' => 'Page d\'inscription',
        '../signup_handler.php' => 'Gestionnaire d\'inscription',
        '../config/config.php' => 'Configuration',
        '../config/Database.php' => 'Classe Database'
    ];
    
    foreach ($files as $file => $description) {
        if (file_exists($file)) {
            echo "<p class='success'>✓ $description ($file) existe</p>";
        } else {
            echo "<p class='error'>✗ $description ($file) manquant</p>";
        }
    }
    
    echo "<hr>";
    if ($tableExists && $panierExists) {
        echo "<p class='success'><strong>✓ Le système d'inscription est prêt !</strong></p>";
        echo "<p><a href='../index.php?controller=auth&action=signup'>Aller à la page d'inscription</a></p>";
    } else {
        echo "<p class='error'><strong>✗ Le système d'inscription n'est pas prêt</strong></p>";
        echo "<p><a href='create_users_and_panier_tables.php'>Créer les tables nécessaires</a></p>";
    }
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
}

