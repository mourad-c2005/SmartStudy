<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - SmartStudy</title>
   
    <link rel="stylesheet" type="text/css" href="css/forget-password.css">
</head>
<body>
    <nav class="top-nav">
        <a href="index.php" class="logo">SmartStudy</a>
    </nav>

    <div class="main">
        <div class="card">
            <h2>Mot de passe oublié</h2>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="message success">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
                <div class="auto-redirect">
                    <p>Redirection automatique vers la page de connexion dans <span class="countdown" id="countdown">5</span> secondes...</p>
                    <p><a href="login.php" class="back-link">Cliquez ici si la redirection ne fonctionne pas</a></p>
                </div>
                
                <script>
                    // Countdown and redirect
                    let seconds = 5;
                    const countdownElement = document.getElementById('countdown');
                    const countdownInterval = setInterval(function() {
                        seconds--;
                        countdownElement.textContent = seconds;
                        
                        if (seconds <= 0) {
                            clearInterval(countdownInterval);
                            window.location.href = 'login.php';
                        }
                    }, 1000);
                </script>
                
            <?php elseif (isset($_GET['error'])): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
                <div class="text-center">
                    <a href="login.php" class="back-link">← Retour à la connexion</a>
                </div>
            <?php else: ?>
            
            <form action="../controller/send-password-reset.php" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email :</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="Entrez votre adresse email">
                </div>
                
                <button type="submit" class="btn btn-primary">Envoyer le lien de réinitialisation</button>
            </form>
            
            <div class="text-center">
                <a href="login.php" class="back-link">← Retour à la connexion</a>
            </div>
            
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 <strong>SmartStudy</strong>. Tous droits réservés.</p>
    </footer>
</body>
</html>