<?php
// app/controllers/QuizController.php

class QuizController extends Controller {
    private $quizModel;
    private $coursModel;
    private $attemptModel;

    public function __construct() {
        $this->quizModel = $this->model('Quiz');
        $this->coursModel = $this->model('Cours');
        $this->attemptModel = $this->model('Attempt');
    }

    // Affiche le quiz pour un cours (20 questions max)
    public function index($id_cours = null) {
        if (!$id_cours) {
            header('Location: ' . URLROOT);
            exit;
        }

        $data['cours'] = $this->coursModel->getById($id_cours);

        // récupérer les questions du cours
        $allQuestions = $this->quizModel->getAllByCours($id_cours);
        $data['questions'] = array_slice($allQuestions, 0, 20); // max 20 questions

        $this->view('layout');
        $this->view('quiz/index', $data);
    }

    // Soumission du quiz
    public function submit($id_cours = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            exit;
        }

        $student_name = trim($body['student_name'] ?? 'Invité');
        $answers = $body['answers'] ?? [];
        $time_seconds = intval($body['time_seconds'] ?? 0);
        $id = intval($id_cours);

        $questions = $this->quizModel->getAllByCours($id);
        $total = count($questions);
        $score = 0;

        $correctMap = [];
        foreach ($questions as $q) $correctMap[$q['id']] = intval($q['correcte']);

        foreach ($answers as $ans) {
            $qid = intval($ans['question_id']);
            $sel = isset($ans['selected']) ? intval($ans['selected']) : null;
            if ($sel !== null && isset($correctMap[$qid]) && $sel === $correctMap[$qid]) {
                $score++;
            }
        }

        // Enregistrer la tentative
        $attemptId = $this->attemptModel->create($id, $student_name, $score, $total, $time_seconds);

        // Enregistrer les réponses
        foreach ($answers as $ans) {
            $qid = intval($ans['question_id']);
            $sel = isset($ans['selected']) ? intval($ans['selected']) : null;
            $this->attemptModel->addAnswer($attemptId, $qid, $sel);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'attempt_id' => $attemptId,
            'score' => $score,
            'total' => $total
        ]);
    }

    // Admin: toutes les tentatives
    public function results() {
        header('Content-Type: application/json');
        echo json_encode($this->attemptModel->getAllAttempts());
    }

    // Admin: réponses d'une tentative
    public function answers($attempt_id = null) {
        if (!$attempt_id) {
            echo json_encode([]);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode($this->attemptModel->getAnswers($attempt_id));
    }
}
