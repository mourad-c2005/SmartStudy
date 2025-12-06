<?php
// ✅ Démarrer la session seulement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir l'utilisateur temporaire
if (!isset($_SESSION['current_user'])) {
    $_SESSION['current_user'] = 'Invité';
    $_SESSION['is_admin'] = false;
}

// Fonctions utiles
function getCurrentUser() {
    return $_SESSION['current_user'] ?? 'Invité';
}

function setCurrentUser($name) {
    $_SESSION['current_user'] = $name;
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function setAdmin($isAdmin = true) {
    $_SESSION['is_admin'] = $isAdmin;
}
?>