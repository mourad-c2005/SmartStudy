<?php
require_once __DIR__ . "/../models/Category.php";
require_once __DIR__ . "/../models/Section.php";
require_once __DIR__ . "/../config/Database.php";

class CategoryController {

    /**
     * List all categories
     */
    public function listCategories() {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT c.*, s.nom as section_nom FROM categorie c LEFT JOIN sections s ON c.section_id = s.id ORDER BY c.id DESC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . "/../views/admin/categories_list.php";
    }

    /**
     * Show category form (add or edit)
     */
    public function showCategory($id = null) {
        $category = null;
        $sections = Section::getAll();
        
        if ($id) {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("SELECT * FROM categorie WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        require __DIR__ . "/../views/admin/category_form.php";
    }

    /**
     * Add a new category
     */
    public function addCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['section_id'])) {
            $nom = trim($_POST['nom']);
            $section_id = intval($_POST['section_id']);
            
            if (!empty($nom) && $section_id > 0) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("INSERT INTO categorie (nom, section_id) VALUES (?, ?)");
                    $stmt->execute([$nom, $section_id]);
                    header('Location: admin_categories_controller.php?action=list&success=1');
                    exit;
                } catch (PDOException $e) {
                    header('Location: admin_categories_controller.php?action=list&error=' . urlencode($e->getMessage()));
                    exit;
                }
            } else {
                header('Location: admin_categories_controller.php?action=list&error=' . urlencode("Tous les champs sont requis"));
                exit;
            }
        }
        $this->showCategory();
    }

    /**
     * Update a category
     */
    public function updateCategory($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['section_id'])) {
            $nom = trim($_POST['nom']);
            $section_id = intval($_POST['section_id']);
            
            if (!empty($nom) && $section_id > 0) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("UPDATE categorie SET nom = ?, section_id = ? WHERE id = ?");
                    $stmt->execute([$nom, $section_id, $id]);
                    header('Location: admin_categories_controller.php?action=list&success=1');
                    exit;
                } catch (PDOException $e) {
                    header('Location: admin_categories_controller.php?action=list&error=' . urlencode($e->getMessage()));
                    exit;
                }
            } else {
                header('Location: admin_categories_controller.php?action=list&error=' . urlencode("Tous les champs sont requis"));
                exit;
            }
        }
        $this->showCategory($id);
    }

    /**
     * Delete a category
     */
    public function deleteCategory($id) {
        try {
            $pdo = Database::connect();
            
            // Check if category has formations
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM formation WHERE id_categorie = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                header('Location: admin_categories_controller.php?action=list&error=' . urlencode("Impossible de supprimer : cette catégorie contient des formations"));
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM categorie WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: admin_categories_controller.php?action=list&success=1');
            exit;
        } catch (PDOException $e) {
            header('Location: admin_categories_controller.php?action=list&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
