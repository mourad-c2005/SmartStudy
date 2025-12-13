<?php
require_once '../config/database.php';
require_once '../model/User.php';

$userModel = new User($pdo);

// Get token from URL or use the one from your example
$token = $_GET['token'] ?? 'a42d8325419a64a9d76bf28b30dee8e0';

echo "<h1>Token Debug Page</h1>";
echo "<p>Testing token: <code>$token</code></p>";

// Test the token step by step
echo "<h2>Step 1: Token Analysis</h2>";
$tokenHash = hash('sha256', $token);
echo "Raw Token: " . $token . "<br>";
echo "Token Length: " . strlen($token) . "<br>";
echo "Hashed Token: " . $tokenHash . "<br>";
echo "Current Time: " . date('Y-m-d H:i:s') . "<br>";

echo "<h2>Step 2: Database Check</h2>";

// Check if this specific token exists
try {
    $stmt = $pdo->prepare("SELECT email, reset_token_hash, reset_token_expires_at FROM users WHERE reset_token_hash = ?");
    $stmt->execute([$tokenHash]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<p style='color: green;'>✓ Token FOUND in database!</p>";
        echo "Email: " . $result['email'] . "<br>";
        echo "Expires: " . $result['reset_token_expires_at'] . "<br>";
        
        $isValid = strtotime($result['reset_token_expires_at']) > time();
        echo "Valid: " . ($isValid ? 'YES' : 'NO - EXPIRED') . "<br>";
        
        if (!$isValid) {
            echo "<p style='color: orange;'>Token has expired!</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Token NOT found in database</p>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<h2>Step 3: Check All Tokens in Database</h2>";
try {
    $stmt = $pdo->query("SELECT email, reset_token_hash, reset_token_expires_at FROM users WHERE reset_token_hash IS NOT NULL");
    $allTokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($allTokens) > 0) {
        echo "<p>Found " . count($allTokens) . " token(s) in database:</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Email</th><th>Token Hash (first 20 chars)</th><th>Expires</th><th>Status</th></tr>";
        foreach ($allTokens as $tokenData) {
            $isValid = strtotime($tokenData['reset_token_expires_at']) > time();
            $status = $isValid ? 'Valid' : 'Expired';
            $color = $isValid ? 'green' : 'red';
            echo "<tr>";
            echo "<td>" . $tokenData['email'] . "</td>";
            echo "<td>" . substr($tokenData['reset_token_hash'], 0, 20) . "...</td>";
            echo "<td>" . $tokenData['reset_token_expires_at'] . "</td>";
            echo "<td style='color: $color;'>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No tokens found in database at all.</p>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<h2>Step 4: Test User Model Methods</h2>";

// Test findByResetToken
$user = $userModel->findByResetToken($token);
if ($user) {
    echo "<p style='color: green;'>✓ findByResetToken() SUCCESS - User: " . $user['email'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ findByResetToken() FAILED - No user found</p>";
}

echo "<h2>Step 5: Test Reset Link</h2>";
$reset_link = "http://localhost/profile%20-%20Copy/view/reset-password.php?token=" . $token;
echo "<p><a href='$reset_link' target='_blank'>Test Reset Password Link</a></p>";
echo "<p>Full URL: <code>$reset_link</code></p>";

echo "<h2>Step 6: Manual SQL Check</h2>";
echo "<p>Run this SQL in phpMyAdmin to verify:</p>";
echo "<code>SELECT email, reset_token_hash, reset_token_expires_at FROM users WHERE reset_token_hash = '$tokenHash';</code>";

?>