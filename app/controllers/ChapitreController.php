<?php
// app/controllers/ChapitreController.php
class ChapitreController extends Controller {
    private $chapitreModel;
    private $matiereModel;

    public function __construct() {
        $this->chapitreModel = $this->model('Chapitre');
        $this->matiereModel = $this->model('Matiere');
    }

    // afficher chapitres d'une matiere: /Chapitre/index/{id_matiere}
    public function index($id_matiere = null) {
        if (!$id_matiere) header('Location: ' . URLROOT);
        $data['matieres'] = $this->matiereModel->getAll();
        $data['chapitres'] = $this->chapitreModel->getAllByMatiere($id_matiere);
        $data['matiere'] = $this->matiereModel->getById($id_matiere);
        $this->view('layout');
        $this->view('chapitres/index', $data);
    }

    public function add($id_matiere = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->chapitreModel->add($_POST['id_matiere'], $_POST['titre']);
            header('Location: ' . URLROOT . '/Chapitre/index/' . $_POST['id_matiere']);
            exit;
        }
        $data['matiere'] = $this->matiereModel->getById($id_matiere);
        $this->view('layout');
        $this->view('chapitres/add', $data);
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->chapitreModel->update($id, $_POST['titre']);
            header('Location: ' . URLROOT);
            exit;
        }
        $data['chapitre'] = $this->chapitreModel->getById($id);
        $this->view('layout');
        $this->view('chapitres/edit', $data);
    }

    public function delete($id) {
        // récupérer id_matiere pour redirection (optionnel)
        $chap = $this->chapitreModel->getById($id);
        $id_matiere = $chap['id_matiere'] ?? null;
        $this->chapitreModel->delete($id);
        if ($id_matiere) header('Location: ' . URLROOT . '/Chapitre/index/' . $id_matiere);
        else header('Location: ' . URLROOT);
        exit;
    }
}
