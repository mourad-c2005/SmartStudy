<?php
// public/index.php : Front controller

error_reporting(E_ALL);
ini_set('display_errors', 1);

// On charge les classes de base
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Planning.php';
require_once __DIR__ . '/../services/PlanningService.php';

// Récupération des paramètres GET
$controller = $_GET['controller'] ?? 'planning';   // planning par défaut
$controller = strtolower($controller);             // <<< on met tout en minuscules
$action     = $_GET['action'] ?? 'index';          // index par défaut

// Sélection du contrôleur
switch ($controller) {

    case 'planning':
        require_once __DIR__ . '/../controllers/PlanningController.php';
        $controllerInstance = new PlanningController();
        break;

    case 'adminplanning':   // <<< tout en minuscules
        require_once __DIR__ . '/../controllers/AdminPlanningController.php';
        $controllerInstance = new AdminPlanningController();
        break;

    default:
        http_response_code(404);
        echo "Controller inconnu : " . htmlspecialchars($controller);
        exit;
}

// Vérifier que l’action existe
if (!method_exists($controllerInstance, $action)) {
    http_response_code(404);
    echo "Action '$action' introuvable dans le contrôleur.";
    exit;
}

// Appeler l’action demandée
$controllerInstance->$action();
