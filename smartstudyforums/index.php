<?php
$controller = $_GET['controller'] ?? 'forum';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

require_once "controllers/{$controller}Controller.php";
$ctrlClass = ucfirst($controller).'Controller';
$ctrl = new $ctrlClass();

if($id) $ctrl->$action($id);
else $ctrl->$action();
?>
