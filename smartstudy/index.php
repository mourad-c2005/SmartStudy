<?php
/**
 * Front Controller - Point d'entrée unique de l'application
 * Toutes les requêtes passent par ce fichier
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/FrontController.php';

// Router toutes les requêtes
$frontController = new FrontController();
$frontController->handleRequest();
