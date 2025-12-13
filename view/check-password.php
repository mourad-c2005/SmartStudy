<?php
require_once '../config/database.php';

$email = "ayoubcherif18042005@gmail.com";

echo "<h1>Check Current Password Hash</h1>";

try {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p>Email: <strong>$email</strong></p>";
        echo "<p>Current Password Hash: </p>";
        echo "<code style='background: #f5f5f5; padding: 10px; display: block; word-break: break-all;'>" . $user['password'] . "</code>";
        echo "<p>Hash Length: " . strlen($user['password']) . " characters</p>";
    } else {
        echo "<p style='color: red;'>User not found!</p>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

echo "<hr>";
echo "<h2>Test Password Reset</h2>";
echo "<p><a href='forgot-password.php'>Go to Forgot Password</a> to test reset</p>";
?>