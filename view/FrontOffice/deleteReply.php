<?php
session_start();

include '../../Controller/ReplyController.php';

$replyC = new ReplyController();

if (isset($_GET['id']) && isset($_GET['forum_id'])) {
    $reply_id = intval($_GET['id']);
    $forum_id = intval($_GET['forum_id']);
    
    // Vérifier les permissions (dans un vrai projet, vérifier si l'utilisateur est l'auteur)
    // Pour le moment, on permet la suppression
    
    $replyC->deleteReply($reply_id);
    
    header("Location: thread.php?id=$forum_id&delete=success");
    exit();
}

header("Location: forums.php");
exit();
?>