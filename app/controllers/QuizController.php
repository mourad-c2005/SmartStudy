<?php
// app/controllers/QuizController.php
class QuizController extends Controller {
    private $quizModel;
    private $chapitreModel;

    public function __construct() {
        $this->quizModel = $this->model('Quiz');
        $this->chapitreModel = $this->model('Chapitre');
    }

    // /Quiz/index/{id_chapitre}
    public function index($id_chapitre = null) {
        if (!$id_chapitre) header('Location: ' . URLROOT);
        $data['chapitre'] = $this->chapitreModel->getById($id_chapitre);
        $data['questions'] = $this->quizModel->getAllByChapitre($id_chapitre);
        $this->view('layout');
        $this->view('quiz/index', $data);
    }

    public function add($id_chapitre = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->quizModel->add($_POST['id_chapitre'], $_POST['question'], $_POST['rep1'], $_POST['rep2'], $_POST['rep3'], $_POST['rep4'], $_POST['correcte']);
            header('Location: ' . URLROOT . '/Quiz/index/' . $_POST['id_chapitre']);
            exit;
        }
        $data['chapitre'] = $this->chapitreModel->getById($id_chapitre);
        $this->view('layout');
        $this->view('quiz/add', $data); // you can create add view if needed
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->quizModel->update($id, $_POST['question'], $_POST['rep1'], $_POST['rep2'], $_POST['rep3'], $_POST['rep4'], $_POST['correcte']);
            header('Location: ' . URLROOT);
            exit;
        }
        $data['quiz'] = $this->quizModel->getById($id);
        $this->view('layout');
        // create view if needed
    }

    public function delete($id) {
        // find chapitre for redirect
        $q = $this->quizModel->getById($id);
        $this->quizModel->delete($id);
        header('Location: ' . URLROOT . '/Quiz/index/' . ($q['id_chapitre'] ?? ''));
        exit;
    }
}
