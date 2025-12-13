<?php
// controller/AdminController.php
class AdminController extends Controller {
    private $coursModel;
    private $chapitreModel;
    private $quizModel;
    private $attemptModel;

    public function __construct() {
        $this->coursModel = $this->model('Cours');
        $this->chapitreModel = $this->model('Chapitre');
        $this->quizModel = $this->model('Quiz');
        $this->attemptModel = $this->model('Attempt');
    }

    // Affiche la page d'accueil admin (tentatives par défaut)
    public function index() {
        $data['attempts'] = $this->attemptModel->getAllAttempts();
        $this->view('admin/attempts', $data);
    }

    // Affiche tous les cours avec actions
    public function manageCourses() {
    // Use the new method that includes chapitre titles
    $cours = $this->coursModel->getAllWithChapitre();
    $data = ['cours' => $cours];
    $this->view('admin/manage_courses', $data);
}


    // Ajouter un cours
    public function addCourse($chapitreId = null) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? ''; // Changed from description
            $lien_video = $_POST['lien_video'] ?? ''; // Changed from video
            
            // Note: You need to get chapitreId from form if not in URL
            $id_chapitre = $_POST['id_chapitre'] ?? $chapitreId;

            $this->coursModel->add($id_chapitre, $titre, $contenu, $lien_video);
            header('Location: ' . URLROOT . '/Admin/manageCourses');
            exit;
        }
        $data = ['chapitreId' => $chapitreId];
        $this->view('admin/add_course', $data);
    }

    // Modifier un cours
    public function editCourse($id) {
        $cours = $this->coursModel->getById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';

            $this->coursModel->update($id, $titre, $contenu, $lien_video);
            header('Location: ' . URLROOT . '/Admin/manageCourses');
            exit;
        }
        $data = ['cours' => $cours];
        $this->view('admin/edit_course', $data);
    }

    // Supprimer un cours
    public function deleteCourse($id) {
        $this->coursModel->delete($id);
        header('Location: ' . URLROOT . '/Admin/manageCourses');
        exit;
    }

    // Rest of your methods remain the same...
    // Gestion des chapitres
    public function manageChapitres() {
        $chapitres = $this->chapitreModel->getAll();
        $data = ['chapitres' => $chapitres];
        $this->view('admin/manage_chapitres', $data);
    }

    public function addChapitre() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $this->chapitreModel->addChapitre($titre);
            header('Location: ' . URLROOT . '/Admin/manageChapitres');
            exit;
        }
        $this->view('admin/add_chapitre');
    }

    public function editChapitre($id) {
        $chapitre = $this->chapitreModel->getById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $this->chapitreModel->updateChapitre($id, $titre);
            header('Location: ' . URLROOT . '/Admin/manageChapitres');
            exit;
        }
        $data = ['chapitre' => $chapitre];
        $this->view('admin/edit_chapitre', $data);
    }

    public function deleteChapitre($id) {
        $this->chapitreModel->deleteChapitre($id);
        header('Location: ' . URLROOT . '/Admin/manageChapitres');
        exit;
    }

    // Pages supplémentaires d'administration
    public function attempts() {
        $data['attempts'] = $this->attemptModel->getAllAttempts();
        $this->view('admin/attempts', $data);
    }

    // In AdminController.php, update the results() method:
public function results() {
    // Quick temporary fix - use getAllAttempts if it exists
    $results = $this->attemptModel->getAllAttempts();
    
    // If getAllAttempts() doesn't exist or returns null, set to empty array
    if (!$results) {
        $results = [];
    }
    
    $data = ['results' => $results];
    $this->view('admin/results', $data);
}
    public function answers() {
        $this->view('admin/answers');
    }

    public function quiz_answers() {
        $this->view('admin/quiz_answers');
    }

    public function access() {
        $this->view('admin/access');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Vérifier les identifiants
            if ($username === 'admin' && $password === 'admin123') {
                $_SESSION['admin_logged'] = true;
                header('Location: ' . URLROOT . '/Admin/index');
                exit;
            } else {
                $data['error'] = 'Identifiants incorrects';
                $this->view('admin/login', $data);
            }
        } else {
            $this->view('admin/login');
        }
    }

    public function logout() {
        unset($_SESSION['admin_logged']);
        session_destroy();
        header('Location: ' . URLROOT . '/Admin/login');
        exit;
    }
}