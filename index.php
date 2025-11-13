<?php
// index.php
session_start();
require_once 'config/config.php';

// autoload core files
require_once 'app/core/Database.php';
require_once 'app/core/Model.php';
require_once 'app/core/Controller.php';

// read url
$url = isset($_GET['url']) ? explode('/', trim($_GET['url'], '/')) : [];

// default controller and method
$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$method = $url[1] ?? 'index';
$params = array_slice($url, 2);

// if controller file exists load it, else fallback to HomeController
$controllerFile = 'app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    // fallback to MatiereController if user requested 'Matiere' style
    $controllerName = 'MatiereController';
    $controllerFile = 'app/controllers/' . $controllerName . '.php';
}

require_once $controllerFile;
$controller = new $controllerName();

// call method if exists
if (method_exists($controller, $method)) {
    call_user_func_array([$controller, $method], $params);
} else {
    // default to index
    $controller->index();
}
