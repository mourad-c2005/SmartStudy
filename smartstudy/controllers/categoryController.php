<?php
require_once "models/Category.php";

class CategoryController {

    public function listCategories() {
        $sectionId = $_GET['id'];
        $categories = Category::getBySection($sectionId);

        require "views/front/categories.php";
    }
}
