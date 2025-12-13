<?php
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function getMailer() {
    $mail = new PHPMailer(true);
    
    // Configuration SMTP
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = "smtp.gmail.com"; // ou votre serveur SMTP
    $mail->Port = 587;
    $mail->Username = "votre.email@gmail.com"; // Votre email
    $mail->Password = "votre-mot-de-passe-application"; // Mot de passe d'application
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    
    return $mail;
}