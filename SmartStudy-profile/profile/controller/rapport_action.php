<?php

session_start();

// Vérifier si  admin
if (!isset($_SESSION['user']['id']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../view/login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Rapport.php';

$rapportModel = new Rapport($pdo);

$action = $_GET['action'] ?? '';
$rapportId = $_GET['id'] ?? 0;

if ($rapportId) {
    switch ($action) {
        case 'read':
            if ($rapportModel->markAsRead($rapportId)) {
                header("Location: ../view/back_office/rapports.php?success=marked_read");
            } else {
                header("Location: ../view/back_office/rapports.php?error=1");
            }
            exit();
            
        case 'unread':
            if ($rapportModel->markAsUnread($rapportId)) {
                header("Location: ../view/back_office/rapports.php?success=marked_unread");
            } else {
                header("Location: ../view/back_office/rapports.php?error=1");
            }
            exit();
            
        case 'pin':
            if ($rapportModel->togglePin($rapportId)) {
              
                $rapport = $rapportModel->find($rapportId);
                $success_type = $rapport['pin'] == 1 ? 'pinned' : 'unpinned';
                header("Location: ../view/back_office/rapports.php?success=" . $success_type);
            } else {
                header("Location: ../view/back_office/rapports.php?error=1");
            }
            exit();
            
        case 'delete':
            if ($rapportModel->delete($rapportId)) {
                header("Location: ../view/back_office/rapports.php?success=deleted");
            } else {
                header("Location: ../view/back_office/rapports.php?error=1");
            }
            exit();
    }
}


header("Location: ../view/back_office/rapports.php");
exit();