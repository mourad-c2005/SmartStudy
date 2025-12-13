<?php
// index.php - Front Controller
session_start();

// Load configuration
require_once 'config/config.php';

// Autoload core classes
require_once 'app/core/Database.php';
require_once 'app/core/Model.php';
require_once 'app/core/Controller.php';

// Sanitize URL
$url = isset($_GET['url']) ? filter_var($_GET['url'], FILTER_SANITIZE_URL) : '';
$url = explode('/', trim($url, '/'));

// Default controller/method
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$method = $url[1] ?? 'index';
$params = array_slice($url, 2);

// Prevent loading non-existing or disallowed controller
$controllerFile = "app/controllers/$controllerName.php";
if (!file_exists($controllerFile)) {
    // Fallback to HomeController
    $controllerName = 'HomeController';
    $controllerFile = "app/controllers/$controllerName.php";
}

require_once $controllerFile;

// Create controller instance
$controller = new $controllerName();

// Check if method exists, else fallback to index
if (!method_exists($controller, $method)) {
    $method = 'index';
}

// Call controller method
call_user_func_array([$controller, $method], $params);
