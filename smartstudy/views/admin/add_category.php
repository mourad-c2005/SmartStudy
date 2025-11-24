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
if (!class_exists('Category')) {
    require_once __DIR__ . "/../../models/Category.php";
}

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

$message = '';
$error = '';
$sections = Section::getAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['section_id'])) {
    $nom = trim($_POST['nom']);
    $section_id = intval($_POST['section_id']);
    
    if (!empty($nom) && $section_id > 0) {
        try {
            $pdo = Database::connect();
            
            // Debug: Check current database
            $currentDb = $pdo->query("SELECT DATABASE()")->fetchColumn();
            
            // Debug: Check if table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'categorie'")->rowCount() > 0;
            
            if (!$tableExists) {
                $error = "La table 'categorie' n'existe pas dans la base de données '" . $currentDb . "'. Tables disponibles: " . implode(", ", $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN));
            } else {
                // Try the insert with backticks
                $stmt = $pdo->prepare("INSERT INTO `categorie` (`nom`, `section_id`) VALUES (?, ?)");
                $stmt->execute([$nom, $section_id]);
                
                $message = "Catégorie ajoutée avec succès !";
                $_POST = []; // Clear form
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage() . " (Code: " . $e->getCode() . ")";
            
            // Additional debug info
            try {
                $currentDb = $pdo->query("SELECT DATABASE()")->fetchColumn();
                $error .= "<br>Base de données actuelle: " . htmlspecialchars($currentDb);
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                $error .= "<br>Tables disponibles: " . implode(", ", $tables);
            } catch (Exception $e2) {
                // Ignore debug errors
            }
        }
    } else {
        $error = "Tous les champs sont requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ajouter une Catégorie | SmartStudy+ Admin</title>
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
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 0.9rem 1.2rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      font-family: 'Open Sans', sans-serif;
      transition: 0.3s;
    }
    .form-group input:focus,
    .form-group select:focus {
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
    <a href="index.php?controller=admin_categories&action=list" class="btn-back">
      <i class="fas fa-arrow-left"></i> Retour aux catégories
    </a>

    <div class="form-card">
      <h1><i class="fas fa-plus-circle"></i> Ajouter une Catégorie</h1>

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

      <form method="POST" action="" id="categoryForm" novalidate>
        <div class="form-group">
          <label for="nom"><i class="fas fa-tag"></i> Nom de la catégorie</label>
          <input type="text" id="nom" name="nom" placeholder="Ex: PHP, JavaScript, Python..." required value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
          <small class="error-message" id="nomError" style="display: none; color: var(--red); margin-top: 0.5rem;"></small>
        </div>

        <div class="form-group">
          <label for="section_id"><i class="fas fa-folder"></i> Section</label>
          <select id="section_id" name="section_id" required>
            <option value="">Sélectionnez une section</option>
            <?php foreach ($sections as $section): ?>
              <option value="<?= $section['id'] ?>" <?= (isset($_POST['section_id']) && $_POST['section_id'] == $section['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($section['nom']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="error-message" id="section_idError" style="display: none; color: var(--red); margin-top: 0.5rem;"></small>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fas fa-save"></i> Ajouter la catégorie
        </button>
      </form>
    </div>
  </div>

  <script>
    // Validation JavaScript pour le formulaire
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Réinitialiser les erreurs
      clearErrors();
      
      // Récupérer les valeurs
      const nom = document.getElementById('nom').value.trim();
      const sectionId = document.getElementById('section_id').value;
      
      let isValid = true;
      
      // Validation du nom
      if (!nom) {
        showError('nom', 'Le nom de la catégorie est requis.');
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
      
      // Validation de la section
      if (!sectionId || sectionId === '') {
        showError('section_id', 'Veuillez sélectionner une section.');
        isValid = false;
      } else if (isNaN(sectionId) || parseInt(sectionId) <= 0) {
        showError('section_id', 'Section invalide.');
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
      } else if (nom) {
        clearError('nom');
      }
    });
    
    document.getElementById('nom').addEventListener('input', function() {
      if (this.value.trim().length >= 2) {
        clearError('nom');
      }
    });
    
    document.getElementById('section_id').addEventListener('change', function() {
      if (this.value && this.value !== '') {
        clearError('section_id');
      }
    });
    
    function showError(fieldId, message) {
      const errorElement = document.getElementById(fieldId + 'Error');
      if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        const field = document.getElementById(fieldId);
        if (field) {
          field.style.borderColor = 'var(--red)';
        }
      }
    }
    
    function clearError(fieldId) {
      const errorElement = document.getElementById(fieldId + 'Error');
      if (errorElement) {
        errorElement.style.display = 'none';
        const field = document.getElementById(fieldId);
        if (field) {
          field.style.borderColor = '#e0e0e0';
        }
      }
    }
    
    function clearErrors() {
      document.querySelectorAll('.error-message').forEach(el => {
        el.style.display = 'none';
      });
      document.querySelectorAll('.form-group input, .form-group select').forEach(input => {
        input.style.borderColor = '#e0e0e0';
      });
    }
  </script>
</body>
</html>
