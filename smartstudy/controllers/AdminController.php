<?php
/**
 * AdminController - Gère le dashboard admin
 */

require_once __DIR__ . "/../models/Section.php";
require_once __DIR__ . "/../models/Category.php";
require_once __DIR__ . "/../models/formation.php";

class AdminController {

    public function dashboard() {
        // Check if admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        // Get statistics
        $sections = Section::getAll();
        $totalSections = count($sections);
        
        $totalCategories = 0;
        foreach ($sections as $section) {
            $categories = Category::getBySection($section['id']);
            $totalCategories += count($categories);
        }
        
        $formations = Formation::getAll();
        $totalFormations = count($formations);

        require __DIR__ . "/../views/admin/dashboard.php";
    }
}

