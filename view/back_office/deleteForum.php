<?php
require_once('../../Controller/ForumController.php');

$forumC = new ForumController();

// Vérifier si l'ID est bien passé en paramètre
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $forumC->deleteForum($_GET['id']);
}

// Redirection vers la liste des forums
header('Location: manageForums.php');
exit();
?>