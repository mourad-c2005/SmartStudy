<?php
/**
 * UserController - Gère les pages publiques (accueil, formations, etc.)
 */

require_once __DIR__ . "/../models/formation.php";
require_once __DIR__ . "/../models/Category.php";
require_once __DIR__ . "/../models/Section.php";
require_once __DIR__ . "/../models/Panier.php";

class UserController {

    public function home() {
        // Get user info from session
        $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
        $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
        $userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Get user profile picture from database if logged in
        $profilePicture = null;
        if ($userId) {
            try {
                require_once __DIR__ . "/../config/Database.php";
                $pdo = Database::connect();
                $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
                if ($tableExists) {
                    $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($userData) {
                        $profilePicture = $userData['profile_picture'];
                    }
                }
            } catch (PDOException $e) {
                // Ignore errors
            }
        }

        // Generate initial from name if no profile picture
        $userInitial = 'U';
        if ($userName) {
            $nameParts = explode(' ', $userName);
            if (count($nameParts) > 0) {
                $userInitial = strtoupper(substr($nameParts[0], 0, 1));
                if (count($nameParts) > 1) {
                    $userInitial .= strtoupper(substr($nameParts[1], 0, 1));
                }
            }
        }

        // Get cart count
        $cartCount = 0;
        if ($userId) {
            $cartItems = Panier::getItems();
            $cartCount = count($cartItems);
        }

        // Role display names
        $roleNames = [
            'etudiant' => 'Étudiant',
            'enseignant' => 'Enseignant',
            'admin' => 'Administrateur'
        ];
        $displayRole = isset($roleNames[$userRole]) ? $roleNames[$userRole] : 'Utilisateur';

        require __DIR__ . "/../views/user/home.php";
    }

    public function formations() {
        // Get sections and categories for filters
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

        // Get selected filters
        $selectedSection = isset($_GET['section']) ? intval($_GET['section']) : 0;
        $selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;

        // Get formations
        $formations = [];
        if ($selectedCategory > 0) {
            $formations = Formation::getByCategory($selectedCategory);
        } else {
            $formations = Formation::getAll();
        }

        require __DIR__ . "/../views/user/formations.php";
    }

    public function formationDetail() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id <= 0) {
            header('Location: index.php?controller=user&action=formations');
            exit;
        }

        // Get formation details
        $formation = Formation::getById($id);

        if (!$formation) {
            header('Location: index.php?controller=user&action=formations&error=not_found');
            exit;
        }

        // Get category and section info
        $category = null;
        $section = null;
        if (isset($formation['id_categorie'])) {
            $categories = Category::getBySection(0);
            foreach (Section::getAll() as $sec) {
                $cats = Category::getBySection($sec['id']);
                foreach ($cats as $cat) {
                    if ($cat['id'] == $formation['id_categorie']) {
                        $category = $cat;
                        $section = $sec;
                        break 2;
                    }
                }
            }
        }

        // Handle add to cart
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?controller=auth&action=login');
                exit;
            } else {
                error_log("UserController::formationDetail - Attempting to add formation ID: $id for user ID: " . $_SESSION['user_id']);
                $result = Panier::addFormation($id);
                error_log("UserController::formationDetail - addFormation returned: " . ($result ? 'TRUE' : 'FALSE'));
                
                if ($result) {
                    header("Location: index.php?controller=panier&action=show&success=added");
                    exit;
                } else {
                    header("Location: index.php?controller=panier&action=show&error=already_in_cart");
                    exit;
                }
            }
        }
        
        // Initialize cart messages for display (if not redirected)
        $cartMessage = '';
        $cartError = '';

        // Parse URLs - can be JSON array or single URL
        $urls = [];
        if (!empty($formation['url'])) {
            $decoded = json_decode($formation['url'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $urls = $decoded;
            } else {
                $urls = [$formation['url']];
            }
        }

        require __DIR__ . "/../views/user/formation_detail.php";
    }
}

