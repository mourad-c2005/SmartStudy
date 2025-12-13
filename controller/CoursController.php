<?php
class CoursController extends Controller {

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

    // Liste des cours d’un chapitre
    public function index($id_chapitre = null) {
        if (!$id_chapitre) {
            // If no chapitre specified, redirect to chapitre list
            header("Location: " . URLROOT . "/Chapitre");
            exit;
        }

        // Verify chapitre exists
        $chapitre = $this->chapitreModel->getById($id_chapitre);
        if (!$chapitre) {
            header("Location: " . URLROOT . "/Chapitre");
            exit;
        }

        $data = [
            'chapitre' => $chapitre,
            'cours'    => $this->coursModel->getAllByChapitre($id_chapitre)
        ];

        $this->view('layout');
        $this->view('cours/index', $data);
    }

    // Show form to select chapitre for adding a course
    public function selectChapitre() {
        if (empty($_SESSION['admin_logged'])) {
            header("Location: " . URLROOT . "/Admin/login");
            exit;
        }

        $chapitres = $this->chapitreModel->getAll();
        $data = ['chapitres' => $chapitres];
        
        $this->view('layout');
        $this->view('cours/select_chapitre', $data);
    }

    // Add a course with quiz - parameter is optional
    public function add($id_chapitre = null) {
        if (empty($_SESSION['admin_logged'])) {
            header("Location: " . URLROOT . "/Admin/login");
            exit;
        }

        // If no chapitre ID provided in URL, check POST data
        if (!$id_chapitre) {
            $id_chapitre = $_POST['id_chapitre'] ?? null;
        }

        // If still no chapitre ID, redirect to chapitre selection
        if (!$id_chapitre) {
            header("Location: " . URLROOT . "/Cours/selectChapitre");
            exit;
        }

        // Verify chapitre exists
        $chapitre = $this->chapitreModel->getById($id_chapitre);
        if (!$chapitre) {
            header("Location: " . URLROOT . "/Cours/selectChapitre");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';
            $id_chapitre = $_POST['id_chapitre'] ?? $id_chapitre;

            // Validate required fields
            if (empty($titre) || empty($contenu)) {
                $data = [
                    'error' => 'Le titre et le contenu sont obligatoires',
                    'id_chapitre' => $id_chapitre
                ];
                $this->view('layout');
                $this->view('cours/add', $data);
                return;
            }

            // Add course
            $cours_id = $this->coursModel->add($id_chapitre, $titre, $contenu, $lien_video);

            // Add quiz questions if provided
            if (!empty($_POST['quiz']) && is_array($_POST['quiz'])) {
                foreach ($_POST['quiz'] as $q) {
                    if (!empty($q['question'])) {
                        $this->quizModel->add(
                            $cours_id,
                            $id_chapitre,
                            trim($q['question']),
                            trim($q['rep1'] ?? ''),
                            trim($q['rep2'] ?? ''),
                            trim($q['rep3'] ?? ''),
                            trim($q['rep4'] ?? ''),
                            intval($q['correcte'] ?? 1)
                        );
                    }
                }
            }

            // Success - redirect to course list for this chapitre
            header("Location: " . URLROOT . "/Cours/index/" . $id_chapitre);
            exit;
        }

        // GET request - show add form
        $data = [
            'id_chapitre' => $id_chapitre,
            'chapitre' => $chapitre
        ];
        
        $this->view('layout');
        $this->view('cours/add', $data);
    }

    // Edit a course + quiz
    public function edit($id = null) {
        if (empty($_SESSION['admin_logged'])) {
            header("Location: " . URLROOT . "/Admin/login");
            exit;
        }

        if (!$id) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $cours = $this->coursModel->getById($id);
        if (!$cours) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $quiz = $this->quizModel->getByCoursWithJoin($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';

            // Validate required fields
            if (empty($titre) || empty($contenu)) {
                $data = [
                    'cours' => $cours,
                    'quizs' => $quiz,
                    'error' => 'Le titre et le contenu sont obligatoires'
                ];
                $this->view('layout');
                $this->view('cours/edit', $data);
                return;
            }

            // Update course
            $this->coursModel->update($id, $titre, $contenu, $lien_video);

            // Update or add quiz questions
            if (!empty($_POST['quiz']) && is_array($_POST['quiz'])) {
                foreach ($_POST['quiz'] as $q) {
                    if (!empty($q['question'])) {
                        if (!empty($q['id'])) {
                            // Update existing quiz
                            $this->quizModel->update(
                                intval($q['id']),
                                trim($q['question']),
                                trim($q['rep1'] ?? ''),
                                trim($q['rep2'] ?? ''),
                                trim($q['rep3'] ?? ''),
                                trim($q['rep4'] ?? ''),
                                intval($q['correcte'] ?? 1)
                            );
                        } else {
                            // Add new quiz
                            $this->quizModel->add(
                                $id,
                                $cours['id_chapitre'],
                                trim($q['question']),
                                trim($q['rep1'] ?? ''),
                                trim($q['rep2'] ?? ''),
                                trim($q['rep3'] ?? ''),
                                trim($q['rep4'] ?? ''),
                                intval($q['correcte'] ?? 1)
                            );
                        }
                    }
                }
            }

            // Success - redirect to course view
            header("Location: " . URLROOT . "/Cours/show/" . $id);
            exit;
        }

        // GET request - show edit form
        $data = [
            'cours' => $cours,
            'quizs' => $quiz
        ];
        
        $this->view('layout');
        $this->view('cours/edit', $data);
    }

    // Show a single course
    public function show($id = null) {
        if (!$id) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $cours = $this->coursModel->getById($id);
        if (!$cours) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $chapitre = $this->chapitreModel->getById($cours['id_chapitre']);
        $quiz = $this->quizModel->getAllByCours($id);

        $data = [
            'cours' => $cours,
            'chapitre' => $chapitre,
            'quiz' => $quiz
        ];

        $this->view('layout');
        $this->view('cours/show', $data);
    }

    // Delete a course
    public function delete($id = null) {
        if (empty($_SESSION['admin_logged'])) {
            header("Location: " . URLROOT . "/Admin/login");
            exit;
        }

        if (!$id) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $cours = $this->coursModel->getById($id);
        if ($cours) {
            $this->coursModel->delete($id);
            header("Location: " . URLROOT . "/Cours/index/" . $cours['id_chapitre']);
            exit;
        } else {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }
    }

    // Submit quiz answers
    public function submit($id_chapitre = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        // Get JSON input
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            $body = $_POST; // Fallback to regular POST
        }

        $student_name = trim($body['student_name'] ?? 'Invité');
        $answers = $body['answers'] ?? [];
        $time_seconds = intval($body['time_seconds'] ?? 0);
        
        // Get chapitre ID from parameter or POST
        $id_chap = $id_chapitre ? intval($id_chapitre) : intval($body['id_chapitre'] ?? 0);

        // Verify chapitre exists
        $chapitre = $this->chapitreModel->getById($id_chap);
        if (!$chapitre) {
            http_response_code(400);
            echo json_encode(['error' => 'Chapitre inexistant']);
            exit;
        }

        // Get questions for this chapitre
        $questions = $this->quizModel->getAllByChapitre($id_chap);
        $total = count($questions);
        $score = 0;

        // Create map of correct answers
        $correctMap = [];
        foreach ($questions as $q) {
            $correctMap[$q['id']] = intval($q['correcte']);
        }

        // Calculate score
        foreach ($answers as $ans) {
            $qid = intval($ans['question_id'] ?? 0);
            $sel = isset($ans['selected']) ? intval($ans['selected']) : null;
            
            if ($sel !== null && isset($correctMap[$qid]) && $sel === $correctMap[$qid]) {
                $score++;
            }
        }

        // Create attempt record
        $attemptId = $this->attemptModel->create($id_chap, $student_name, $score, $total, $time_seconds);

        // Save individual answers
        foreach ($answers as $ans) {
            $qid = intval($ans['question_id'] ?? 0);
            $sel = isset($ans['selected']) ? intval($ans['selected']) : null;
            $this->attemptModel->addAnswer($attemptId, $qid, $sel);
        }

        // Return success response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'attempt_id' => $attemptId,
            'score' => $score,
            'total' => $total,
            'percentage' => $total > 0 ? round(($score / $total) * 100, 2) : 0
        ]);
    }

    // Show quiz for a course
    public function quiz($id = null) {
        if (!$id) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $cours = $this->coursModel->getById($id);
        if (!$cours) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $quiz = $this->quizModel->getAllByCours($id);

        $data = [
            'cours' => $cours,
            'questions' => $quiz
        ];

        $this->view('layout');
        $this->view('cours/quiz', $data);
    }

    // Show quiz results
    public function results($attempt_id = null) {
        if (!$attempt_id) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $attempt = $this->attemptModel->getById($attempt_id);
        if (!$attempt) {
            header("Location: " . URLROOT . "/Cours");
            exit;
        }

        $answers = $this->attemptModel->getAnswers($attempt_id);
        $questions = $this->quizModel->getAllByChapitre($attempt['id_chapitre']);

        // Format data for view
        $formattedData = [];
        foreach ($questions as $question) {
            $studentAnswer = null;
            foreach ($answers as $ans) {
                if ($ans['question_id'] == $question['id']) {
                    $studentAnswer = $ans['selected'];
                    break;
                }
            }

            $formattedData[] = [
                'question' => $question['question'],
                'options' => [
                    $question['rep1'],
                    $question['rep2'],
                    $question['rep3'],
                    $question['rep4']
                ],
                'correct_answer' => $question['correcte'] - 1, // Convert to 0-index
                'student_answer' => $studentAnswer,
                'is_correct' => $studentAnswer !== null && $studentAnswer == $question['correcte'] - 1
            ];
        }

        $data = [
            'attempt' => $attempt,
            'questions' => $formattedData
        ];

        $this->view('layout');
        $this->view('cours/results', $data);
    }
}