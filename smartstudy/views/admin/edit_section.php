<?php
// Session is already started in index.php
// No need to start it again

// Check if required classes are already loaded, if not load them
if (!class_exists('Database')) {
    require_once __DIR__ . "/../../config/Database.php";
}
if (!class_exists('Section')) {
    require_once __DIR__ . "/../../models/Section.php";
}

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

$message = '';
$error = '';
$section = null;

// Get section ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php?controller=admin_sections&action=list');
    exit;
}

// Get section data
try {
    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT * FROM sections WHERE id = ?");
    $stmt->execute([$id]);
    $section = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$section) {
        header('Location: index.php?controller=admin_sections&action=list');
        exit;
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération : " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
    $nom = trim($_POST['nom']);
    
    if (!empty($nom)) {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("UPDATE sections SET nom = ? WHERE id = ?");
            $stmt->execute([$nom, $id]);
            
            $message = "Section modifiée avec succès !";
            // Refresh section data
            $stmt = $pdo->prepare("SELECT * FROM sections WHERE id = ?");
            $stmt->execute([$id]);
            $section = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification : " . $e->getMessage();
        }
    } else {
        $error = "Le nom de la section est requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Modifier une Section | SmartStudy+ Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --green: #4CAF50;
      --yellow: #FFEB3B;
      --light: #E8F5E8;
      --white: #ffffff;
      --dark: #2e7d32;
      --red: #f44336;
    }
    body {
      font-family: 'Open Sans', sans-serif;
      background: var(--light);
      color: #333;
      margin: 0;
    }
    .top-nav {
      background: var(--white);
      padding: 1rem 5%;
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--green);
      text-decoration: none;
    }
    .main-content {
      padding: 3rem 5%;
      max-width: 800px;
      margin: 0 auto;
    }
    .form-card {
      background: var(--white);
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    }
    .form-card h1 {
      font-family: 'Montserrat', sans-serif;
      color: var(--green);
      margin-bottom: 2rem;
    }
    .form-group {
      margin-bottom: 1.5rem;
    }
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #555;
    }
    .form-group input {
      width: 100%;
      padding: 0.9rem 1.2rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      font-family: 'Open Sans', sans-serif;
      transition: 0.3s;
    }
    .form-group input:focus {
      outline: none;
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    .btn-submit {
      background: var(--green);
      color: white;
      padding: 0.9rem 2.5rem;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn-submit:hover {
      background: var(--dark);
      transform: translateY(-2px);
    }
    .btn-back {
      background: #ccc;
      color: #333;
      padding: 0.6rem 1.5rem;
      border-radius: 30px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
    }
    .alert {
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
    }
    .alert-success {
      background: #e8f5e9;
      color: #2e7d32;
      border-left: 4px solid var(--green);
    }
    .alert-error {
      background: #ffebee;
      color: #c62828;
      border-left: 4px solid var(--red);
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <a href="index.php?controller=admin&action=dashboard" class="logo">SmartStudy+ <span style="color: var(--red); font-size: 1rem;">Admin</span></a>
    <a href="logout.php" style="color: var(--red); text-decoration: none;">Déconnexion</a>
  </nav>

  <div class="main-content">
    <a href="index.php?controller=admin_sections&action=list" class="btn-back">
      <i class="fas fa-arrow-left"></i> Retour aux sections
    </a>

    <div class="form-card">
      <h1><i class="fas fa-edit"></i> Modifier la Section</h1>

      <?php if ($message): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <?php if ($error): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($section): ?>
        <form method="POST" action="" id="editSectionForm" novalidate>
          <div class="form-group">
            <label for="nom"><i class="fas fa-folder"></i> Nom de la section</label>
            <input type="text" id="nom" name="nom" placeholder="Ex: Développement Web" required value="<?= htmlspecialchars($section['nom']) ?>">
            <small class="error-message" id="nomError" style="display: none; color: var(--red); margin-top: 0.5rem;"></small>
          </div>

          <button type="submit" class="btn-submit">
            <i class="fas fa-save"></i> Enregistrer les modifications
          </button>
        </form>
      <?php else: ?>
        <div class="alert alert-error">
          Section non trouvée.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Validation JavaScript pour le formulaire d'édition
    const editForm = document.getElementById('editSectionForm');
    if (editForm) {
      editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Réinitialiser les erreurs
        clearErrors();
        
        // Récupérer les valeurs
        const nom = document.getElementById('nom').value.trim();
        
        let isValid = true;
        
        // Validation du nom
        if (!nom) {
          showError('nom', 'Le nom de la section est requis.');
          isValid = false;
        } else if (nom.length < 2) {
          showError('nom', 'Le nom doit contenir au moins 2 caractères.');
          isValid = false;
        } else if (nom.length > 255) {
          showError('nom', 'Le nom ne peut pas dépasser 255 caractères.');
          isValid = false;
        } else if (!/^[a-zA-Z0-9\s\-_àáâãäåæçèéêëìíîïðñòóôõöøùúûüýþÿÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞŸ]+$/.test(nom)) {
          showError('nom', 'Le nom contient des caractères non autorisés.');
          isValid = false;
        }
        
        // Si valide, soumettre le formulaire
        if (isValid) {
          this.submit();
        }
      });
      
      // Validation en temps réel
      document.getElementById('nom').addEventListener('blur', function() {
        const nom = this.value.trim();
        if (nom && nom.length < 2) {
          showError('nom', 'Le nom doit contenir au moins 2 caractères.');
        } else {
          clearError('nom');
        }
      });
      
      document.getElementById('nom').addEventListener('input', function() {
        if (this.value.trim().length >= 2) {
          clearError('nom');
        }
      });
    }
    
    function showError(fieldId, message) {
      const errorElement = document.getElementById(fieldId + 'Error');
      if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        document.getElementById(fieldId).style.borderColor = 'var(--red)';
      }
    }
    
    function clearError(fieldId) {
      const errorElement = document.getElementById(fieldId + 'Error');
      if (errorElement) {
        errorElement.style.display = 'none';
        document.getElementById(fieldId).style.borderColor = '#e0e0e0';
      }
    }
    
    function clearErrors() {
      document.querySelectorAll('.error-message').forEach(el => {
        el.style.display = 'none';
      });
      document.querySelectorAll('.form-group input').forEach(input => {
        input.style.borderColor = '#e0e0e0';
      });
    }
  </script>
</body>
</html>
