<?php
require_once '../config/database.php';

try {
    echo "<h2>Database Structure Check</h2>";
    
    // Check users table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Users Table Columns:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "<td>{$column['Extra']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if reset token columns exist
    $hasResetColumns = false;
    foreach ($columns as $column) {
        if (in_array($column['Field'], ['reset_token_hash', 'reset_token_expires_at'])) {
            $hasResetColumns = true;
        }
    }
    
    if (!$hasResetColumns) {
        echo "<h3 style='color: red;'>MISSING COLUMNS! Run this SQL:</h3>";
        echo "<code>ALTER TABLE users ADD reset_token_hash VARCHAR(64) NULL, ADD reset_token_expires_at DATETIME NULL;</code>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>