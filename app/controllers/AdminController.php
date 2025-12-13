<?php
// app/controllers/AdminController.php
class AdminController extends Controller {
    private $coursModel;
    private $chapitreModel;
    private $quizModel;

    public function __construct() {
        $this->coursModel = $this->model('Cours');
        $this->chapitreModel = $this->model('Chapitre');
        $this->quizModel = $this->model('Quiz');
    }

    // Affiche tous les cours avec actions
    public function manageCourses() {
        $cours = $this->coursModel->getAllCoursesWithChapitre();
        $data = ['cours' => $cours];
        $this->view('admin/manage_courses', $data);
    }

    // Ajouter un cours
    public function addCourse($chapitreId = null) {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $video = $_POST['video'] ?? '';

            $this->coursModel->addCourse($titre, $description, $video, $chapitreId);
            header('Location: '. URLROOT .'/Admin/manageCourses');
            exit;
        }
        $data = ['chapitreId' => $chapitreId];
        $this->view('admin/add_course', $data);
    }

    // Modifier un cours
    public function editCourse($id) {
        $cours = $this->coursModel->getById($id);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $titre = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $video = $_POST['video'] ?? '';

            $this->coursModel->updateCourse($id, $titre, $description, $video);
            header('Location: '. URLROOT .'/Admin/manageCourses');
            exit;
        }
        $data = ['cours' => $cours];
        $this->view('admin/edit_course', $data);
    }

    // Supprimer un cours
    public function deleteCourse($id) {
        $this->coursModel->deleteCourse($id);
        header('Location: '. URLROOT .'/Admin/manageCourses');
        exit;
    }

    // Même logique pour Chapitres
    public function manageChapitres() {
        $chapitres = $this->chapitreModel->getAll();
        $data = ['chapitres' => $chapitres];
        $this->view('admin/manage_chapitres', $data);
    }

    public function addChapitre() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $titre = $_POST['titre'] ?? '';
            $this->chapitreModel->addChapitre($titre);
            header('Location: '. URLROOT .'/Admin/manageChapitres');
            exit;
        }
        $this->view('admin/add_chapitre');
    }

    public function editChapitre($id) {
        $chapitre = $this->chapitreModel->getById($id);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $titre = $_POST['titre'] ?? '';
            $this->chapitreModel->updateChapitre($id, $titre);
            header('Location: '. URLROOT .'/Admin/manageChapitres');
            exit;
        }
        $data = ['chapitre' => $chapitre];
        $this->view('admin/edit_chapitre', $data);
    }

    public function deleteChapitre($id) {
        $this->chapitreModel->deleteChapitre($id);
        header('Location: '. URLROOT .'/Admin/manageChapitres');
        exit;
    }
}
class AdminController extends Controller {

    public function index() {
        // Par exemple, rediriger vers la liste des tentatives
        $attemptModel = $this->model('Attempt');
        $data['attempts'] = $attemptModel->getAllAttempts();
        $this->view('admin/attempts', $data);
    }}
