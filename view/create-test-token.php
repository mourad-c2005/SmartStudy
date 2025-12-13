<?php
require_once '../config/database.php';
require_once '../model/User.php';

$test_email = "ayoubcherif18042005@gmail.com";
$userModel = new User($pdo);

echo "<h1>Create New Test Token</h1>";

// Check if user exists
if (!$userModel->emailExists($test_email)) {
    echo "<p style='color: red;'>User $test_email does not exist!</p>";
    exit;
}

// Generate new token
$new_token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $new_token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes

echo "<p>Email: $test_email</p>";
echo "<p>New Token: <strong>$new_token</strong></p>";
echo "<p>Token Hash: $token_hash</p>";
echo "<p>Expires: $expiry</p>";

// Save token
$result = $userModel->createPasswordResetToken($test_email, $token_hash, $expiry);

if ($result) {
    echo "<p style='color: green;'>✓ Token saved successfully!</p>";
    
    // Verify
    $stmt = $pdo->prepare("SELECT reset_token_hash FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    $saved = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($saved && $saved['reset_token_hash'] === $token_hash) {
        echo "<p style='color: green;'>✓ Token verified in database!</p>";
        
        // Create test links
        $reset_link = "reset-password.php?token=$new_token";
        $minimal_link = "reset-password-minimal.php?token=$new_token";
        
        echo "<h3>Test Links:</h3>";
        echo "<ul>";
        echo "<li><a href='$reset_link' target='_blank'>Test reset-password.php</a></li>";
        echo "<li><a href='$minimal_link' target='_blank'>Test minimal version</a></li>";
        echo "</ul>";
        
        echo "<h3>Full URL:</h3>";
        echo "<code>http://localhost/profile%20-%20Copy/view/reset-password.php?token=$new_token</code>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to save token!</p>";
}
?>