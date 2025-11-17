<?php
require_once "models/Formation.php";

class FormationController {

    public function listFormations() {

        if (!isset($_GET["id"])) {
            die("Catégorie non trouvée");
        }

        $id = intval($_GET["id"]);  // id de la catégorie

        $formations = Formation::getByCategory($id);

        require "views/front/formations.php";
    }
}
