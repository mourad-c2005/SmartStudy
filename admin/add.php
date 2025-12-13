<?php
// DÉBUT DU FICHIER - PAS DE CONTENU AVANT session_start()
session_start();

// Vérifier si l'admin est connecté
if (empty($_SESSION['admin_logged'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une matière - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* CSS INTÉGRÉ POUR ADD MATIERE */
        :root {
            --green: #4CAF50;
            --yellow: #FFEB3B;
            --light: #E8F5E8;
            --white: #ffffff;
            --dark: #2e7d32;
            --blue: #2196F3;
            --red: #e53935;
            --orange: #FF9800;
        }
        
        * { 
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--light);
            color: #333;
            margin: 0;
            padding: 0;
            padding-bottom: 60px;
        }
        
        /* Header - Utilisez le même que dans header.php */
        
        .main-content {
            padding: 2rem 5%;
            min-height: calc(100vh - 70px - 60px);
            max-width: 900px;
            margin: 0 auto;
        }
        
        .page-header {
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }
        
        .page-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        /* Carte du formulaire */
        .form-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            border-left: 5px solid var(--green);
            margin-bottom: 2rem;
        }
        
        /* Formulaire */
        .form-group {
            margin-bottom: 1.8rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #444;
            margin-bottom: 0.5rem;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label i {
            color: var(--green);
        }
        
        .form-input, .form-textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Open Sans', sans-serif;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        .form-textarea {
            min-height: 150px;
            resize: vertical;
            line-height: 1.5;
        }
        
        .form-helper {
            font-size: 0.85rem;
            color: #777;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        /* Boutons */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }
        
        .btn {
            padding: 0.9rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            flex: 1;
        }
        
        .btn-submit {
            background: var(--green);
            color: white;
        }
        
        .btn-submit:hover {
            background: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-cancel {
            background: #f5f5f5;
            color: #666;
            border: 2px solid #ddd;
        }
        
        .btn-cancel:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }
        
        /* Messages d'erreur/succès */
        .message {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            animation: slideIn 0.3s ease;
        }
        
        .message-success {
            background: #E8F5E9;
            color: var(--dark);
            border-left: 4px solid var(--green);
        }
        
        .message-error {
            background: #FFEBEE;
            color: var(--red);
            border-left: 4px solid var(--red);
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Indicateur de caractères */
        .char-count {
            text-align: right;
            font-size: 0.85rem;
            color: #999;
            margin-top: 0.3rem;
        }
        
        .char-count.warning {
            color: var(--orange);
        }
        
        .char-count.error {
            color: var(--red);
            font-weight: 600;
        }
        
        /* Indicateur de champ obligatoire */
        .required::after {
            content: " *";
            color: var(--red);
        }
        
        /* Preview */
        .preview-card {
            background: #f9f9f9;
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            border: 2px dashed #ddd;
        }
        
        .preview-title {
            font-weight: 600;
            color: #666;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .preview-content {
            color: #333;
            line-height: 1.6;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                padding: 1.5rem;
            }
            
            .form-card {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .form-input, .form-textarea {
                padding: 0.8rem;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>
            <i class="fas fa-book-medical"></i>
            Ajouter une nouvelle matière
        </h1>
        <p>Créez une nouvelle matière pour organiser vos cours et chapitres</p>
    </div>

    <!-- Messages -->
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="message message-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="message message-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="form-card">
        <form id="addMatiereForm" method="POST" action="<?= URLROOT ?>/Matiere/add">
            <div class="form-group">
                <label class="form-label required">
                    <i class="fas fa-tag"></i>
                    Nom de la matière
                </label>
                <input type="text" 
                       id="nom" 
                       name="nom" 
                       class="form-input"
                       placeholder="Ex: Mathématiques, Physique, Français..."
                       maxlength="100"
                       required>
                <div class="char-count" id="nom-counter">0/100 caractères</div>
                <div class="form-helper">
                    <i class="fas fa-info-circle"></i>
                    Donnez un nom clair et concis à votre matière
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-align-left"></i>
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          class="form-textarea"
                          placeholder="Décrivez brièvement cette matière, ses objectifs, son contenu..."
                          maxlength="500"></textarea>
                <div class="char-count" id="desc-counter">0/500 caractères</div>
                <div class="form-helper">
                    <i class="fas fa-info-circle"></i>
                    Optionnel - Cette description aide les étudiants à comprendre le sujet
                </div>
            </div>

            <!-- Prévisualisation -->
            <div class="preview-card" id="preview" style="display:none;">
                <div class="preview-title">
                    <i class="fas fa-eye"></i>
                    Aperçu
                </div>
                <div class="preview-content">
                    <strong id="preview-nom">[Nom de la matière]</strong><br>
                    <span id="preview-desc">[Description]</span>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?= URLROOT ?>/Matiere/index" class="btn btn-cancel">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-plus-circle"></i>
                    Créer la matière
                </button>
            </div>
        </form>
    </div>

    <!-- Guide -->
    <div class="form-card">
        <h3 style="color: var(--green); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-lightbulb"></i>
            Conseils pour créer une bonne matière
        </h3>
        <ul style="color: #666; line-height: 1.6; padding-left: 1.2rem;">
            <li><strong>Nom explicite :</strong> Utilisez un nom clair que tout le monde comprendra</li>
            <li><strong>Description utile :</strong> Expliquez brièvement ce que les étudiants apprendront</li>
            <li><strong>Organisation :</strong> Pensez à comment vous allez organiser les chapitres</li>
            <li><strong>Exemples :</strong> "Mathématiques Niveau 1", "Histoire de France", "Programmation Python"</li>
        </ul>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Compteurs de caractères
    const nomInput = document.getElementById('nom');
    const descInput = document.getElementById('description');
    const nomCounter = document.getElementById('nom-counter');
    const descCounter = document.getElementById('desc-counter');
    const preview = document.getElementById('preview');
    const previewNom = document.getElementById('preview-nom');
    const previewDesc = document.getElementById('preview-desc');
    
    // Mettre à jour les compteurs
    function updateCounters() {
        const nomLength = nomInput.value.length;
        const descLength = descInput.value.length;
        
        // Compteur nom
        nomCounter.textContent = `${nomLength}/100 caractères`;
        if (nomLength > 80) {
            nomCounter.className = 'char-count warning';
        } else if (nomLength >= 100) {
            nomCounter.className = 'char-count error';
        } else {
            nomCounter.className = 'char-count';
        }
        
        // Compteur description
        descCounter.textContent = `${descLength}/500 caractères`;
        if (descLength > 400) {
            descCounter.className = 'char-count warning';
        } else if (descLength >= 500) {
            descCounter.className = 'char-count error';
        } else {
            descCounter.className = 'char-count';
        }
        
        // Prévisualisation
        if (nomLength > 0) {
            preview.style.display = 'block';
            previewNom.textContent = nomInput.value || '[Nom de la matière]';
            previewDesc.textContent = descInput.value || '[Aucune description pour le moment]';
        } else {
            preview.style.display = 'none';
        }
    }
    
    // Écouter les changements
    nomInput.addEventListener('input', updateCounters);
    descInput.addEventListener('input', updateCounters);
    
    // Initialiser les compteurs
    updateCounters();
    
    // Validation du formulaire
    document.getElementById('addMatiereForm').addEventListener('submit', function(e) {
        const nom = nomInput.value.trim();
        const desc = descInput.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        // Validation du nom
        if (!nom) {
            errorMessage = 'Le nom de la matière est obligatoire.';
            isValid = false;
            nomInput.style.borderColor = 'var(--red)';
        } else if (nom.length > 100) {
            errorMessage = 'Le nom ne doit pas dépasser 100 caractères.';
            isValid = false;
            nomInput.style.borderColor = 'var(--red)';
        } else {
            nomInput.style.borderColor = '#ddd';
        }
        
        // Validation de la description
        if (desc.length > 500) {
            errorMessage = errorMessage || 'La description ne doit pas dépasser 500 caractères.';
            isValid = false;
            descInput.style.borderColor = 'var(--red)';
        } else {
            descInput.style.borderColor = '#ddd';
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Afficher le message d'erreur
            const errorDiv = document.createElement('div');
            errorDiv.className = 'message message-error';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${errorMessage}`;
            
            // Insérer au début du formulaire
            const formCard = document.querySelector('.form-card');
            const existingError = formCard.querySelector('.message-error');
            if (existingError) {
                existingError.remove();
            }
            formCard.insertBefore(errorDiv, formCard.firstChild);
            
            // Scroll vers l'erreur
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Focus sur le champ problématique
            if (!nom) {
                nomInput.focus();
            } else if (desc.length > 500) {
                descInput.focus();
            }
        }
    });
    
    // Confirmation avant de quitter si des données sont saisies
    window.addEventListener('beforeunload', function(e) {
        const nom = nomInput.value.trim();
        const desc = descInput.value.trim();
        
        if (nom || desc) {
            e.preventDefault();
            e.returnValue = 'Vous avez des modifications non enregistrées. Voulez-vous vraiment quitter ?';
            return e.returnValue;
        }
    });
    
    // Désactiver la confirmation quand le formulaire est soumis
    document.getElementById('addMatiereForm').addEventListener('submit', function() {
        window.removeEventListener('beforeunload', arguments.callee);
    });
</script>

</body>
</html>