<?php
require_once '../config/database.php';
require_once '../model/User.php';

$token = "a42d8325419a64a9d76bf28b30dee8e0";

echo "<h1>Token Diagnosis</h1>";
echo "<p>Testing token: <strong>$token</strong></p>";

// Test 1: Direct database check
echo "<h2>Test 1: Direct Database Check</h2>";
$tokenHash = hash('sha256', $token);
$stmt = $pdo->prepare("SELECT email, reset_token_hash, reset_token_expires_at FROM users WHERE reset_token_hash = ?");
$stmt->execute([$tokenHash]);
$dbResult = $stmt->fetch(PDO::FETCH_ASSOC);

if ($dbResult) {
    echo "<p style='color: green;'>✓ Database: Token EXISTS for user: " . $dbResult['email'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ Database: Token NOT FOUND</p>";
}

// Test 2: User model check
echo "<h2>Test 2: User Model Check</h2>";
$userModel = new User($pdo);
$user = $userModel->findByResetToken($token);

if ($user) {
    echo "<p style='color: green;'>✓ User Model: User FOUND - " . $user['email'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ User Model: User NOT FOUND</p>";
}

// Test 3: Simulate reset-password.php environment
echo "<h2>Test 3: Simulating reset-password.php</h2>";
$_GET['token'] = $token;
$simulated_token = isset($_GET['token']) ? trim($_GET['token']) : '';
echo "<p>Simulated token from \$_GET: " . $simulated_token . "</p>";

if ($simulated_token === $token) {
    echo "<p style='color: green;'>✓ Token transfer: SUCCESS</p>";
    
    // Test the User model again in this simulated environment
    $user2 = $userModel->findByResetToken($simulated_token);
    if ($user2) {
        echo "<p style='color: green;'>✓ Simulated environment: User FOUND - " . $user2['email'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Simulated environment: User NOT FOUND</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Token transfer: FAILED</p>";
}

// Test 4: Check if there are multiple User classes
echo "<h2>Test 4: Class Check</h2>";
$classes = get_declared_classes();
$userClasses = array_filter($classes, function($class) {
    return strpos($class, 'User') !== false;
});

if (count($userClasses) > 1) {
    echo "<p style='color: red;'>Multiple User classes found:</p>";
    echo "<ul>";
    foreach ($userClasses as $class) {
        echo "<li>$class</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: green;'>✓ Only one User class found: " . reset($userClasses) . "</p>";
}

echo "<h2>Test Links</h2>";
echo "<p><a href='reset-password.php?token=$token'>Test reset-password.php</a></p>";
echo "<p><a href='reset-password.php'>Test without token</a></p>";
?>