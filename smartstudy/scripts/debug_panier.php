<?php
/**
 * Script de débogage pour le panier
 */

session_start();
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Panier.php";
require_once __DIR__ . "/../models/formation.php";

echo "<h1>Débogage du Panier</h1>";
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
    
    // 1. Vérifier la session
    echo "<h2>1. Informations de session</h2>";
    if (isset($_SESSION['user_id'])) {
        echo "<p class='success'>✓ User ID en session : " . $_SESSION['user_id'] . "</p>";
        echo "<p class='info'>Nom : " . ($_SESSION['user_name'] ?? 'N/A') . "</p>";
        echo "<p class='info'>Email : " . ($_SESSION['user_email'] ?? 'N/A') . "</p>";
    } else {
        echo "<p class='error'>✗ Aucun utilisateur connecté</p>";
    }
    
    // 2. Vérifier si la table panier existe
    echo "<h2>2. Vérification de la table 'panier'</h2>";
    $tableExists = $pdo->query("SHOW TABLES LIKE 'panier'")->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p class='success'>✓ La table 'panier' existe</p>";
        
        // Afficher la structure de la table
        $result = $pdo->query("DESCRIBE panier");
        echo "<h3>Structure de la table 'panier':</h3>";
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
        
        // 3. Vérifier les données dans la table panier
        echo "<h2>3. Données dans la table 'panier'</h2>";
        if (isset($_SESSION['user_id'])) {
            $stmt = $pdo->prepare("SELECT * FROM panier WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $panierItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($panierItems) > 0) {
                echo "<p class='success'>✓ " . count($panierItems) . " formation(s) trouvée(s) dans le panier</p>";
                echo "<table>";
                echo "<tr><th>ID</th><th>User ID</th><th>Formation ID</th><th>Date</th></tr>";
                foreach ($panierItems as $item) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['user_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['formation_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($item['created_at'] ?? 'N/A') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>✗ Aucune formation dans le panier pour cet utilisateur</p>";
            }
        } else {
            echo "<p class='error'>✗ Impossible de vérifier : utilisateur non connecté</p>";
        }
        
        // 4. Tester la méthode getItems()
        echo "<h2>4. Test de la méthode Panier::getItems()</h2>";
        if (isset($_SESSION['user_id'])) {
            $items = Panier::getItems();
            
            if (count($items) > 0) {
                echo "<p class='success'>✓ " . count($items) . " formation(s) récupérée(s) par getItems()</p>";
                echo "<table>";
                echo "<tr><th>ID Formation</th><th>Titre</th><th>Prix</th><th>Catégorie</th></tr>";
                foreach ($items as $item) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($item['id_formation'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($item['titre'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($item['prix'] ?? '0') . " €</td>";
                    echo "<td>" . htmlspecialchars($item['id_categorie'] ?? 'N/A') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='error'>✗ getItems() retourne un tableau vide</p>";
                
                // Vérifier si les formations existent
                echo "<h3>Vérification des formations dans la base de données:</h3>";
                $stmt = $pdo->query("SELECT id_formation, titre, prix FROM formation LIMIT 5");
                $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (count($formations) > 0) {
                    echo "<p class='info'>Formations disponibles dans la base:</p>";
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Titre</th><th>Prix</th></tr>";
                    foreach ($formations as $f) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($f['id_formation']) . "</td>";
                        echo "<td>" . htmlspecialchars($f['titre']) . "</td>";
                        echo "<td>" . htmlspecialchars($f['prix']) . " €</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p class='error'>✗ Aucune formation dans la base de données</p>";
                }
            }
        } else {
            echo "<p class='error'>✗ Impossible de tester : utilisateur non connecté</p>";
        }
        
    } else {
        echo "<p class='error'>✗ La table 'panier' n'existe pas</p>";
        echo "<p><strong>Solution :</strong> Exécutez <a href='create_users_and_panier_tables.php'>create_users_and_panier_tables.php</a> pour créer la table.</p>";
    }
    
    // 5. Vérifier le panier en session
    echo "<h2>5. Panier en session</h2>";
    if (isset($_SESSION['panier'])) {
        echo "<p class='info'>Panier session : " . print_r($_SESSION['panier'], true) . "</p>";
    } else {
        echo "<p class='info'>Aucun panier en session</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='../index.php?controller=panier&action=show'>Aller au panier</a> | <a href='../index.php?controller=user&action=home'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
}

