<?php
/**
 * Script de test pour vérifier l'ajout et la récupération du panier
 */

session_start();
require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/Panier.php";
require_once __DIR__ . "/../models/formation.php";

echo "<h1>Test du Panier</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    .test-section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
</style>";

try {
    $pdo = Database::connect();
    
    // 1. Vérifier la session
    echo "<div class='test-section'>";
    echo "<h2>1. Vérification de la session</h2>";
    if (isset($_SESSION['user_id'])) {
        echo "<p class='success'>✓ User ID: " . $_SESSION['user_id'] . "</p>";
        echo "<p class='info'>Nom: " . ($_SESSION['user_name'] ?? 'N/A') . "</p>";
        echo "<p class='info'>Email: " . ($_SESSION['user_email'] ?? 'N/A') . "</p>";
        $userId = $_SESSION['user_id'];
    } else {
        echo "<p class='error'>✗ Aucun utilisateur connecté</p>";
        echo "<p><a href='../index.php?controller=auth&action=login'>Se connecter</a></p>";
        exit;
    }
    echo "</div>";
    
    // 2. Vérifier la table panier
    echo "<div class='test-section'>";
    echo "<h2>2. Vérification de la table 'panier'</h2>";
    $tableExists = $pdo->query("SHOW TABLES LIKE 'panier'")->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p class='success'>✓ La table 'panier' existe</p>";
        
        // Afficher la structure
        $result = $pdo->query("DESCRIBE panier");
        echo "<h3>Structure:</h3>";
        echo "<table>";
        echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ La table 'panier' n'existe pas</p>";
        echo "<p><a href='create_users_and_panier_tables.php'>Créer la table</a></p>";
        exit;
    }
    echo "</div>";
    
    // 3. Vérifier les formations disponibles
    echo "<div class='test-section'>";
    echo "<h2>3. Formations disponibles</h2>";
    $formations = Formation::getAll();
    if (count($formations) > 0) {
        echo "<p class='success'>✓ " . count($formations) . " formation(s) trouvée(s)</p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Titre</th><th>Prix</th><th>Action</th></tr>";
        foreach ($formations as $f) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($f['id_formation']) . "</td>";
            echo "<td>" . htmlspecialchars($f['titre']) . "</td>";
            echo "<td>" . htmlspecialchars($f['prix']) . " €</td>";
            echo "<td><a href='?test_add=" . $f['id_formation'] . "'>Ajouter au panier</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ Aucune formation trouvée</p>";
    }
    echo "</div>";
    
    // 4. Test d'ajout si demandé
    if (isset($_GET['test_add'])) {
        $formationId = intval($_GET['test_add']);
        echo "<div class='test-section'>";
        echo "<h2>4. Test d'ajout au panier</h2>";
        echo "<p class='info'>Tentative d'ajout de la formation ID: $formationId</p>";
        
        // Vérifier si la formation existe
        $formation = Formation::getById($formationId);
        if ($formation) {
            echo "<p class='success'>✓ Formation trouvée: " . htmlspecialchars($formation['titre']) . "</p>";
        } else {
            echo "<p class='error'>✗ Formation non trouvée avec ID: $formationId</p>";
        }
        
        // Vérifier si déjà dans le panier
        $stmt = $pdo->prepare("SELECT id FROM panier WHERE user_id = ? AND formation_id = ?");
        $stmt->execute([$userId, $formationId]);
        $existing = $stmt->fetch();
        if ($existing) {
            echo "<p class='error'>✗ Cette formation est déjà dans le panier</p>";
        } else {
            echo "<p class='info'>La formation n'est pas encore dans le panier, on peut l'ajouter</p>";
        }
        
        // Tenter l'ajout
        $result = Panier::addFormation($formationId);
        if ($result) {
            echo "<p class='success'>✓ Méthode addFormation() a retourné TRUE</p>";
            
            // Vérifier dans la base de données
            $stmt = $pdo->prepare("SELECT * FROM panier WHERE user_id = ? AND formation_id = ?");
            $stmt->execute([$userId, $formationId]);
            $added = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($added) {
                echo "<p class='success'>✓ Formation confirmée dans la base de données !</p>";
                echo "<p class='info'>ID panier: " . $added['id'] . "</p>";
            } else {
                echo "<p class='error'>✗ La formation n'a PAS été ajoutée à la base de données malgré le retour TRUE</p>";
            }
        } else {
            echo "<p class='error'>✗ Méthode addFormation() a retourné FALSE</p>";
        }
        
        echo "<p><a href='test_panier.php'>Recharger la page</a></p>";
        echo "</div>";
    }
    
    // 5. Vérifier le contenu du panier
    echo "<div class='test-section'>";
    echo "<h2>5. Contenu du panier</h2>";
    
    // Vérifier directement dans la base
    $stmt = $pdo->prepare("SELECT * FROM panier WHERE user_id = ?");
    $stmt->execute([$userId]);
    $panierItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p class='info'>Items dans la table panier: " . count($panierItems) . "</p>";
    if (count($panierItems) > 0) {
        echo "<table>";
        echo "<tr><th>ID Panier</th><th>User ID</th><th>Formation ID</th></tr>";
        foreach ($panierItems as $item) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['id']) . "</td>";
            echo "<td>" . htmlspecialchars($item['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($item['formation_id']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Utiliser la méthode getItems()
    $items = Panier::getItems();
    echo "<p class='info'>Items récupérés par getItems(): " . count($items) . "</p>";
    
    if (count($items) > 0) {
        echo "<table>";
        echo "<tr><th>ID Formation</th><th>Titre</th><th>Prix</th></tr>";
        foreach ($items as $item) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['id_formation'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($item['titre'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($item['prix'] ?? '0') . " €</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ Aucun item récupéré par getItems()</p>";
        
        // Debug: vérifier chaque formation_id
        if (count($panierItems) > 0) {
            echo "<h3>Debug - Vérification des formations:</h3>";
            foreach ($panierItems as $panierItem) {
                $formationId = $panierItem['formation_id'];
                $formation = Formation::getById($formationId);
                if ($formation) {
                    echo "<p class='success'>✓ Formation ID $formationId trouvée: " . htmlspecialchars($formation['titre']) . "</p>";
                } else {
                    echo "<p class='error'>✗ Formation ID $formationId NON trouvée dans la table formation</p>";
                }
            }
        }
    }
    echo "</div>";
    
    echo "<hr>";
    echo "<p><a href='../index.php?controller=panier&action=show'>Aller au panier</a> | <a href='../index.php?controller=user&action=home'>Retour à l'accueil</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 class='error'>Erreur:</h2>";
    echo "<p class='error'>" . htmlspecialchars($e->getMessage()) . "</p>";
}

