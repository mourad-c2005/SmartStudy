<?php
// app/controllers/HomeController.php

class HomeController extends Controller
{
    private $matiereModel;

    public function __construct() {
        // Charger le modèle Matiere
        $this->matiereModel = $this->model('Matiere');
    }

    public function index() {
        // Récupération des matières
        $matieres = $this->matiereModel->getAll();

        // Préparation des données
        $data = [
            'title'     => 'Plans de Révision',
            'matieres'  => $matieres
        ];

        // Chargement du layout + vue
        $this->view('layout');           // header / menu global
        $this->view('home/index', $data); // page principale
    }
}
