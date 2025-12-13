<?php
$email = $_POST["email"]; 
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token); 
$expiry = date("Y-m-d H:i:s", time() + 60 * 30); 

require_once '../config/database.php';
require_once '../model/User.php';
require_once '../config/mailer.php'; // Adaptez le chemin

try {
    $userModel = new User($pdo);
    
    if (!$userModel->emailExists($email)) {
        header("Location: ../view/forgot-password.php?message=Si votre email existe, un lien de réinitialisation vous a été envoyé");
        exit;
    }
    
    $result = $userModel->createPasswordResetToken($email, $token_hash, $expiry);
    
    if ($result) {
        // Configuration de l'email
        $mail = getMailer();
        $mail->setFrom("noreply@smartstudy.com", "SmartStudy");
        $mail->addAddress($email);
        $mail->Subject = "Réinitialisation de votre mot de passe SmartStudy";
        
        $reset_link = "http://localhost/profile%20-%20Copy/view/reset-password.php?token=" . $token;
        
        $mail->Body = "
            <html>
            <head>
                <style>
    body {
        font-family: 'Open Sans', sans-serif;
        background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%);
        color: #333;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }
    
    .container {
        max-width: 600px;
        width: 90%;
        margin: 40px auto;
        padding: 40px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(76, 175, 80, 0.1);
        border: 1px solid rgba(76, 175, 80, 0.08);
    }
    
    .header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .logo {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 2rem;
        color: #4CAF50;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .logo i {
        font-size: 2.2rem;
    }
    
    .title {
        font-family: 'Montserrat', sans-serif;
        color: #2E7D32;
        text-align: center;
        margin: 25px 0;
        font-size: 1.8rem;
    }
    
    .content {
        line-height: 1.6;
        color: #555;
        margin-bottom: 25px;
        font-size: 1rem;
    }
    
    .button {
        display: inline-block;
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        color: white;
        padding: 14px 32px;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1rem;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        margin: 10px 5px;
    }
    
    .button:hover {
        background: linear-gradient(135deg, #2E7D32, #4CAF50);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
    }
    
    .button-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
    }
    
    .button-secondary:hover {
        background: linear-gradient(135deg, #495057, #6c757d);
    }
    
    .button-danger {
        background: linear-gradient(135deg, #F44336, #C62828);
    }
    
    .button-danger:hover {
        background: linear-gradient(135deg, #C62828, #F44336);
    }
    
    .button-small {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
    
    .button-large {
        padding: 16px 40px;
        font-size: 1.1rem;
    }
    
    .button-block {
        display: block;
        width: 100%;
        box-sizing: border-box;
    }
    
    .button-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin: 25px 0;
        flex-wrap: wrap;
    }
    
    .alert {
        padding: 18px 20px;
        border-radius: 12px;
        margin: 20px 0;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        border-left: 5px solid;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .alert-success {
        background: #E8F5E9;
        color: #2E7D32;
        border-left-color: #4CAF50;
    }
    
    .alert-warning {
        background: #FFF3E0;
        color: #F57C00;
        border-left-color: #FF9800;
    }
    
    .alert-danger {
        background: #FFEBEE;
        color: #C62828;
        border-left-color: #F44336;
    }
    
    .alert-info {
        background: #E3F2FD;
        color: #0D47A1;
        border-left-color: #2196F3;
    }
    
    .alert i {
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    
    .footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        font-size: 0.85rem;
        color: #95a5a6;
        text-align: center;
        line-height: 1.5;
    }
    
    .footer strong {
        color: #4CAF50;
    }
    
    .footer-links {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .footer-link {
        color: #666;
        text-decoration: none;
        font-size: 0.8rem;
        transition: color 0.3s;
    }
    
    .footer-link:hover {
        color: #4CAF50;
    }
    
    .divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, #ddd, transparent);
        margin: 30px 0;
    }
    
    .highlight {
        background: #FFF9C4;
        padding: 3px 6px;
        border-radius: 4px;
        font-weight: 600;
        color: #333;
    }
    
    .code-block {
        background: #F8F9FA;
        padding: 15px;
        border-radius: 10px;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        color: #495057;
        margin: 15px 0;
        border: 1px solid #DEE2E6;
        overflow-x: auto;
    }
    
    @media (max-width: 768px) {
        .container {
            padding: 25px;
            margin: 20px auto;
        }
        
        .logo {
            font-size: 1.7rem;
        }
        
        .title {
            font-size: 1.5rem;
        }
        
        .button {
            padding: 12px 24px;
            width: 100%;
            margin: 5px 0;
        }
        
        .button-group {
            flex-direction: column;
            gap: 10px;
        }
        
        .footer-links {
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }
    }
    
    @media (max-width: 480px) {
        .container {
            padding: 20px;
            margin: 15px auto;
        }
        
        .logo {
            font-size: 1.5rem;
        }
        
        .title {
            font-size: 1.3rem;
        }
        
        .content {
            font-size: 0.95rem;
        }
    }
</style>
            </head>
            <body>
                <div class='container'>
                    <h2>Réinitialisation de mot de passe</h2>
                    <p>Bonjour,</p>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe SmartStudy.</p>
                    <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                    <p>
                        <a href='$reset_link' class='button'>Réinitialiser mon mot de passe</a>
                    </p>
                    <p>Ou copiez-collez ce lien dans votre navigateur :</p>
                    <p><code>$reset_link</code></p>
                    <p><strong>Ce lien expirera dans 30 minutes.</strong></p>
                    <p>Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet email.</p>
                    <div class='footer'>
                        <p>L'équipe SmartStudy</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        // Envoi de l'email
        if ($mail->send()) {
            header("Location: ../view/forgot-password.php?message=Si votre email existe, un lien de réinitialisation vous a été envoyé");
            exit;
        } else {
            throw new Exception("Erreur lors de l'envoi de l'email: " . $mail->ErrorInfo);
        }
        
    } else {
        header("Location: ../view/forgot-password.php?error=Erreur lors de la génération du token");
        exit;
    }
   
} catch (Exception $e) {
    error_log("Erreur send-password-reset: " . $e->getMessage());
    
    // En cas d'erreur d'envoi, afficher le lien manuellement
    $reset_link = "http://localhost/profile%20-%20Copy/view/reset-password.php?token=" . $token;
    echo "
        <h3>Erreur d'envoi d'email - Lien de réinitialisation</h3>
        <p>L'email n'a pas pu être envoyé à : $email</p>
        <p>Voici votre lien de réinitialisation :</p>
        <p><a href='$reset_link' style='font-size: 16px;'>$reset_link</a></p>
        <p><a href='../view/forgot-password.php'>Retour</a></p>
    ";
    exit;
}