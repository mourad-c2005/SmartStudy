<?php
session_start();
$pdo = require_once '../config/database.php';      // ← doit contenir $pdo = new PDO(...)
require_once '../model/Profile.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$profileModel = new Profile($pdo);

// Garantir que le profil existe (anciens utilisateurs inclus)
$profileModel->ensureExists($user_id);

// Récupérer les vraies données du profil
$profile = $profileModel->getById($user_id);
if (!$profile) die("Erreur profil");

// Mise à jour session (pour que le header affiche les bonnes infos partout)
$_SESSION['user']['nom']   = $profile['nom'];
$_SESSION['user']['email'] = $profile['email'];
$_SESSION['user']['role']  = $profile['role'];

// Traitement du formulaire
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom'           => trim($_POST['nom']),
        'email'         => trim($_POST['email']),
        'text'          => trim($_POST['text']),
        'role'          => $_POST['role'],
        'date_naissance'=> $_POST['date_naissance'],
        'etablissement' => trim($_POST['etablissement']),
        'niveau'        => trim($_POST['niveau']),
        'twitter'       => trim($_POST['twitter']),
        'linkedin'      => trim($_POST['linkedin']),
        'github'        => trim($_POST['github']),
    ];

    if ($profileModel->update($user_id, $data)) {
        $success = "Profil mis à jour avec succès !";
        $profile = $profileModel->getById($user_id); // rafraîchir
    } else {
        $error = "Erreur lors de la sauvegarde.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mon Profil | SmartStudy+</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root { --green:#4CAF50; --yellow:#FFEB3B; --light:#E8F5E8; --dark:#2e7d32; }
    body { background:var(--light); font-family:'Open Sans',sans-serif; padding-bottom:80px; }
    .top-nav { background:white; padding:1rem 5%; box-shadow:0 4px 15px rgba(76,175,80,.1); position:sticky; top:0; z-index:1000; display:flex; justify-content:space-between; align-items:center; }
    .logo { font-family:'Montserrat',sans-serif; font-weight:700; font-size:1.8rem; color:var(--green); text-decoration:none; }
    .card { max-width:900px; margin:3rem auto; border-radius:20px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,.1); }
    .profile-header { background:var(--green); color:white; padding:2rem; text-align:center; }
    .profile-photo { width:150px; height:150px; border-radius:50%; object-fit:cover; border:5px solid white; }
    .section { padding:2rem; }
    .social-links a { color:var(--green); font-size:1.5rem; margin:0 10px; }
    .user-section { display: flex; align-items: center; gap: 15px; }
    .user-info { text-align: right; }
    .profile-link { text-decoration: none; color: inherit; }
    .user-photo { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
    .logout-btn { background: var(--green); color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; }
    .logout-btn:hover { background: var(--dark); color: white; }
  </style>
</head>
<body>

<!-- Header identique à index.php -->
<nav class="top-nav">
  <a href="index.php" class="logo">SmartStudy+</a>
  <div class="user-section">
    <div class="user-info">
      <a href="profile.php" class="profile-link">
        <div class="name"><?php echo htmlspecialchars($profile['nom']); ?></div>
        <div class="role"><?php echo ucfirst($profile['role']); ?></div>
      </a>
    </div>
    <a href="profile.php">
      <img src="<?php echo $profile['img_per'] ?: 'https://ui-avatars.com/api/?name='.urlencode($profile['nom']).'&background=4CAF50&color=fff&size=45'; ?>" 
           alt="Photo" class="user-photo">
    </a>
    <a href="login.php" class="logout-btn">Déconnexion</a>
  </div>
</nav>

<div class="container">
  <div class="card">
    <div class="profile-header">
      <img src="<?php echo $profile['img_per'] ?: 'https://ui-avatars.com/api/?name='.urlencode($profile['nom']).'&background=4CAF50&color=fff&size=300'; ?>" 
           alt="Photo de profil" class="profile-photo">
      <h2><?php echo htmlspecialchars($profile['nom']); ?></h2>
      <p class="lead"><?php echo ucfirst($profile['role']); ?> • Membre depuis <?php echo date('d/m/Y', strtotime($profile['date_creation'])); ?></p>
    </div>

    <?php if ($success): ?><div class="alert alert-success m-3"><?php echo $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger m-3"><?php echo $error; ?></div><?php endif; ?>

    <div class="section">
      <h4>Informations personnelles</h4>
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Nom complet</label><input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($profile['nom']); ?>" required></div>
          <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($profile['email']); ?>" required></div>
          <div class="col-md-6"><label class="form-label">Date de naissance</label><input type="date" name="date_naissance" class="form-control" value="<?php echo $profile['date_naissance']; ?>"></div>
      
            
          </div>
          <div class="col-md-6"><label class="form-label">Établissement</label><input type="text" name="etablissement" class="form-control" value="<?php echo htmlspecialchars($profile['etablissement']??''); ?>"></div>
          <div class="col-md-6"><label class="form-label">Niveau</label><input type="text" name="niveau" class="form-control" value="<?php echo htmlspecialchars($profile['niveau']??''); ?>"></div>
          <div class="col-12"><label class="form-label">Biographie</label><textarea name="text" class="form-control" rows="4"><?php echo htmlspecialchars($profile['text']??''); ?></textarea></div>

          <div class="col-md-4"><label class="form-label">Twitter</label><input type="text" name="twitter" class="form-control" value="<?php echo htmlspecialchars($profile['twitter']??''); ?>" placeholder="@username"></div>
          <div class="col-md-4"><label class="form-label">LinkedIn</label><input type="text" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($profile['linkedin']??''); ?>"></div>
          <div class="col-md-4"><label class="form-label">GitHub</label><input type="text" name="github" class="form-control" value="<?php echo htmlspecialchars($profile['github']??''); ?>"></div>
        </div>

        <div class="text-center mt-4">
          <button type="submit" class="btn btn-success btn-lg">Enregistrer les modifications</button>
        </div>
      </form>
    </div>

    <?php if ($profile['twitter'] || $profile['linkedin'] || $profile['github']): ?>
    <div class="section text-center">
      <h4>Réseaux sociaux</h4>
      <div class="social-links">
        <?php if ($profile['twitter']): ?><a href="https://twitter.com/<?php echo ltrim($profile['twitter'],'@'); ?>" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
        <?php if ($profile['linkedin']): ?><a href="<?php echo $profile['linkedin']; ?>" target="_blank"><i class="fab fa-linkedin"></i></a><?php endif; ?>
        <?php if ($profile['github']): ?><a href="<?php echo $profile['github']; ?>" target="_blank"><i class="fab fa-github"></i></a><?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>