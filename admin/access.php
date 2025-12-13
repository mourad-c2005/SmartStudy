<?php
session_start();
if (empty($_SESSION['admin_logged'])) { 
    header('Location: login.php'); 
    exit; 
}

require_once "../config/config.php";
require_once "../app/core/Database.php";
require_once "../app/core/Model.php";        
require_once "../app/models/AdminSetting.php";

$settingModel = new AdminSetting();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $val = ($_POST['allow'] == '1') ? '1' : '0';
    $settingModel->set('allow_course_crud', $val);
    $msg = "Paramètre mis à jour.";
}

$current = $settingModel->get('allow_course_crud');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrôle d'accès - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Styles spécifiques pour cette page */
        .admin-content {
            padding: 2rem 5%;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .settings-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            margin-bottom: 2rem;
        }
        
        .settings-card h2 {
            font-family: 'Montserrat', sans-serif;
            color: var(--green);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light);
            font-size: 1.8rem;
        }
        
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            margin: 1.5rem 0 2rem;
        }
        
        .radio-label {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            border: 2px solid #eee;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .radio-label:hover {
            border-color: var(--green);
            background: rgba(76, 175, 80, 0.05);
        }
        
        .radio-label input[type="radio"] {
            margin-top: 0.3rem;
            accent-color: var(--green);
            transform: scale(1.2);
        }
        
        .radio-text {
            font-size: 1.05rem;
            color: #444;
            line-height: 1.5;
        }
        
        .radio-text strong {
            color: var(--dark);
        }
        
        .message-success {
            background: #E8F5E9;
            color: var(--dark);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--green);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .message-success i {
            font-size: 1.2rem;
        }
        
        .btn-submit {
            background: var(--green);
            color: white;
            padding: 0.8rem 2.5rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-submit:hover {
            background: var(--dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-submit i {
            font-size: 1rem;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-left: 1rem;
        }
        
        .status-active {
            background: #E8F5E9;
            color: var(--dark);
        }
        
        .status-inactive {
            background: #FFEBEE;
            color: #C62828;
        }
        
        @media (max-width: 768px) {
            .admin-content {
                padding: 1.5rem;
            }
            
            .settings-card {
                padding: 1.5rem;
            }
            
            .radio-label {
                flex-direction: column;
                gap: 0.8rem;
                padding: 1rem;
            }
            
            .radio-text {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="main-content">
    <div class="admin-content">
        
        <div class="welcome-card">
            <h1><i class="fas fa-cogs"></i> Contrôle d'Accès</h1>
            <p>Gérez les autorisations de modification des cours et modules du système.</p>
        </div>
        
        <div class="settings-card">
            <h2>Modification/Ajout/Suppression de Cours</h2>
            
            <?php if ($msg): ?>
                <div class="message-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($msg) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="allow" value="1" <?= $current === '1' ? 'checked' : '' ?>>
                        <div class="radio-text">
                            <strong>Autoriser</strong> l'administrateur à ajouter, modifier ou supprimer des cours
                            <?php if ($current === '1'): ?>
                                <span class="status-indicator status-active">
                                    <i class="fas fa-check"></i> Actuellement actif
                                </span>
                            <?php endif; ?>
                        </div>
                    </label>
                    
                    <label class="radio-label">
                        <input type="radio" name="allow" value="0" <?= $current === '0' ? 'checked' : '' ?>>
                        <div class="radio-text">
                            <strong>Désactiver</strong> l'ajout et la suppression (mode lecture seule)
                            <?php if ($current === '0'): ?>
                                <span class="status-indicator status-inactive">
                                    <i class="fas fa-lock"></i> Actuellement désactivé
                                </span>
                            <?php endif; ?>
                        </div>
                    </label>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Enregistrer les paramètres
                </button>
            </form>
        </div>
        
        <div class="settings-card">
            <h3><i class="fas fa-info-circle"></i> Informations</h3>
            <p>Cette option contrôle les permissions au niveau système. En mode "lecture seule", l'administrateur pourra toujours visualiser les cours mais ne pourra pas les modifier, ajouter ou supprimer.</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>