<?php
// controllers/update.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/User.php';

// Debug (à supprimer après)
echo "<pre>POST data:\n";
print_r($_POST);
echo "</pre>";

// Vérifie que le formulaire est bien soumis
if (!isset($_POST['user_id'])) {
    header('Location: ../view/home.php?error=invalid_form');
    exit();
}

$user_id = (int)$_POST['user_id'];

try {
    $userModel = new User($pdo);
    
    // Récupérer les données actuelles de l'utilisateur
    $currentUser = $userModel->getById($user_id);
    
    if (!$currentUser) {
        header('Location: ../view/home.php?error=user_not_found');
        exit();
    }

    echo "<pre>Données actuelles de l'utilisateur:\n";
    print_r($currentUser);
    echo "</pre>";

    $updateData = [];

    // Vérifier chaque champ - si vide, garder l'ancienne valeur
    $updateData['nom'] = !empty(trim($_POST['nom'])) ? trim($_POST['nom']) : $currentUser['nom'];
    
    $updateData['email'] = !empty(trim($_POST['email'])) ? trim($_POST['email']) : $currentUser['email'];
    
    // Pour le rôle, vérifier si c'est une chaîne vide (option sélectionnée)
    $updateData['role'] = (!empty($_POST['role']) && $_POST['role'] !== '') ? $_POST['role'] : $currentUser['role'];

    // Debug pour voir les données à mettre à jour
    echo "<pre>Données à mettre à jour:\n";
    print_r($updateData);
    echo "</pre>";

    // Vérifier s'il y a réellement des changements
    $hasChanges = false;
    foreach ($updateData as $key => $value) {
        if (isset($currentUser[$key]) && $currentUser[$key] != $value) {
            $hasChanges = true;
            break;
        }
    }

    if (!$hasChanges) {
        header('Location: ../view/back_office/user.php?update_msg=Aucune modification apportée');
        exit();
    }

    $result = $userModel->update($user_id, $updateData);

    if ($result) {
        header('Location: ../view/back_office/user.php?success=updated');
    } else {
        header('Location: ../view/back_office/user.php?error=update_failed');
    }
    exit();

} catch (Exception $e) {
    error_log("Erreur update: " . $e->getMessage());
    header('Location: ../view/back_office/user.php?error=server_error');
    exit();
}
?>