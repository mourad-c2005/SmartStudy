<?php
session_start();
if (!isset($_SESSION['reset_token'])) {
    header("Location: forgot-password.php");
    exit;
}

$token = $_SESSION['reset_token'];
$email = $_SESSION['reset_email'];
$reset_link = "http://localhost/profile%20-%20Copy/view/reset-password.php?token=" . $token;

// Check if token still exists in database
require_once '../config/database.php';
require_once '../model/User.php';

$userModel = new User($pdo);
$user = $userModel->findByResetToken($token);
$token_status = $user ? 'valid' : 'invalid_or_expired';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lien de réinitialisation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .message { padding: 15px; border-radius: 4px; margin: 20px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .reset-link { background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 4px; margin: 20px 0; word-break: break-all; }
        .button { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px 0; }
        .button:hover { background: #0056b3; }
        .button:disabled { background: #6c757d; cursor: not-allowed; }
    </style>
</head>
<body>
    <h2>Lien de réinitialisation</h2>
    
    <?php if ($token_status === 'valid'): ?>
        <div class="message success">
            <p><strong>✓ Lien valide !</strong></p>
        </div>
    <?php else: ?>
        <div class="message error">
            <p><strong>✗ Lien expiré ou invalide !</strong></p>
            <p>Ce lien a expiré ou a déjà été utilisé.</p>
        </div>
    <?php endif; ?>
    
    <p>Destinataire : <strong><?php echo htmlspecialchars($email); ?></strong></p>
    <p>Token : <code><?php echo htmlspecialchars($token); ?></code></p>
    <p>Statut : <strong><?php echo $token_status === 'valid' ? 'Valide' : 'Expiré/Invalide'; ?></strong></p>
    
    <div class="reset-link">
        <p><strong>Lien de réinitialisation :</strong></p>
        <code id="reset-link"><?php echo htmlspecialchars($reset_link); ?></code>
        <button onclick="copyToClipboard()" style="margin-top: 10px;">Copier le lien</button>
    </div>
    
    <?php if ($token_status === 'valid'): ?>
        <p>Cliquez sur le bouton ci-dessous pour réinitialiser le mot de passe :</p>
        <a href="<?php echo htmlspecialchars($reset_link); ?>" class="button">Réinitialiser le mot de passe</a>
    <?php else: ?>
        <p>Ce lien n'est plus valide. Veuillez en demander un nouveau.</p>
        <a href="forgot-password.php" class="button">Demander un nouveau lien</a>
    <?php endif; ?>
    
    <p style="margin-top: 20px;"><a href="forgot-password.php">Retour</a></p>
    
    <script>
    function copyToClipboard() {
        const linkText = document.getElementById('reset-link').textContent;
        navigator.clipboard.writeText(linkText).then(function() {
            alert('Lien copié dans le presse-papier !');
        });
    }
    </script>
</body>
</html>