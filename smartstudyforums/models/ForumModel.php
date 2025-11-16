<?php
require_once 'config/database.php';

class ForumModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    // Get all topics
    public function getTopics() {
        $stmt = $this->pdo->query("SELECT t.*, u.name AS USER_ID, 
          (SELECT COUNT(*) FROM replies r WHERE r.topic_id=t.TOPIC_ID) AS REPLIES_COUNT
          FROM topics t 
          JOIN users u ON t.user_id=u.id 
          ORDER BY t.created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single topic
    public function getTopic($id) {
        $stmt = $this->pdo->prepare("SELECT t.*, u.name AS USER_ID FROM topics t 
            JOIN users u ON t.user_id=u.id WHERE t.TOPIC_ID=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get replies for topic
    public function getReplies($topic_id) {
        $stmt = $this->pdo->prepare("SELECT r.*, u.name AS USER_ID FROM replies r 
            JOIN users u ON r.user_id=u.id WHERE r.topic_id=? ORDER BY r.CREATED_AT ASC");
        $stmt->execute([$topic_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add new topic
    public function addTopic($title, $category, $content, $user_id) {
        $stmt = $this->pdo->prepare("INSERT INTO topics (TITLE,CATEGORY,CONTENT,USER_ID,CREATED_AT) VALUES (?,?,?,?,NOW())");
        $stmt->execute([$title,$category,$content,$user_id]);
        return $this->pdo->lastInsertId();
    }

    // Add reply
    public function addReply($topic_id, $content, $user_id) {
        $stmt = $this->pdo->prepare("INSERT INTO replies (TOPIC_ID,CONTENT,USER_ID,CREATED_AT) VALUES (?,?,?,NOW())");
        $stmt->execute([$topic_id,$content,$user_id]);
    }
}
?>
