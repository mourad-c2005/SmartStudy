<?php
// config/database.php - Version PDO obligatoire
$host = 'localhost';
$dbname = 'smartstudy';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Retourner la connexion PDO (important !)
    return $pdo;
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>