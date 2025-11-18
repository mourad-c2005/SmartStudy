<?php
// app/controllers/CoursController.php
class CoursController extends Controller {
    private $coursModel;
    private $chapitreModel;

    public function __construct() {
        $this->coursModel = $this->model('Cours');
        $this->chapitreModel = $this->model('Chapitre');
    }

    // lister cours d'un chapitre: /Cours/index/{id_chapitre}
    public function index($id_chapitre = null) {
        if (!$id_chapitre) header('Location: ' . URLROOT);
        $data['chapitre'] = $this->chapitreModel->getById($id_chapitre);
        $data['cours'] = $this->coursModel->getAllByChapitre($id_chapitre);
        $this->view('layout');
        $this->view('cours/index', $data);
    }

    public function show($id) {
        $data['cours'] = $this->coursModel->getById($id);
        $this->view('layout');
        $this->view('cours/show', $data);
    }

    public function add($id_chapitre = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->coursModel->add($_POST['id_chapitre'], $_POST['titre'], $_POST['contenu'], $_POST['lien_video']);
            header('Location: ' . URLROOT . '/Cours/index/' . $_POST['id_chapitre']);
            exit;
        }
        $data['chapitre'] = $this->chapitreModel->getById($id_chapitre);
        $this->view('layout');
        $this->view('cours/add', $data);
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->coursModel->update($id, $_POST['titre'], $_POST['contenu'], $_POST['lien_video']);
            header('Location: ' . URLROOT);
            exit;
        }
        $data['cours'] = $this->coursModel->getById($id);
        $this->view('layout');
        $this->view('cours/edit', $data);
    }

    public function delete($id) {
        $cours = $this->coursModel->getById($id);
        $this->coursModel->delete($id);
        header('Location: ' . URLROOT . '/Cours/index/' . ($cours['id_chapitre'] ?? ''));
        exit;
    }
}
