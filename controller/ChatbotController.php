<?php
// app/controllers/ChatbotController.php
class ChatbotController extends Controller {
    private $coursModel;

    public function __construct() {
        $this->coursModel = $this->model('Cours');
    }

    // Page principale du chatbot
    public function index() {
        $courses = $this->coursModel->getAll();
        $data = ['courses' => $courses];
        $this->view('layout');
        $this->view('chatbot/index', $data);
    }

    // Retourne le contenu d'un cours en JSON
    public function getCourse($id = null) {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID cours manquant']);
            exit;
        }
        $cours = $this->coursModel->getById(intval($id));
        if (!$cours) {
            http_response_code(404);
            echo json_encode(['error' => 'Cours introuvable']);
            exit;
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cours' => $cours]);
    }

    // Endpoint pour résumer un texte (POST JSON { text: "..." })
    public function summarize() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $body = json_decode(file_get_contents('php://input'), true);
        $text = trim($body['text'] ?? '');
        if ($text === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Texte manquant']);
            exit;
        }

        // Si une clé API OpenAI est définie, appeler l'API pour un meilleur résumé.
        if (defined('OPENAI_API_KEY') && OPENAI_API_KEY !== '') {
            $summary = $this->callOpenAIAndSummarize($text);
            if ($summary === null) {
                // fallback local si erreur
                $summary = $this->localSummarize($text);
            }
        } else {
            // Par défaut, résumé local simple (extraction des phrases)
            $summary = $this->localSummarize($text);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'summary' => $summary]);
    }

    // Appel simple à l'API OpenAI Chat Completions pour demander un résumé
    private function callOpenAIAndSummarize($text) {
        $apiKey = OPENAI_API_KEY;
        $model = defined('OPENAI_MODEL') ? OPENAI_MODEL : 'gpt-3.5-turbo';

        $prompt = "Peux-tu résumer ce texte en 3 à 5 phrases en français, en gardant les points clés ?\n\n" . $text;

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'max_tokens' => 300,
            'temperature' => 0.3
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($ch);
        if ($result === false) {
            curl_close($ch);
            return null;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            return null;
        }

        $data = json_decode($result, true);
        if (!isset($data['choices'][0]['message']['content'])) return null;
        $reply = trim($data['choices'][0]['message']['content']);
        return $reply;
    }

    // Méthode de secours: résumé simple local
    private function localSummarize($text, $maxSentences = 3) {
        // Normaliser et découper en phrases basiques
        $text = trim(preg_replace('/\s+/', ' ', $text));
        // Utiliser une séparation simple sur [.?!]\s
        $sentences = preg_split('/(?<=[\.\?!])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (!$sentences || count($sentences) === 0) return '';

        // Si peu de phrases, renvoyer tout
        if (count($sentences) <= $maxSentences) {
            return implode(' ', $sentences);
        }

        // Choisir les phrases les plus longues (heuristique simple)
        usort($sentences, function($a, $b){
            return mb_strlen($b) - mb_strlen($a);
        });
        $top = array_slice($sentences, 0, $maxSentences);
        // Remettre dans l'ordre d'apparition dans le texte
        usort($top, function($a, $b) use ($text){
            return mb_strpos($text, $a) - mb_strpos($text, $b);
        });
        return implode(' ', $top);
    }
}
