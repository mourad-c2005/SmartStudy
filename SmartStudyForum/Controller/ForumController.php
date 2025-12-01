<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../Model/Forum.php');

class ForumController {
    
    // Lister tous les forums
    public function listForums() {
        $sql = "SELECT * FROM forums ORDER BY is_pinned DESC, created_at DESC";
        $db = ConfigForum::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Chercher des forums par titre, contenu ou auteur
    public function searchForums($query) {
        $sql = "SELECT * FROM forums WHERE title LIKE :query OR content LIKE :query OR author LIKE :query ORDER BY created_at DESC";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $searchTerm = '%' . $query . '%';
            $stmt->bindValue(':query', $searchTerm);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Filtrer par catégorie
    public function filterByCategory($category) {
        $sql = "SELECT * FROM forums WHERE category = :category ORDER BY is_pinned DESC, created_at DESC";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':category', $category);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Afficher un forum spécifique avec compteur de vues
    public function showForum($id) {
        // Incrémenter les vues
        $sqlUpdate = "UPDATE forums SET views = views + 1 WHERE id = :id";
        $db = ConfigForum::getConnexion();
        try {
            $stmtUpdate = $db->prepare($sqlUpdate);
            $stmtUpdate->bindValue(':id', $id);
            $stmtUpdate->execute();
        } catch (Exception $e) {
            // Ignorer l'erreur si l'update échoue
        }
        
        // Récupérer le forum
        $sql = "SELECT * FROM forums WHERE id = :id";
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            $forum = $query->fetch();
            return $forum;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // Ajouter un forum
    public function addForum(Forum $forum) {
        $sql = "INSERT INTO forums (title, category, author, content, is_pinned, is_locked) 
                VALUES (:title, :category, :author, :content, :is_pinned, :is_locked)";
        $db = ConfigForum::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $forum->getTitle(),
                'category' => $forum->getCategory(),
                'author' => $forum->getAuthor(),
                'content' => $forum->getContent(),
                'is_pinned' => $forum->getIsPinned() ? 1 : 0,
                'is_locked' => $forum->getIsLocked() ? 1 : 0
            ]);
            return $db->lastInsertId();
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    // Modifier un forum
    public function updateForum(Forum $forum, $id) {
        try {
            $db = ConfigForum::getConnexion();
            $query = $db->prepare(
                'UPDATE forums SET 
                    title = :title,
                    category = :category,
                    author = :author,
                    content = :content,
                    is_pinned = :is_pinned,
                    is_locked = :is_locked
                WHERE id = :id'
            );
            $query->execute([
                'id' => $id,
                'title' => $forum->getTitle(),
                'category' => $forum->getCategory(),
                'author' => $forum->getAuthor(),
                'content' => $forum->getContent(),
                'is_pinned' => $forum->getIsPinned() ? 1 : 0,
                'is_locked' => $forum->getIsLocked() ? 1 : 0
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    // Supprimer un forum
    public function deleteForum($id) {
        $sql = "DELETE FROM forums WHERE id = :id";
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

    // Compter le nombre de réponses pour un forum
    public function countReplies($forum_id) {
        $sql = "SELECT COUNT(*) as count FROM replies WHERE forum_id = :forum_id";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':forum_id', $forum_id);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    // Obtenir la dernière date de réponse
    public function getLastReplyDate($forum_id) {
        $sql = "SELECT MAX(created_at) as last_date FROM replies WHERE forum_id = :forum_id";
        $db = ConfigForum::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':forum_id', $forum_id);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['last_date'];
        } catch (Exception $e) {
            return null;
        }
    }

    // Épingler/désépingler un forum
    public function togglePin($id) {
        $sql = "UPDATE forums SET is_pinned = NOT is_pinned WHERE id = :id";
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

    // Verrouiller/déverrouiller un forum
    public function toggleLock($id) {
        $sql = "UPDATE forums SET is_locked = NOT is_locked WHERE id = :id";
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
}
?>