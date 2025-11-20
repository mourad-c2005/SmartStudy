<?php
session_start();
if (empty($_SESSION['admin_logged'])) { 
    header('Location: login.php'); 
    exit; 
}

require_once "../config/config.php";
require_once "../app/core/Database.php";
require_once "../app/core/Model.php";        // 👈 OBLIGATOIRE
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
    <title>Contrôle d'accès - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<h2>Contrôle d'accès : Modification/Ajout/Suppression de Cours</h2>

<?php if ($msg): ?>
    <p style="color: green;"><?= $msg ?></p>
<?php endif; ?>

<form method="POST">

    <label>
        <input type="radio" name="allow" value="1" <?= $current === '1' ? 'checked' : '' ?>>
        Autoriser l’admin à ajouter / modifier / supprimer les cours
    </label>
    <br>

    <label>
        <input type="radio" name="allow" value="0" <?= $current === '0' ? 'checked' : '' ?>>
        Désactiver l’ajout / suppression (lecture seule)
    </label>

    <br><br>
    <button type="submit" class="btn-start">Enregistrer</button>

</form>

<?php include 'footer.php'; ?>

</body>
</html>
