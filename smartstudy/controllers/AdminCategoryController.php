<?php
/**
 * AdminCategoryController - Gère les catégories (CRUD)
 */

require_once __DIR__ . "/../models/Category.php";
require_once __DIR__ . "/../models/Section.php";
require_once __DIR__ . "/../config/Database.php";

class AdminCategoryController {

    public function list() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $sections = Section::getAll();
        $categories = [];
        foreach ($sections as $section) {
            $cats = Category::getBySection($section['id']);
            foreach ($cats as $cat) {
                $cat['section_nom'] = $section['nom'];
                $categories[] = $cat;
            }
        }

        require __DIR__ . "/../views/admin/categories/list.php";
    }

    public function add() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $message = '';
        $error = '';
        $sections = Section::getAll();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['section_id'])) {
            $nom = trim($_POST['nom']);
            $section_id = intval($_POST['section_id']);
            
            if (!empty($nom) && $section_id > 0) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("INSERT INTO `categorie` (`nom`, `section_id`) VALUES (?, ?)");
                    $stmt->execute([$nom, $section_id]);
                    
                    header('Location: index.php?controller=admin_categories&action=list&success=added');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            } else {
                $error = "Le nom et la section sont requis.";
            }
        }

        require __DIR__ . "/../views/admin/add_category.php";
    }

    public function edit() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $message = '';
        $error = '';
        $category = null;
        $sections = Section::getAll();

        if ($id > 0) {
            try {
                $pdo = Database::connect();
                $stmt = $pdo->prepare("SELECT * FROM categorie WHERE id = ?");
                $stmt->execute([$id]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $error = "Erreur : " . $e->getMessage();
            }
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['section_id']) && isset($_POST['id'])) {
            $nom = trim($_POST['nom']);
            $section_id = intval($_POST['section_id']);
            $id = intval($_POST['id']);
            
            if (!empty($nom) && $section_id > 0 && $id > 0) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("UPDATE categorie SET nom = ?, section_id = ? WHERE id = ?");
                    $stmt->execute([$nom, $section_id, $id]);
                    
                    header('Location: index.php?controller=admin_categories&action=list&success=updated');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error = "Tous les champs sont requis.";
            }
        }

        require __DIR__ . "/../views/admin/edit_category.php";
    }

    public function delete() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id > 0) {
            try {
                $pdo = Database::connect();
                
                // Check if category has formations
                require_once __DIR__ . "/../models/formation.php";
                $formations = Formation::getByCategory($id);
                
                if (count($formations) > 0) {
                    header('Location: index.php?controller=admin_categories&action=list&error=has_formations');
                    exit;
                }
                
                $stmt = $pdo->prepare("DELETE FROM categorie WHERE id = ?");
                $stmt->execute([$id]);
                
                header('Location: index.php?controller=admin_categories&action=list&success=deleted');
                exit;
            } catch (PDOException $e) {
                header('Location: index.php?controller=admin_categories&action=list&error=' . urlencode($e->getMessage()));
                exit;
            }
        }

        header('Location: index.php?controller=admin_categories&action=list');
        exit;
    }
}

