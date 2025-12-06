<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../Model/Reply.php');

class ReplyController {
    
    // Lister toutes les réponses d'un forum
    public function listRepliesByForum($forum_id) {
        $sql = "SELECT * FROM replies WHERE forum_id = :forum_id ORDER BY is_solution DESC, created_at ASC";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':forum_id', $forum_id);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Afficher une réponse spécifique
    public function showReply($id) {
        $sql = "SELECT * FROM replies WHERE id = :id";
        $db = ConfigForum::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            $reply = $query->fetch();
            return $reply;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Ajouter une réponse
    public function addReply(Reply $reply) {
        $sql = "INSERT INTO replies (forum_id, parent_id, author, content, is_solution, likes) 
                VALUES (:forum_id, :parent_id, :author, :content, :is_solution, :likes)";
        $db = ConfigForum::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'forum_id' => $reply->getForumId(),
                'parent_id' => $reply->getParentId(),
                'author' => $reply->getAuthor(),
                'content' => $reply->getContent(),
                'is_solution' => $reply->getIsSolution() ? 1 : 0,
                'likes' => $reply->getLikes()
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Modifier une réponse
    public function updateReply(Reply $reply, $id) {
        try {
            $db = ConfigForum::getConnexion();
            $query = $db->prepare(
                'UPDATE replies SET 
                    content = :content,
                    is_solution = :is_solution,
                    likes = :likes
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'content' => $reply->getContent(),
                'is_solution' => $reply->getIsSolution() ? 1 : 0,
                'likes' => $reply->getLikes()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Supprimer une réponse
    public function deleteReply($id) {
        $sql = "DELETE FROM replies WHERE id = :id";
        $db = ConfigForum::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Marquer comme solution
    public function markAsSolution($id) {
        $sql = "UPDATE replies SET is_solution = 1 WHERE id = :id";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Retirer la marque solution
    public function unmarkAsSolution($id) {
        $sql = "UPDATE replies SET is_solution = 0 WHERE id = :id";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Ajouter un like
    public function addLike($id) {
        $sql = "UPDATE replies SET likes = likes + 1 WHERE id = :id";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Retirer un like
    public function removeLike($id) {
        $sql = "UPDATE replies SET likes = GREATEST(likes - 1, 0) WHERE id = :id";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Compter les réponses d'un auteur
    public function countRepliesByAuthor($author) {
        $sql = "SELECT COUNT(*) as count FROM replies WHERE author = :author";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':author', $author);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    // ← NOUVELLE MÉTHODE : Récupérer les réponses d'une réponse
 public function getRepliesByParentId($parent_id) {
    $sql = "SELECT * FROM replies WHERE parent_id = :parent_id ORDER BY created_at ASC";
    $db = ConfigForum::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':parent_id', $parent_id);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
 }

// ← NOUVELLE MÉTHODE : Lister seulement les réponses principales (sans parent)
 public function listMainRepliesByForum($forum_id) {
    $sql = "SELECT * FROM replies WHERE forum_id = :forum_id AND parent_id IS NULL ORDER BY is_solution DESC, created_at ASC";
    $db = ConfigForum::getConnexion();
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':forum_id', $forum_id);
        $stmt->execute();
        return $stmt;
    } catch (Exception $e) {
        die('Error: ' . $e->getMessage());
    }
 }
}
?>