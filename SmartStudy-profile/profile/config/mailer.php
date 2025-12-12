<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendPasswordResetEmail($email, $token) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings for Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'smartstudyemail@gmail.com';
        $mail->Password = 'jcix dzct unvy iuge';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('your.email@gmail.com', 'SmartStudy'); 
        $mail->addAddress($email); 
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Réinitialisation de votre mot de passe - SmartStudy';
        
        $reset_link = "http://localhost/profile%20-%20Copy/view/reset-password.php?token=" . $token;
        
        $mail->Body = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <style>
                    body { 
                        font-family: 'Arial', sans-serif; 
                        line-height: 1.6;
                        color: #333;
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                    }
                    .header {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        padding: 30px;
                        text-align: center;
                        border-radius: 10px 10px 0 0;
                    }
                    .content {
                        background: #f9f9f9;
                        padding: 30px;
                        border-radius: 0 0 10px 10px;
                    }
                    .button {
                        display: inline-block;
                        background: #667eea;
                        color: white;
                        padding: 14px 28px;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                        margin: 20px 0;
                    }
                    .button:hover {
                        background: #5a6fd8;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 30px;
                        padding-top: 20px;
                        border-top: 1px solid #ddd;
                        color: #666;
                        font-size: 14px;
                    }
                    .token-info {
                        background: #fff;
                        padding: 15px;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        margin: 15px 0;
                        word-break: break-all;
                    }
                </style>
            </head>
            <body>
                <div class='header'>
                    <h1>SmartStudy</h1>
                    <p>Réinitialisation de mot de passe</p>
                </div>
                
                <div class='content'>
                    <h2>Bonjour,</h2>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe SmartStudy.</p>
                    
                    <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                    
                    <p style='text-align: center;'>
                        <a href='$reset_link' class='button'>Réinitialiser mon mot de passe</a>
                    </p>
                    
                    <p>Ou copiez ce lien dans votre navigateur :</p>
                    
                    <div class='token-info'>
                        <code>$reset_link</code>
                    </div>
                    
                    <p><strong>Important :</strong></p>
                    <ul>
                        <li>Ce lien expirera dans 30 minutes</li>
                        <li>Ne partagez jamais ce lien avec personne</li>
                        <li>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</li>
                    </ul>
                </div>
                
                <div class='footer'>
                    <p>© 2024 SmartStudy. Tous droits réservés.</p>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Réinitialisation de mot de passe SmartStudy\n\n" .
                        "Cliquez sur ce lien pour réinitialiser votre mot de passe :\n" .
                        "$reset_link\n\n" .
                        "Ce lien expirera dans 30 minutes.\n\n" .
                        "Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.";
        
        $mail->send();
        error_log("Password reset email sent successfully to: " . $email);
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}
