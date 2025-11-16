<?php
require_once 'models/ForumModel.php';

class ForumController {
    private $model;

    public function __construct() {
        $this->model = new ForumModel();
    }

    // List topics
    public function list() {
        $topics = $this->model->getTopics();
        require 'views/forum_list.php';
    }

    // Show single topic
    public function view($id) {
        $topic = $this->model->getTopic($id);
        $replies = $this->model->getReplies($id);
        require 'views/forum_thread.php';
    }

    // Add topic (from form)
    public function create() {
        if($_SERVER['REQUEST_METHOD']=='POST') {
            $this->model->addTopic($_POST['title'], $_POST['category'], $_POST['content'], 1);
            header('Location: index.php?controller=forum&action=list');
        } else {
            require 'views/forum_create.php';
        }
    }

    // Add reply
    public function reply($topic_id) {
        if($_SERVER['REQUEST_METHOD']=='POST') {
            $this->model->addReply($topic_id, $_POST['content'], 1);
            header("Location: index.php?controller=forum&action=view&id=$topic_id");
        }
    }
}
?>
