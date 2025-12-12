<?php
session_start();

include '../../Controller/ReplyController.php';

$replyC = new ReplyController();

if (isset($_GET['reply_id']) && isset($_GET['forum_id'])) {
    $reply_id = intval($_GET['reply_id']);
    $forum_id = intval($_GET['forum_id']);
    
    // Créer un identifiant unique pour éviter les likes multiples
    $like_key = "liked_reply_" . $reply_id;
    
    // Vérifier si l'utilisateur a déjà liké
    if (!isset($_SESSION[$like_key])) {
        // Ajouter le like
        $replyC->addLike($reply_id);
        $_SESSION[$like_key] = true;
        $message = "success";
    } else {
        // L'utilisateur a déjà liké
        $message = "already_liked";
    }
    
    // Redirection avec message
    header("Location: thread.php?id=$forum_id&like_status=$message");
    exit();
}

// Si les paramètres sont manquants
header("Location: forums.php");
exit();
?>