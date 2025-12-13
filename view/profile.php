<?php
session_start();
$pdo = require_once '../config/database.php';
require_once '../model/Profile.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$profileModel = new Profile($pdo);

$profileModel->ensureExists($user_id);
$profile = $profileModel->getById($user_id);
if (!$profile) die("Erreur profil");

// Mise à jour session
$_SESSION['user']['nom']   = $profile['nom'];
$_SESSION['user']['email'] = $profile['email'];

// Chemin de base pour les URLs
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$base_url = rtrim($base_url, '/\\') . '/';

// CORRECTION: Gérer les dates
$date_naissance = $profile['date_naissance'] ?? '';
if ($date_naissance === '0000-00-00' || empty($date_naissance)) {
    $date_naissance = '';
}

// CORRECTION: Gérer la date de création
$date_creation = $profile['user_date_creation'] ?? $profile['date_creation'] ?? $profile['profile_date_creation'] ?? date('Y-m-d');

// Traitement du formulaire
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CORRECTION: Toujours inclure img_per
    $data = [
        'nom'           => trim($_POST['nom']),
        'email'         => trim($_POST['email']),
        'text'          => trim($_POST['text']),
        'date_naissance'=> !empty(trim($_POST['date_naissance'])) ? trim($_POST['date_naissance']) : null,
        'etablissement' => trim($_POST['etablissement']),
        'niveau'        => trim($_POST['niveau']),
        'twitter'       => trim($_POST['twitter']),
        'linkedin'      => trim($_POST['linkedin']),
        'github'        => trim($_POST['github']),
        'img_per'       => $profile['img_per'] ?? null, // IMPORTANT
    ];
    
    // Gestion de l'upload de photo
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/pic/';
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $file = $_FILES['profile_photo'];
        $fileName = 'profile_' . $user_id . '_' . time();
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            if ($file['size'] <= 5 * 1024 * 1024) {
                // Vérifier le type MIME
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (in_array($mime, $allowedMimes)) {
                    $finalFileName = $fileName . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $finalFileName;
                    
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        // Supprimer l'ancienne photo si elle existe et est locale
                        if (!empty($profile['img_per']) && strpos($profile['img_per'], 'http') === false) {
                            $oldPhotoPath = realpath(__DIR__ . '/' . $profile['img_per']);
                            if ($oldPhotoPath && file_exists($oldPhotoPath) && strpos($oldPhotoPath, 'pic') !== false) {
                                @unlink($oldPhotoPath);
                            }
                        }
                        
                        $data['img_per'] = 'pic/' . $finalFileName;
                        
                    } else {
                        $error = "Erreur lors de l'upload de la photo.";
                    }
                } else {
                    $error = "Type de fichier non autorisé.";
                }
            } else {
                $fileSizeMB = round($file['size'] / (1024 * 1024), 2);
                $error = "La photo est trop volumineuse ($fileSizeMB MB). Taille maximale : 5MB.";
            }
        } else {
            $error = "Extension de fichier non autorisée. Utilisez JPG, PNG, GIF ou WebP.";
        }
    }
    
    // Gestion de la suppression de photo
    if (isset($_POST['remove_photo']) && $_POST['remove_photo'] == '1') {
        // Supprimer l'ancienne photo si elle existe et est locale
        if (!empty($profile['img_per']) && strpos($profile['img_per'], 'http') === false) {
            $oldPhotoPath = realpath(__DIR__ . '/' . $profile['img_per']);
            if ($oldPhotoPath && file_exists($oldPhotoPath)) {
                @unlink($oldPhotoPath);
            }
        }
        $data['img_per'] = null;
    }
    
    // Mettre à jour le profil seulement si aucune erreur d'upload
    if (empty($error)) {
        if ($profileModel->update($user_id, $data)) {
            $success = "Profil mis à jour avec succès !";
            $profile = $profileModel->getById($user_id); // rafraîchir
            
            // Rafraîchir aussi la session
            $_SESSION['user']['img_per'] = $profile['img_per'] ?? null;
            
            // Rafraîchir la date de création
            $date_creation = $profile['user_date_creation'] ?? $profile['date_creation'] ?? $profile['profile_date_creation'] ?? date('Y-m-d');
        } else {
            $error = "Erreur lors de la sauvegarde.";
        }
    }
}

// Fonction pour obtenir l'URL correcte de l'image
function getProfileImageUrl($imgPath, $base_url) {
    if (empty($imgPath)) {
        return null;
    }
    
    if (strpos($imgPath, 'http') === 0) {
        return $imgPath;
    }
    
    // CORRECTION: Vérifier si le fichier existe
    $physicalPath = __DIR__ . '/' . $imgPath;
    if (!file_exists($physicalPath)) {
        return null;
    }
    
    if (strpos($imgPath, 'pic/') === 0) {
        return $base_url . $imgPath;
    }
    
    return $imgPath;
}

$profileImageUrl = getProfileImageUrl($profile['img_per'] ?? '', $base_url);
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
  <link rel="stylesheet" type="text/css" href="css/profile.css">
  <style>
    .profile-photo-container {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
    }
    
    .profile-photo, .user-photo {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        background-color: #f0f0f0;
    }
    
    .user-section .user-photo {
        width: 45px;
        height: 45px;
        border: 3px solid #fff;
    }
    
    .photo-upload-label {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #4CAF50;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .photo-upload-label:hover {
        background: #45a049;
        transform: scale(1.1);
    }
    
    .photo-preview {
        display: none;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .remove-photo-btn {
        background: #f44336;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        margin-top: 15px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .remove-photo-btn:hover {
        background: #d32f2f;
        transform: translateY(-2px);
    }
    
    .avatar-default {
        font-size: 60px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4CAF50, #45a049);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        border: 5px solid #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        font-weight: bold;
    }
    
    .avatar-default.small {
        font-size: 18px;
        width: 45px;
        height: 45px;
        border: 3px solid #fff;
    }
  </style>
</head>
<body>

<!-- Header -->
<nav class="top-nav">
  <a href="index.php" class="logo">SmartStudy+</a>
  <div class="user-section">
    <div class="user-info">
      <a href="profile.php" class="profile-link">
        <div class="name"><?php echo htmlspecialchars($profile['nom']); ?></div>
      </a>
    </div>
    <a href="profile.php">
      <?php if (!empty($profileImageUrl)): ?>
        <img src="<?php echo htmlspecialchars($profileImageUrl); ?>" 
             alt="Photo" 
             class="user-photo"
             onerror="this.onerror=null; showAvatarFallback(this);">
      <?php else: ?>
        <div class="avatar-default small" id="header-avatar">
          <?php echo strtoupper(substr($profile['nom'], 0, 1)); ?>
        </div>
      <?php endif; ?>
    </a>
  </div>
</nav>

<div class="container">
  <div class="card">
    <div class="profile-header text-center">
      <div class="profile-photo-container">
        <?php if (!empty($profileImageUrl)): ?>
          <img src="<?php echo htmlspecialchars($profileImageUrl); ?>" 
               alt="Photo de profil" 
               class="profile-photo"
               id="current-photo"
               onerror="this.onerror=null; showMainAvatarFallback();">
        <?php else: ?>
          <div class="avatar-default" id="avatar-default">
            <?php echo strtoupper(substr($profile['nom'], 0, 1)); ?>
          </div>
        <?php endif; ?>
        
        <img src="" alt="Aperçu" class="photo-preview" id="photo-preview">
        
        <label for="profile_photo" class="photo-upload-label" title="Changer la photo">
          <i class="fas fa-camera"></i>
        </label>
      </div>
      
      <h2 class="mt-3"><?php echo htmlspecialchars($profile['nom']); ?></h2>
      <!-- CORRECTION LIGNE 318: Utiliser $date_creation au lieu de $profile['date_creation'] -->
      <p class="lead text-muted">Membre depuis <?php echo date('d/m/Y', strtotime($date_creation)); ?></p>
      
      <?php if (!empty($profileImageUrl)): ?>
        <button type="button" class="remove-photo-btn" id="remove-photo-btn">
          <i class="fas fa-trash"></i> Supprimer la photo
        </button>
      <?php endif; ?>
      
       
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success m-3">
        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
      </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
      <div class="alert alert-danger m-3">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
      </div>
    <?php endif; ?>

    <div class="section">
      <h4><i class="fas fa-user-circle me-2"></i>Informations personnelles</h4>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="remove_photo" id="remove-photo-input" value="0">
        
        <div class="mb-4 p-3 bg-light rounded">
          <label class="form-label fw-bold">
            <i class="fas fa-image me-2"></i>Photo de profil
          </label>
          <input type="file" 
                 name="profile_photo" 
                 id="profile_photo" 
                 class="form-control mt-2"
                 accept="image/jpeg,image/png,image/gif,image/webp">
          <div class="form-text mt-2">
            <i class="fas fa-info-circle me-1"></i>
            Taille maximale : 5MB. Formats acceptés : JPG, PNG, GIF, WebP.
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">
              <i class="fas fa-user me-2"></i>Nom complet
            </label>
            <input type="text" name="nom" class="form-control" value="<?php echo htmlspecialchars($profile['nom']); ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">
              <i class="fas fa-envelope me-2"></i>Email
            </label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($profile['email']); ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">
              <i class="fas fa-birthday-cake me-2"></i>Date de naissance
            </label>
            <input type="date" 
                   name="date_naissance" 
                   class="form-control" 
                   value="<?php echo htmlspecialchars($date_naissance); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">
              <i class="fas fa-school me-2"></i>Établissement
            </label>
            <input type="text" name="etablissement" class="form-control" value="<?php echo htmlspecialchars($profile['etablissement'] ?? ''); ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">
              <i class="fas fa-graduation-cap me-2"></i>Niveau
            </label>
            <input type="text" name="niveau" class="form-control" value="<?php echo htmlspecialchars($profile['niveau'] ?? ''); ?>">
          </div>
          <div class="col-12">
            <label class="form-label fw-bold">
              <i class="fas fa-edit me-2"></i>Biographie
            </label>
            <textarea name="text" class="form-control" rows="4"><?php echo htmlspecialchars($profile['text'] ?? ''); ?></textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-bold">
              <i class="fab fa-twitter me-2"></i>Twitter
            </label>
            <input type="text" name="twitter" class="form-control" value="<?php echo htmlspecialchars($profile['twitter'] ?? ''); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">
              <i class="fab fa-linkedin me-2"></i>LinkedIn
            </label>
            <input type="text" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($profile['linkedin'] ?? ''); ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold">
              <i class="fab fa-github me-2"></i>GitHub
            </label>
            <input type="text" name="github" class="form-control" value="<?php echo htmlspecialchars($profile['github'] ?? ''); ?>">
          </div>
        </div>

        <div class="text-center mt-5">
          <button type="submit" class="btn btn-success btn-lg px-5 py-3">
            <i class="fas fa-save me-2"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function showAvatarFallback(imgElement) {
    const parent = imgElement.parentElement;
    const avatar = document.createElement('div');
    avatar.className = 'avatar-default small';
    avatar.textContent = '<?php echo strtoupper(substr($profile["nom"], 0, 1)); ?>';
    parent.appendChild(avatar);
    imgElement.style.display = 'none';
  }
  
  function showMainAvatarFallback() {
    const currentPhoto = document.getElementById('current-photo');
    if (currentPhoto) {
      currentPhoto.style.display = 'none';
    }
    
    let avatar = document.getElementById('avatar-default');
    if (!avatar) {
      const container = document.querySelector('.profile-photo-container');
      avatar = document.createElement('div');
      avatar.className = 'avatar-default';
      avatar.id = 'avatar-default';
      avatar.textContent = '<?php echo strtoupper(substr($profile["nom"], 0, 1)); ?>';
      container.insertBefore(avatar, container.firstChild);
    } else {
      avatar.style.display = 'flex';
    }
  }
  
  document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const maxSize = 5 * 1024 * 1024;
      if (file.size > maxSize) {
        alert('La photo est trop volumineuse (max 5MB).');
        this.value = '';
        return;
      }
      
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('photo-preview');
        preview.src = e.target.result;
        preview.style.display = 'block';
        
        const currentPhoto = document.getElementById('current-photo');
        const avatarDefault = document.getElementById('avatar-default');
        
        if (currentPhoto) currentPhoto.style.display = 'none';
        if (avatarDefault) avatarDefault.style.display = 'none';
      }
      reader.readAsDataURL(file);
      
      const removeBtn = document.getElementById('remove-photo-btn');
      if (removeBtn) removeBtn.style.display = 'inline-block';
    }
  });
  
  const removePhotoBtn = document.getElementById('remove-photo-btn');
  if (removePhotoBtn) {
    removePhotoBtn.addEventListener('click', function() {
      if (confirm('Voulez-vous vraiment supprimer votre photo de profil ?')) {
        document.getElementById('remove-photo-input').value = '1';
        
        const currentPhoto = document.getElementById('current-photo');
        const avatarDefault = document.getElementById('avatar-default');
        const photoPreview = document.getElementById('photo-preview');
        
        if (currentPhoto) currentPhoto.style.display = 'none';
        if (photoPreview) photoPreview.style.display = 'none';
        
        let avatar = avatarDefault;
        if (!avatar) {
          const container = document.querySelector('.profile-photo-container');
          avatar = document.createElement('div');
          avatar.className = 'avatar-default';
          avatar.id = 'avatar-default';
          avatar.textContent = '<?php echo strtoupper(substr($profile["nom"], 0, 1)); ?>';
          container.insertBefore(avatar, container.firstChild);
        } else {
          avatar.style.display = 'flex';
        }
        
        this.style.display = 'none';
        document.getElementById('profile_photo').value = '';
      }
    });
  }
</script>
</body>
</html>