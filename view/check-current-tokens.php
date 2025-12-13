<?php
require_once '../config/database.php';

echo "<h1>Current Tokens in Database</h1>";

try {
    $stmt = $pdo->query("
        SELECT email, reset_token_hash, reset_token_expires_at 
        FROM users 
        WHERE reset_token_hash IS NOT NULL
        ORDER BY reset_token_expires_at DESC
    ");
    $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($tokens) > 0) {
        echo "<p>Found " . count($tokens) . " token(s):</p>";
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>Email</th><th>Token Hash</th><th>Expires</th><th>Status</th></tr>";
        
        foreach ($tokens as $token) {
            $isValid = strtotime($token['reset_token_expires_at']) > time();
            $status = $isValid ? 'Valid' : 'Expired';
            $color = $isValid ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>" . $token['email'] . "</td>";
            echo "<td><code>" . substr($token['reset_token_hash'], 0, 20) . "...</code></td>";
            echo "<td>" . $token['reset_token_expires_at'] . "</td>";
            echo "<td style='color: $color;'><strong>$status</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No tokens found in database!</p>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<hr>";
echo "<h2>Quick Token Creation Test</h2>";
echo "<p><a href='create-test-token.php'>Create a new test token</a></p>";
?>