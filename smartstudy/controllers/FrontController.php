<?php
/**
 * Front Controller - Route toutes les requêtes vers les bons contrôleurs
 */

require_once __DIR__ . "/AuthController.php";
require_once __DIR__ . "/UserController.php";
require_once __DIR__ . "/AdminController.php";
require_once __DIR__ . "/AdminSectionController.php";
require_once __DIR__ . "/AdminCategoryController.php";
require_once __DIR__ . "/AdminFormationController.php";
require_once __DIR__ . "/PanierController.php";

class FrontController {

    public function handleRequest() {
        // Récupérer l'action depuis l'URL
        $action = $_GET['action'] ?? 'home';
        $controller = $_GET['controller'] ?? 'user';

        // Router vers le bon contrôleur
        switch ($controller) {
            case 'auth':
                $this->handleAuth($action);
                break;
            
            case 'admin':
                $this->handleAdmin($action);
                break;
            
            case 'admin_sections':
                $this->handleAdminSections($action);
                break;
            
            case 'admin_categories':
                $this->handleAdminCategories($action);
                break;
            
            case 'admin_formations':
                $this->handleAdminFormations($action);
                break;
            
            case 'panier':
                $this->handlePanier($action);
                break;
            
            case 'user':
            default:
                $this->handleUser($action);
                break;
        }
    }

    private function handleAuth($action) {
        $authController = new AuthController();
        
        switch ($action) {
            case 'login':
                $authController->login();
                break;
            case 'signup':
                $authController->signup();
                break;
            case 'logout':
                $authController->logout();
                break;
            default:
                $authController->showLogin();
                break;
        }
    }

    private function handleAdmin($action) {
        // Vérifier si admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $adminController = new AdminController();
        $adminSectionController = new AdminSectionController();
        $adminCategoryController = new AdminCategoryController();
        $adminFormationController = new AdminFormationController();

        switch ($action) {
            case 'dashboard':
                $adminController->dashboard();
                break;
            case 'sections':
                $adminSectionController->list();
                break;
            case 'sections_add':
                $adminSectionController->add();
                break;
            case 'sections_edit':
                $adminSectionController->edit();
                break;
            case 'sections_delete':
                $adminSectionController->delete();
                break;
            case 'categories':
                $adminCategoryController->list();
                break;
            case 'categories_add':
                $adminCategoryController->add();
                break;
            case 'categories_edit':
                $adminCategoryController->edit();
                break;
            case 'categories_delete':
                $adminCategoryController->delete();
                break;
            case 'formations':
                $adminFormationController->list();
                break;
            case 'formations_add':
                $adminFormationController->add();
                break;
            case 'formations_edit':
                $adminFormationController->edit();
                break;
            case 'formations_delete':
                $adminFormationController->delete();
                break;
            default:
                $adminController->dashboard();
                break;
        }
    }

    private function handleUser($action) {
        $userController = new UserController();

        switch ($action) {
            case 'home':
                $userController->home();
                break;
            case 'formations':
                $userController->formations();
                break;
            case 'formation_detail':
                $userController->formationDetail();
                break;
            default:
                $userController->home();
                break;
        }
    }

    private function handleAdminSections($action) {
        // Vérifier si admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $adminSectionController = new AdminSectionController();

        switch ($action) {
            case 'list':
                $adminSectionController->list();
                break;
            case 'add':
                $adminSectionController->add();
                break;
            case 'edit':
                $adminSectionController->edit();
                break;
            case 'delete':
                $adminSectionController->delete();
                break;
            default:
                $adminSectionController->list();
                break;
        }
    }

    private function handleAdminCategories($action) {
        // Vérifier si admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $adminCategoryController = new AdminCategoryController();

        switch ($action) {
            case 'list':
                $adminCategoryController->list();
                break;
            case 'add':
                $adminCategoryController->add();
                break;
            case 'edit':
                $adminCategoryController->edit();
                break;
            case 'delete':
                $adminCategoryController->delete();
                break;
            default:
                $adminCategoryController->list();
                break;
        }
    }

    private function handleAdminFormations($action) {
        // Vérifier si admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        $adminFormationController = new AdminFormationController();

        switch ($action) {
            case 'list':
                $adminFormationController->list();
                break;
            case 'add':
                $adminFormationController->add();
                break;
            case 'edit':
                $adminFormationController->edit();
                break;
            case 'delete':
                $adminFormationController->delete();
                break;
            default:
                $adminFormationController->list();
                break;
        }
    }

    private function handlePanier($action) {
        $panierController = new PanierController();

        switch ($action) {
            case 'add':
                $panierController->add();
                break;
            case 'show':
                $panierController->show();
                break;
            case 'remove':
                $panierController->remove();
                break;
            case 'clear':
                $panierController->clear();
                break;
            case 'checkout':
                $panierController->pay();
                break;
            default:
                $panierController->show();
                break;
        }
    }
}
