<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/User.php';

if (isset($_GET['id'])) {
    try {
        $userModel = new User($pdo);
        $result = $userModel->delete($_GET['id']);
        
        if ($result) {
            header("Location: ../view/back_office/user.php?success=deleted");
        } else {
            header("Location: ../view/back_office/user.php?error=delete_failed");
        }
        exit();
    } catch (Exception $e) {
        header("Location: ../view/back_office/user.php?error=server_error");
        exit();
    }
}
?>