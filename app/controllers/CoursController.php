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
        if (!$id_chapitre) header("Location: " . URLROOT);

        $data = [
            'chapitre' => $this->chapitreModel->getById($id_chapitre),
            'cours'    => $this->coursModel->getAllByChapitre($id_chapitre)
        ];

        $this->view('layout');
        $this->view('cours/index', $data);
    }

    // Ajouter un cours + quiz
    public function add($id_chapitre) {
        if (empty($_SESSION['admin_logged'])) exit("Accès interdit.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';

            $cours_id = $this->coursModel->add($id_chapitre, $titre, $contenu, $lien_video);

            // Ajouter quiz si fourni
            if (!empty($_POST['quiz']) && is_array($_POST['quiz'])) {
                foreach ($_POST['quiz'] as $q) {
                    $this->quizModel->add(
                        $cours_id,
                        $id_chapitre, // id chapitre
                        $q['question'] ?? '',
                        $q['rep1'] ?? '',
                        $q['rep2'] ?? '',
                        $q['rep3'] ?? '',
                        $q['rep4'] ?? '',
                        intval($q['correcte'] ?? 0)
                    );
                }
            }

            header("Location: " . URLROOT . "/Cours/index/" . $id_chapitre);
            exit;
        }

        $data['id_chapitre'] = $id_chapitre;
        $this->view('layout');
        $this->view('cours/add', $data);
    }

    // Modifier un cours + quiz
    public function edit($id) {
        if (empty($_SESSION['admin_logged'])) exit("Accès interdit.");

        $cours = $this->coursModel->getById($id);
        if (!$cours) exit("Cours introuvable.");

        $quizs = $this->quizModel->getAllByCours($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';
            $lien_video = $_POST['lien_video'] ?? '';

            $this->coursModel->update($id, $titre, $contenu, $lien_video);

            // Ajouter ou modifier quiz
            if (!empty($_POST['quiz']) && is_array($_POST['quiz'])) {
                foreach ($_POST['quiz'] as $q) {
                    if (!empty($q['id'])) {
                        // Mise à jour d’un quiz existant
                        $this->quizModel->update(
                            intval($q['id']),
                            $q['question'] ?? '',
                            $q['rep1'] ?? '',
                            $q['rep2'] ?? '',
                            $q['rep3'] ?? '',
                            $q['rep4'] ?? '',
                            intval($q['correcte'] ?? 0)
                        );
                    } else {
                        // Ajout d’un nouveau quiz
                        $this->quizModel->add(
                            $id, // id du cours
                            $cours['id_chapitre'], // id du chapitre
                            $q['question'] ?? '',
                            $q['rep1'] ?? '',
                            $q['rep2'] ?? '',
                            $q['rep3'] ?? '',
                            $q['rep4'] ?? '',
                            intval($q['correcte'] ?? 0)
                        );
                    }
                }
            }

            header("Location: " . URLROOT . "/Cours/show/" . $id);
            exit;
        }

        $data['cours'] = $cours;
        $data['quizs'] = $quizs;
        $this->view('layout');
        $this->view('cours/edit', $data);
    }
// Afficher un cours
public function show($id) {
    $cours = $this->coursModel->getById($id);
    if (!$cours) {
        header("Location: " . URLROOT);
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

    // Supprimer un cours
    public function delete($id) {
        if (empty($_SESSION['admin_logged'])) exit("Accès interdit.");

        $cours = $this->coursModel->getById($id);
        if ($cours) {
            $this->coursModel->delete($id);
            header("Location: " . URLROOT . "/Cours/index/" . $cours['id_chapitre']);
            exit;
        } else {
            exit("Cours introuvable.");
        }
    }

    // Soumission d’un quiz par l’étudiant
    public function submit($id_chapitre = null) {
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
        $id_chap = intval($id_chapitre);

        // Vérifier que le chapitre existe
        $chapitre = $this->chapitreModel->getById($id_chap);
        if (!$chapitre) {
            http_response_code(400);
            echo json_encode(['error' => 'Chapitre inexistant']);
            exit;
        }

        // Récupérer les questions du chapitre
        $questions = $this->quizModel->getAllByChapitre($id_chap);
        $total = count($questions);
        $score = 0;

        // Tableau des bonnes réponses
        $correctMap = [];
        foreach ($questions as $q) {
            $correctMap[$q['id']] = intval($q['correcte']);
        }

        // Calculer le score
        foreach ($answers as $ans) {
            $qid = intval($ans['question_id']);
            $sel = isset($ans['selected']) ? intval($ans['selected']) : null;
            if ($sel !== null && isset($correctMap[$qid]) && $sel === $correctMap[$qid]) {
                $score++;
            }
        }

        // Créer la tentative
        $attemptId = $this->attemptModel->create($id_chap, $student_name, $score, $total, $time_seconds);

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
}