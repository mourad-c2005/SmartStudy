<?php
// C:\xampp\htdocs\profile - Copy\lib\mailer.php

// Chemin vers l'autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendEmail($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);
    
    try {
        // Configuration SMTP pour Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'smartstudyemail@gmail.com'; // Votre email Gmail
        $mail->Password = 'jcix dzct unvy iuge'; // Votre mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Options de débogage (désactiver en production)
        $mail->SMTPDebug = 0; // 0 = off, 2 = verbose
        
        // Expéditeur et destinataire
        $mail->setFrom('smartstudyemail@gmail.com', 'SmartStudy+');
        $mail->addAddress($to);
        
        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        if ($altBody) {
            $mail->AltBody = $altBody;
        }
        
        $mail->send();
        error_log("Email sent successfully to: " . $to);
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        // Pour le débogage, vous pouvez aussi afficher l'erreur
        // echo "Mailer Error: " . $mail->ErrorInfo;
        return false;
    }
}
?>