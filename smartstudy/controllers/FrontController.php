<?php

require_once "controllers/SectionController.php";
require_once "controllers/CategoryController.php";
require_once "controllers/FormationController.php";
require_once "controllers/PanierController.php";

class FrontController {

    public function handleRequest() {

        $page = $_GET['page'] ?? 'sections';

        switch ($page) {

            case 'sections':
                (new SectionController())->listSections();
                break;

            case 'categories':
                (new CategoryController())->listCategories();
                break;

            case 'formations':
                (new FormationController())->listFormations();
                break;

            case 'addToCart':
                (new PanierController())->add();
                break;

            case 'cart':
                (new PanierController())->show();
                break;

            case 'pay':
                (new PanierController())->pay();
                break;

            default:
                echo "<h1>404 - Page introuvable</h1>";
        }
    }
}
