<?php
// Session is already started in index.php
// No need to start it again

// Check if required classes are already loaded, if not load them
if (!class_exists('Database')) {
    require_once __DIR__ . "/../../config/Database.php";
}
if (!class_exists('Category')) {
    require_once __DIR__ . "/../../models/Category.php";
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
$sections = Section::getAll();
$categories = [];

// Get categories if section is selected
if (isset($_GET['section_id'])) {
    $categories = Category::getBySection($_GET['section_id']);
} else {
    // Get all categories
    foreach ($sections as $section) {
        $sectionCats = Category::getBySection($section['id']);
        foreach ($sectionCats as $cat) {
            $cat['section_nom'] = $section['nom'];
            $categories[] = $cat;
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre']) && isset($_POST['id_categorie'])) {
    $titre = trim($_POST['titre']);
    $prix = isset($_POST['prix']) ? floatval($_POST['prix']) : 0;
    $id_categorie = intval($_POST['id_categorie']);
    
    // Handle multiple URLs - store as JSON or comma-separated
    $urls = [];
    if (isset($_POST['urls']) && is_array($_POST['urls'])) {
        foreach ($_POST['urls'] as $url) {
            $url = trim($url);
            if (!empty($url)) {
                $urls[] = $url;
            }
        }
    }
    // Store all URLs as JSON in the url field
    $urlsJson = !empty($urls) ? json_encode($urls) : '';
    // For display, use first URL if only one, or JSON if multiple
    $urlToStore = !empty($urls) ? (count($urls) > 1 ? $urlsJson : $urls[0]) : '';
    
    if (!empty($titre) && $id_categorie > 0) {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("INSERT INTO formation (titre, url, prix, id_categorie) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titre, $urlToStore, $prix, $id_categorie]);
            
            // If multiple URLs, store them in a separate field or table
            // For now, we'll store the first URL in url field and can extend later
            
            $message = "Formation ajoutée avec succès !";
            if (count($urls) > 1) {
                $message .= " (" . count($urls) . " liens ajoutés)";
            }
            $_POST = []; // Clear form
            // Redirect to formations list
            header("Location: index.php?controller=admin_formations&action=list&success=added");
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout : " . $e->getMessage();
        }
    } else {
        $error = "Le titre et la catégorie sont requis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ajouter une Formation | SmartStudy+ Admin</title>
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
    .url-input-group {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
      align-items: center;
    }
    .url-input {
      flex: 1;
      padding: 0.9rem 1.2rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 1rem;
      font-family: 'Open Sans', sans-serif;
      transition: 0.3s;
    }
    .url-input:focus {
      outline: none;
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    .btn-remove-url {
      background: var(--red);
      color: white;
      border: none;
      padding: 0.6rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
      font-size: 0.9rem;
      white-space: nowrap;
    }
    .btn-remove-url:hover {
      background: #d32f2f;
      transform: translateY(-1px);
    }
    .btn-add-url {
      background: var(--yellow);
      color: #333;
      border: none;
      padding: 0.6rem 1.5rem;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
      margin-top: 0.5rem;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .btn-add-url:hover {
      background: #fbc02d;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <nav class="top-nav">
    <a href="index.php?controller=admin&action=dashboard" class="logo">SmartStudy+ <span style="color: var(--red); font-size: 1rem;">Admin</span></a>
    <a href="logout.php" style="color: var(--red); text-decoration: none;">Déconnexion</a>
  </nav>

  <div class="main-content">
    <a href="index.php?controller=admin_formations&action=list" class="btn-back">
      <i class="fas fa-arrow-left"></i> Retour aux formations
    </a>

    <div class="form-card">
      <h1><i class="fas fa-plus-circle"></i> Ajouter une Formation</h1>

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

      <form method="POST" action="" id="formationForm" novalidate>
        <div class="form-group">
          <label for="titre"><i class="fas fa-book"></i> Titre de la formation</label>
          <input type="text" id="titre" name="titre" placeholder="Ex: Apprendre PHP de A à Z" required value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>">
          <small class="error-message" id="titreError" style="display: none; color: var(--red); margin-top: 0.5rem;"></small>
        </div>

        <div class="form-group">
          <label for="id_categorie"><i class="fas fa-tag"></i> Catégorie</label>
          <select id="id_categorie" name="id_categorie" required>
            <option value="">Sélectionnez une catégorie</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>" <?= (isset($_POST['id_categorie']) && $_POST['id_categorie'] == $category['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['nom']) ?>
                <?= isset($category['section_nom']) ? ' - ' . htmlspecialchars($category['section_nom']) : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="error-message" id="id_categorieError" style="display: none; color: var(--red); margin-top: 0.5rem;"></small>
        </div>

        <div class="form-group">
          <label><i class="fas fa-link"></i> Liens/URLs (optionnel)</label>
          <div id="urls-container">
            <div class="url-input-group">
              <input type="url" name="urls[]" placeholder="https://example.com/formation" class="url-input" value="<?= isset($_POST['urls'][0]) ? htmlspecialchars($_POST['urls'][0]) : '' ?>">
              <button type="button" class="btn-remove-url" onclick="removeUrlField(this)" style="display: none;">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <button type="button" class="btn-add-url" onclick="addUrlField()">
            <i class="fas fa-plus"></i> Ajouter un lien
          </button>
          <small class="error-message" id="urlsError" style="display: none; color: var(--red); margin-top: 0.5rem;"></small>
        </div>

        <div class="form-group">
          <label for="prix"><i class="fas fa-euro-sign"></i> Prix (€)</label>
          <input type="number" id="prix" name="prix" step="0.01" min="0" placeholder="0.00" value="<?= isset($_POST['prix']) ? htmlspecialchars($_POST['prix']) : '0' ?>">
          <small class="error-message" id="prixError" style="display: none; color: var(--red); margin-top: 0.5rem;"></small>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fas fa-save"></i> Ajouter la formation
        </button>
      </form>
    </div>
  </div>

  <script>
    // Validation JavaScript pour le formulaire
    document.getElementById('formationForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Réinitialiser les erreurs
      clearErrors();
      
      // Récupérer les valeurs
      const titre = document.getElementById('titre').value.trim();
      const idCategorie = document.getElementById('id_categorie').value;
      const prix = parseFloat(document.getElementById('prix').value) || 0;
      const urlInputs = document.querySelectorAll('input[name="urls[]"]');
      
      let isValid = true;
      
      // Validation du titre
      if (!titre) {
        showError('titre', 'Le titre de la formation est requis.');
        isValid = false;
      } else if (titre.length < 3) {
        showError('titre', 'Le titre doit contenir au moins 3 caractères.');
        isValid = false;
      } else if (titre.length > 255) {
        showError('titre', 'Le titre ne peut pas dépasser 255 caractères.');
        isValid = false;
      }
      
      // Validation de la catégorie
      if (!idCategorie || idCategorie === '') {
        showError('id_categorie', 'Veuillez sélectionner une catégorie.');
        isValid = false;
      } else if (isNaN(idCategorie) || parseInt(idCategorie) <= 0) {
        showError('id_categorie', 'Catégorie invalide.');
        isValid = false;
      }
      
      // Validation du prix
      if (isNaN(prix)) {
        showError('prix', 'Le prix doit être un nombre valide.');
        isValid = false;
      } else if (prix < 0) {
        showError('prix', 'Le prix ne peut pas être négatif.');
        isValid = false;
      } else if (prix > 999999.99) {
        showError('prix', 'Le prix est trop élevé (maximum: 999999.99 €).');
        isValid = false;
      }
      
      // Validation des URLs (optionnel mais si présentes, doivent être valides)
      urlInputs.forEach((input, index) => {
        const url = input.value.trim();
        if (url) {
          try {
            const urlObj = new URL(url);
            if (!['http:', 'https:'].includes(urlObj.protocol)) {
              showError('urls', `L'URL ${index + 1} doit utiliser le protocole HTTP ou HTTPS.`);
              isValid = false;
              input.style.borderColor = 'var(--red)';
            } else {
              input.style.borderColor = '#e0e0e0';
            }
          } catch (e) {
            showError('urls', `L'URL ${index + 1} n'est pas valide.`);
            isValid = false;
            input.style.borderColor = 'var(--red)';
          }
        }
      });
      
      // Si valide, soumettre le formulaire
      if (isValid) {
        this.submit();
      }
    });
    
    // Validation en temps réel
    document.getElementById('titre').addEventListener('blur', function() {
      const titre = this.value.trim();
      if (titre && titre.length < 3) {
        showError('titre', 'Le titre doit contenir au moins 3 caractères.');
      } else if (titre) {
        clearError('titre');
      }
    });
    
    document.getElementById('titre').addEventListener('input', function() {
      if (this.value.trim().length >= 3) {
        clearError('titre');
      }
    });
    
    document.getElementById('id_categorie').addEventListener('change', function() {
      if (this.value && this.value !== '') {
        clearError('id_categorie');
      }
    });
    
    document.getElementById('prix').addEventListener('blur', function() {
      const prix = parseFloat(this.value) || 0;
      if (prix < 0) {
        showError('prix', 'Le prix ne peut pas être négatif.');
      } else if (prix > 999999.99) {
        showError('prix', 'Le prix est trop élevé.');
      } else {
        clearError('prix');
      }
    });
    
    document.getElementById('prix').addEventListener('input', function() {
      const prix = parseFloat(this.value) || 0;
      if (prix >= 0 && prix <= 999999.99) {
        clearError('prix');
      }
    });
    
    // Validation des URLs en temps réel
    document.addEventListener('input', function(e) {
      if (e.target.matches('input[name="urls[]"]')) {
        const url = e.target.value.trim();
        if (url) {
          try {
            const urlObj = new URL(url);
            if (!['http:', 'https:'].includes(urlObj.protocol)) {
              e.target.style.borderColor = 'var(--red)';
            } else {
              e.target.style.borderColor = '#e0e0e0';
              clearError('urls');
            }
          } catch (e) {
            e.target.style.borderColor = 'var(--red)';
          }
        } else {
          e.target.style.borderColor = '#e0e0e0';
        }
      }
    });
    
    function addUrlField() {
      const container = document.getElementById('urls-container');
      const newField = document.createElement('div');
      newField.className = 'url-input-group';
      newField.innerHTML = `
        <input type="url" name="urls[]" placeholder="https://example.com/formation" class="url-input">
        <button type="button" class="btn-remove-url" onclick="removeUrlField(this)">
          <i class="fas fa-times"></i>
        </button>
      `;
      container.appendChild(newField);
      updateRemoveButtons();
    }

    function removeUrlField(button) {
      const group = button.closest('.url-input-group');
      group.remove();
      updateRemoveButtons();
    }

    function updateRemoveButtons() {
      const groups = document.querySelectorAll('.url-input-group');
      groups.forEach((group) => {
        const removeBtn = group.querySelector('.btn-remove-url');
        if (groups.length > 1) {
          removeBtn.style.display = 'block';
        } else {
          removeBtn.style.display = 'none';
        }
      });
    }
    
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
      document.querySelectorAll('.form-group input, .form-group select, .url-input').forEach(input => {
        input.style.borderColor = '#e0e0e0';
      });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      updateRemoveButtons();
    });
  </script>
</body>
</html>
