<?php
/**
 * Database Setup Script
 * Run this file once to create the required database tables
 * Access via: http://localhost/SmartStudy/smartstudy/scripts/setup_database.php
 */

require_once __DIR__ . "/../config/Database.php";

try {
    $pdo = Database::connect();
    
    // Create sections table (plural as per your database)
    $sql = "CREATE TABLE IF NOT EXISTS `sections` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nom` VARCHAR(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    
    echo "<h1>Database Setup Successful!</h1>";
    echo "<p>The 'sections' table has been created successfully.</p>";
    echo "<p><a href='index.php?controller=user&action=home'>Go to Homepage</a></p>";
    
} catch (PDOException $e) {
    echo "<h1>Database Setup Error</h1>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

