<?php
class QuizController extends Controller {
    private $quizModel;
    private $coursModel;
    private $attemptModel;

    public function __construct() {
        $this->quizModel = $this->model('Quiz');
        $this->coursModel = $this->model('Cours');
        $this->attemptModel = $this->model('Attempt');
    }

    public function index($id_cours = null) {
    if (!$id_cours) {
        header('Location: ' . URLROOT);
        exit;
    }

    $cours = $this->coursModel->getById($id_cours);

    if (!$cours) {
        // Si le cours n'existe pas, rediriger vers la page d'accueil ou afficher un message
        header('Location: ' . URLROOT);
        exit;
    }

    // Maintenant que $cours existe, on peut récupérer les questions
    $questions = $this->quizModel->getAllByCours($id_cours);

    $data = [
        'cours' => $cours,
        'questions' => $questions ? array_slice($questions, 0, 20) : []
    ];

    $this->view('layout');
    $this->view('quiz/index', $data);
}
 
    public function submit($id_cours = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Méthode non autorisée";
            exit;
        }

        $student_name = trim($_POST['student_name'] ?? '');
        if (!$student_name) $student_name = "Invité";

        $answers = $_POST['answers'] ?? [];
        $time_seconds = intval($_POST['time_seconds'] ?? 0);

        $questions = $this->quizModel->getAllByCours($id_cours);
        $total = count($questions);
        $score = 0;

        $correctMap = [];
        foreach ($questions as $q) {
            $correctMap[$q['id']] = intval($q['correcte']);
        }

        foreach ($answers as $ans) {
            $qid = intval($ans['question_id']);
            $sel = $ans['selected'] ?? null;

            if ($sel !== null && isset($correctMap[$qid]) && intval($sel) === $correctMap[$qid]) {
                $score++;
            }
        }

        // 📌 Enregistre la tentative
        $attemptId = $this->attemptModel->create(
            $id_cours,
            $student_name,
            $score,
            $total,
            $time_seconds
        );

        // 📌 Enregistre les réponses
        foreach ($answers as $ans) {
            $qid = intval($ans['question_id']);
            $sel = $ans['selected'] ?? null;
            $this->attemptModel->addAnswer($attemptId, $qid, $sel);
        }

        header('Location: ' . URLROOT . "/Quiz/result/$attemptId");
        exit;
    }

    
public function result($attempt_id) {

    if (!$attempt_id) die("ID invalide");

    // ✔ Utilisation correcte
    $attempt = $this->attemptModel->getById($attempt_id);

    if (!$attempt) {
        die("Tentative introuvable.");
    }

    $this->view('layout');
    $this->view('quiz/result', ['attempt' => $attempt]);
}


    public function answers($attempt_id) {
        $answers = $this->attemptModel->getAnswers($attempt_id);
        $this->view('layout');
        $this->view('admin/quiz_answers', ['answers' => $answers]);
    }
}
