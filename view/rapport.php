<?php
// rapport.php - Dans le dossier view
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit();
}

// Inclure la configuration de la base de données
require_once '../config/database.php';
require_once '../model/User.php';
require_once '../model/Rapport.php'; // Nouveau modèle

// Initialisation
$userModel = new User($pdo);
$rapportModel = new Rapport($pdo); // Nouvelle instance
$user_id = $_SESSION['user']['id'];

// Récupérer les informations de l'utilisateur
$current_user = $userModel->find($user_id);

// Gestion de l'envoi du rapport
$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rapport'])) {
    $email = trim($_POST['email']);
    $titre = trim($_POST['titre']);
    $message = trim($_POST['message']);
    
    // Insérer dans la base de données avec le modèle
    $rapportData = [
        'email' => $email,
        'titre' => $titre,
        'message' => $message
    ];
    
    $rapportId = $rapportModel->create($rapportData);
    
    if ($rapportId) {
        $messageSent = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStudy+ | Envoyer un Rapport</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/rapport.css">
   
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <a href="index.php" class="logo">SmartStudy+</a>
        
        <div class="nav-menu">
            <a href="index.php">Accueil</a>
            <a href="profile.php">Profil</a>
            <a href="rapport.php" class="active">Rapports</a>
        </div>
        
        <div class="user-section">
            <?php if ($current_user && $current_user['role'] === 'admin'): ?>
                <a href="back_office/index.php" class="admin-btn">
                    <i class="fas fa-cog"></i> Admin
                </a>
            <?php endif; ?>
            
            <div class="user-info">
                <a href="profile.php" class="profile-link">
                    <div class="name"><?php echo htmlspecialchars($current_user['nom']); ?></div>
                    <div class="role"><?php echo ucfirst($current_user['role']); ?></div>
                </a>
            </div>
            
            
            <a href="login.php" class="logout-btn">Déconnexion</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="rapport-card">
            <h1>Envoyer un Rapport</h1>
            <p>Signalez un problème, faites une suggestion ou partagez une observation avec notre équipe</p>
            
            <?php if ($messageSent): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    Votre rapport a été envoyé avec succès ! Les administrateurs en seront informés.
                </div>
            <?php endif; ?>
            
            <form method="POST" action="rapport.php">
                <div class="form-group">
                    <label for="email">Votre email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($current_user['email']); ?>" 
                           required readonly>
                    <small style="color: #666; font-size: 0.9rem;">Votre email est pré-rempli depuis votre profil</small>
                </div>
                
                <div class="form-group">
                    <label for="titre">Titre du rapport</label>
                    <input type="text" id="titre" name="titre" class="form-control" 
                           placeholder="Donnez un titre clair à votre rapport (ex: Problème de connexion, Suggestion d'amélioration...)" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message détaillé</label>
                    <textarea id="message" name="message" class="form-control" 
                              placeholder="Décrivez votre problème, suggestion ou observation en détail. Soyez le plus précis possible pour nous aider à mieux comprendre et résoudre." 
                              required></textarea>
                </div>
                
                <button type="submit" name="submit_rapport" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Envoyer le rapport
                </button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>SmartStudy+ © 2025 — Développé par <strong>bluepixel</strong></p>
    </footer>
</body>
</html>