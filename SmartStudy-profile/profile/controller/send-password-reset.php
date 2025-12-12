<?php
session_start();

$email = $_POST["email"]; 
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token); 
$expiry = date("Y-m-d H:i:s", time() + 60 * 30); 

require_once '../config/database.php';
require_once '../model/User.php';
require_once '../config/mailer.php';

try {
    $userModel = new User($pdo);
    
    // Check if email exists
    if (!$userModel->emailExists($email)) {
        header("Location: ../view/forgot-password.php?message=Email non trouvé");
        exit;
    }
    
    // Update token in database
    $result = $userModel->createPasswordResetToken($email, $token_hash, $expiry);
    
    if ($result) {
        // Send email to the address from the form
        $email_sent = sendPasswordResetEmail($email, $token);
        
        if ($email_sent) {
            header("Location: ../view/forgot-password.php?message=Lien de réinitialisation envoyé à " . htmlspecialchars($email));
            exit;
        } else {
            // If email fails ,  to link.php
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;
            header("Location: ../view/link.php");
            exit;
        }
    } else {
        header("Location: ../view/forgot-password.php?message=Erreur lors de la création du token");
        exit;
    }
   
} catch (Exception $e) {
    error_log("Password reset error: " . $e->getMessage());
    header("Location: ../view/forgot-password.php?error=Erreur système");
    exit;
}