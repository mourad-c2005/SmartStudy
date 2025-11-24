<?php
/**
 * AdminSectionController - Gère les sections (CRUD)
 */

require_once __DIR__ . "/../models/Section.php";
require_once __DIR__ . "/../config/Database.php";

class AdminSectionController {

    public function list() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $sections = Section::getAll();
        $success = isset($_GET['success']) ? $_GET['success'] : '';
        $error_msg = isset($_GET['error']) ? $_GET['error'] : '';

        require __DIR__ . "/../views/admin/sections/list.php";
    }

    public function add() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $message = '';
        $error = '';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
            $nom = trim($_POST['nom']);
            
            if (!empty($nom)) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("INSERT INTO `sections` (`nom`) VALUES (?)");
                    $stmt->execute([$nom]);
                    
                    header('Location: index.php?controller=admin_sections&action=list&success=added');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            } else {
                $error = "Le nom de la section est requis.";
            }
        }

        require __DIR__ . "/../views/admin/add_section.php";
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
        $section = null;

        if ($id > 0) {
            try {
                $pdo = Database::connect();
                $stmt = $pdo->prepare("SELECT * FROM sections WHERE id = ?");
                $stmt->execute([$id]);
                $section = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $error = "Erreur : " . $e->getMessage();
            }
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom']) && isset($_POST['id'])) {
            $nom = trim($_POST['nom']);
            $id = intval($_POST['id']);
            
            if (!empty($nom) && $id > 0) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("UPDATE sections SET nom = ? WHERE id = ?");
                    $stmt->execute([$nom, $id]);
                    
                    header('Location: index.php?controller=admin_sections&action=list&success=updated');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error = "Le nom de la section est requis.";
            }
        }

        require __DIR__ . "/../views/admin/edit_section.php";
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
                
                // Check if section has categories
                require_once __DIR__ . "/../models/Category.php";
                $categories = Category::getBySection($id);
                
                if (count($categories) > 0) {
                    header('Location: index.php?controller=admin_sections&action=list&error=has_categories');
                    exit;
                }
                
                $stmt = $pdo->prepare("DELETE FROM sections WHERE id = ?");
                $stmt->execute([$id]);
                
                header('Location: index.php?controller=admin_sections&action=list&success=deleted');
                exit;
            } catch (PDOException $e) {
                header('Location: index.php?controller=admin_sections&action=list&error=' . urlencode($e->getMessage()));
                exit;
            }
        }

        header('Location: index.php?controller=admin_sections&action=list');
        exit;
    }
}

