<?php
// app/controllers/HomeController.php
class HomeController extends Controller {
    private $matiereModel;
    public function __construct() {
        $this->matiereModel = $this->model('Matiere');
    }

    public function index() {
        $data['matieres'] = $this->matiereModel->getAll();
        $this->view('layout');
        $this->view('home/index', $data);
    }
}
