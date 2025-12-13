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
        font-family: 'Open Sans', sans-serif; 
        line-height: 1.6;
        color: #333;
        background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }
    
    .container {
        max-width: 600px;
        width: 90%;
        margin: 40px auto;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(76, 175, 80, 0.15);
    }
    
    .header {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: white;
        padding: 35px 30px;
        text-align: center;
    }
    
    .header-logo {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 2.2rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }
    
    .header-logo i {
        font-size: 2.5rem;
        background: rgba(255,255,255,0.15);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }
    
    .header-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 1.5rem;
        margin: 15px 0 5px 0;
        font-weight: 600;
    }
    
    .header-subtitle {
        opacity: 0.9;
        font-size: 1rem;
        margin: 0;
    }
    
    .content {
        background: white;
        padding: 40px 30px;
    }
    
    .content-text {
        color: #555;
        margin-bottom: 25px;
        font-size: 1rem;
    }
    
    .content-text strong {
        color: #2E7D32;
    }
    
    .button {
        display: inline-block;
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        color: white;
        padding: 16px 35px;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1.1rem;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 5px 20px rgba(76, 175, 80, 0.25);
        margin: 15px 0;
    }
    
    .button:hover {
        background: linear-gradient(135deg, #2E7D32, #4CAF50);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.35);
    }
    
    .button-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
        box-shadow: 0 5px 20px rgba(108, 117, 125, 0.25);
    }
    
    .button-secondary:hover {
        background: linear-gradient(135deg, #495057, #6c757d);
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.35);
    }
    
    .button-danger {
        background: linear-gradient(135deg, #F44336, #C62828);
        box-shadow: 0 5px 20px rgba(244, 67, 54, 0.25);
    }
    
    .button-danger:hover {
        background: linear-gradient(135deg, #C62828, #F44336);
        box-shadow: 0 8px 25px rgba(244, 67, 54, 0.35);
    }
    
    .button-group {
        display: flex;
        gap: 15px;
        margin: 25px 0;
        flex-wrap: wrap;
    }
    
    .token-info {
        background: #F8F9FA;
        padding: 20px;
        border: 2px solid #E8F5E9;
        border-radius: 12px;
        margin: 25px 0;
        word-break: break-all;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        color: #2E7D32;
        position: relative;
        overflow: hidden;
    }
    
    .token-info::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: #4CAF50;
    }
    
    .token-label {
        display: block;
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-box {
        background: #E3F2FD;
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
        border-left: 5px solid #35bb42ff;
        color: #0D47A1;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    
    .info-box i {
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-top: 3px;
    }
    
    .warning-box {
        background: #FFF3E0;
        border-left-color: #FF9800;
        color: #F57C00;
    }
    
    .danger-box {
        background: #FFEBEE;
        border-left-color: #F44336;
        color: #C62828;
    }
    
    .success-box {
        background: #E8F5E9;
        border-left-color: #4CAF50;
        color: #2E7D32;
    }
    
    .footer {
        text-align: center;
        margin-top: 40px;
        padding-top: 25px;
        border-top: 1px solid #eee;
        color: #95a5a6;
        font-size: 0.85rem;
        line-height: 1.6;
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
        font-size: 0.85rem;
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
        padding: 3px 8px;
        border-radius: 5px;
        font-weight: 600;
        color: #333;
        border: 1px solid #FFEB3B;
    }
    
    .expiry-time {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #FFF3E0;
        color: #F57C00;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        margin: 10px 0;
    }
    
    @media (max-width: 768px) {
        .container {
            width: 95%;
            margin: 20px auto;
        }
        
        .header {
            padding: 25px 20px;
        }
        
        .header-logo {
            font-size: 1.8rem;
            flex-direction: column;
            gap: 10px;
        }
        
        .header-logo i {
            width: 50px;
            height: 50px;
            font-size: 2rem;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .button {
            padding: 14px 25px;
            font-size: 1rem;
            width: 100%;
            margin: 10px 0;
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
        .header {
            padding: 20px 15px;
        }
        
        .header-logo {
            font-size: 1.6rem;
        }
        
        .header-title {
            font-size: 1.3rem;
        }
        
        .content {
            padding: 25px 15px;
        }
        
        .token-info {
            padding: 15px;
            font-size: 0.85rem;
        }
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