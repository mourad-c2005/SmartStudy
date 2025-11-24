<?php
/**
 * AdminFormationController - Gère les formations (CRUD)
 */

require_once __DIR__ . "/../models/formation.php";
require_once __DIR__ . "/../models/Category.php";
require_once __DIR__ . "/../models/Section.php";
require_once __DIR__ . "/../config/Database.php";

class AdminFormationController {

    public function list() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Get all formations
        $formations = Formation::getAll();

        // Get categories for display
        $categoriesMap = [];
        $sectionsMap = [];
        $sections = Section::getAll();

        foreach ($sections as $section) {
            $sectionsMap[$section['id']] = $section['nom'];
            $cats = Category::getBySection($section['id']);
            foreach ($cats as $cat) {
                $categoriesMap[$cat['id']] = $cat['nom'];
            }
        }

        $success = isset($_GET['success']) ? $_GET['success'] : '';
        $error_msg = isset($_GET['error']) ? $_GET['error'] : '';

        require __DIR__ . "/../views/admin/formations/list.php";
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
        $allCategories = [];
        foreach ($sections as $section) {
            $categories = Category::getBySection($section['id']);
            foreach ($categories as $cat) {
                $cat['section_id'] = $section['id'];
                $cat['section_nom'] = $section['nom'];
                $allCategories[] = $cat;
            }
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre']) && isset($_POST['id_categorie'])) {
            $titre = trim($_POST['titre']);
            $prix = floatval($_POST['prix'] ?? 0);
            $id_categorie = intval($_POST['id_categorie']);
            
            // Collect URLs
            $urls = [];
            if (isset($_POST['urls']) && is_array($_POST['urls'])) {
                foreach ($_POST['urls'] as $url) {
                    $url = trim($url);
                    if (!empty($url)) {
                        $urls[] = $url;
                    }
                }
            }
            
            // Store all URLs as JSON in the url field
            $urlsJson = !empty($urls) ? json_encode($urls) : '';
            $urlToStore = !empty($urls) ? (count($urls) > 1 ? $urlsJson : $urls[0]) : '';
            
            if (!empty($titre) && $id_categorie > 0) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("INSERT INTO formation (titre, url, prix, id_categorie) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$titre, $urlToStore, $prix, $id_categorie]);
                    
                    header('Location: index.php?controller=admin_formations&action=list&success=added');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de l'ajout : " . $e->getMessage();
                }
            } else {
                $error = "Le titre et la catégorie sont requis.";
            }
        }

        require __DIR__ . "/../views/admin/add_formation.php";
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
        $formation = null;
        $sections = Section::getAll();
        $allCategories = [];
        foreach ($sections as $section) {
            $categories = Category::getBySection($section['id']);
            foreach ($categories as $cat) {
                $cat['section_id'] = $section['id'];
                $cat['section_nom'] = $section['nom'];
                $allCategories[] = $cat;
            }
        }

        if ($id > 0) {
            $formation = Formation::getById($id);
            if (!$formation) {
                header('Location: index.php?controller=admin_formations&action=list&error=not_found');
                exit;
            }
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre']) && isset($_POST['id_categorie']) && isset($_POST['id'])) {
            $titre = trim($_POST['titre']);
            $prix = floatval($_POST['prix'] ?? 0);
            $id_categorie = intval($_POST['id_categorie']);
            $id = intval($_POST['id']);
            
            // Collect URLs
            $urls = [];
            if (isset($_POST['urls']) && is_array($_POST['urls'])) {
                foreach ($_POST['urls'] as $url) {
                    $url = trim($url);
                    if (!empty($url)) {
                        $urls[] = $url;
                    }
                }
            }
            
            $urlsJson = !empty($urls) ? json_encode($urls) : '';
            $urlToStore = !empty($urls) ? (count($urls) > 1 ? $urlsJson : $urls[0]) : '';
            
            if (!empty($titre) && $id_categorie > 0 && $id > 0) {
                try {
                    $pdo = Database::connect();
                    $stmt = $pdo->prepare("UPDATE formation SET titre = ?, url = ?, prix = ?, id_categorie = ? WHERE id_formation = ?");
                    $stmt->execute([$titre, $urlToStore, $prix, $id_categorie, $id]);
                    
                    header('Location: index.php?controller=admin_formations&action=list&success=updated');
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur lors de la modification : " . $e->getMessage();
                }
            } else {
                $error = "Tous les champs sont requis.";
            }
        }

        require __DIR__ . "/../views/admin/add_formation.php";
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
                $stmt = $pdo->prepare("DELETE FROM formation WHERE id_formation = ?");
                $stmt->execute([$id]);
                
                header('Location: index.php?controller=admin_formations&action=list&success=deleted');
                exit;
            } catch (PDOException $e) {
                header('Location: index.php?controller=admin_formations&action=list&error=' . urlencode($e->getMessage()));
                exit;
            }
        }

        header('Location: index.php?controller=admin_formations&action=list');
        exit;
    }
}

