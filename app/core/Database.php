<?php
// app/core/Database.php - CORRECTED VERSION

// First, make sure config is loaded - NO ECHO STATEMENTS HERE!
// Only require/include statements and maybe define() at the top level

$configLoaded = false;

// Try to find and load config.php
$possiblePaths = [
    __DIR__ . '/../../config/config.php',  // from app/core/
    __DIR__ . '/../config/config.php',     // from app/
    dirname(__DIR__, 2) . '/config/config.php', // from project root
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $configLoaded = true;
        break;
    }
}

if (!$configLoaded) {
    // Use error_log for debugging, not echo
    error_log("Config file not found. Checked paths: " . implode(', ', $possiblePaths));
    // Don't die() here - let the class constructor handle it
}

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Debug: Check if constants are defined
        if (!defined('DB_HOST') || !defined('DB_NAME')) {
            throw new Exception("Database configuration not loaded. Check config.php");
        }
        
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (PDOException $e) {
            // Log error properly
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Could not connect to database. Please check configuration.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
    
    // Optional: Test connection method
    public static function testConnection() {
        try {
            $db = self::getInstance();
            $stmt = $db->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}