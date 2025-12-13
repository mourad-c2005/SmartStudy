<?php
// app/controllers/MatiereController.php
class MatiereController extends Controller {
    private $matiereModel;

    public function __construct() {
        $this->matiereModel = $this->model('Matiere');
    }

    public function index() {
        $data['matieres'] = $this->matiereModel->getAll();
        $this->view('layout'); // header
        $this->view('home/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->matiereModel->add($_POST['nom'], $_POST['description']);
            header('Location: ' . URLROOT);
            exit;
        }
        $this->view('layout');
        $this->view('matieres/add');
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->matiereModel->update($id, $_POST['nom'], $_POST['description']);
            header('Location: ' . URLROOT);
            exit;
        }
        $data['matiere'] = $this->matiereModel->getById($id);
        $this->view('layout');
        $this->view('matieres/edit', $data);
    }

    public function delete($id) {
        $this->matiereModel->delete($id);
       ('Location: ' . URLROOT);
        exit;
    }
}
