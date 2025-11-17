<?php
require_once "models/Panier.php";
require_once "models/Formation.php";

class PanierController {

    public function add() {
        $id = $_GET['id'];

        Panier::addFormation($id);

        header("Location: index.php?page=cart");
    }

    public function show() {
        $items = Panier::getItems();
        require "views/front/panier.php";
    }

    public function pay() {
        Panier::clear();
        require "views/front/pay_success.php";
    }
}
