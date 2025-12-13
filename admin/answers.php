<?php
session_start();
if (empty($_SESSION['admin_logged'])) { 
    header('Location: login.php'); 
    exit; 
}

require_once "../config/config.php";
require_once "../app/core/Database.php";
require_once "../app/core/Model.php";
require_once "../app/models/Attempt.php";

$attemptModel = new Attempt();

// Récupérer toutes les tentatives
$allAttempts = $attemptModel->getAllAttempts();

// Pour chaque tentative, récupérer les réponses
foreach ($allAttempts as &$attempt) {
    $attempt['answers'] = $attemptModel->getAnswers($attempt['id'] ?? 0);
}
unset($attempt); // Déréférencer la variable

// Gérer la suppression si nécessaire
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $attemptModel->delete($id);
    header("Location: answers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toutes les réponses - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* CSS INTÉGRÉ POUR ANSWERS.PHP */
        :root {
            --green: #4CAF50;
            --yellow: #FFEB3B;
            --light: #E8F5E8;
            --white: #ffffff;
            --dark: #2e7d32;
            --blue: #2196F3;
            --red: #e53935;
            --orange: #FF9800;
        }
        
        * { 
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: var(--light);
            color: #333;
            margin: 0;
            padding: 0;
            padding-bottom: 60px;
        }
        
        /* Header - Utilisez le même que dans header.php */
        
        .main-content {
            padding: 2rem 5%;
            min-height: calc(100vh - 70px - 60px);
        }
        
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-family: 'Montserrat', sans-serif;
            color: var(--dark);
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        /* Statistiques */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        
        .stat-card {
            background: var(--white);
            padding: 1.8rem;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            border-left: 5px solid var(--green);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            margin: 0 0 0.8rem 0;
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-card h3 i {
            color: var(--green);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1;
        }
        
        .stat-unit {
            font-size: 1rem;
            color: #999;
            margin-left: 0.3rem;
            font-weight: 400;
        }
        
        /* Filtres */
        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            padding: 1rem;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .filter-btn {
            background: var(--white);
            color: #666;
            padding: 0.6rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            border: 2px solid #eee;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--green);
            color: white;
            border-color: var(--green);
        }
        
        /* Accordéon des tentatives */
        .attempts-container {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            padding: 1.5rem;
        }
        
        .attempt-item {
            border: 1px solid #eee;
            border-radius: 12px;
            margin-bottom: 1rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .attempt-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .attempt-header {
            background: #f9f9f9;
            padding: 1rem 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }
        
        .attempt-header:hover {
            background: #f0f0f0;
        }
        
        .attempt-header.active {
            background: #E8F5E9;
        }
        
        .attempt-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .attempt-id {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.1rem;
        }
        
        .attempt-student {
            font-weight: 600;
            color: #333;
        }
        
        .attempt-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .attempt-score {
            background: #E8F5E9;
            color: var(--dark);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .attempt-time {
            background: #E3F2FD;
            color: var(--blue);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .attempt-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .attempt-toggle {
            font-size: 1.2rem;
            color: var(--green);
            transition: transform 0.3s;
        }
        
        .attempt-toggle.active {
            transform: rotate(180deg);
        }
        
        /* Contenu des réponses */
        .attempt-content {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.5s ease;
            background: var(--white);
        }
        
        .attempt-content.active {
            padding: 1.5rem;
            max-height: 5000px;
        }
        
        .answers-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .answers-table th {
            background: var(--green);
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
        }
        
        .answers-table td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #eee;
            color: #555;
            font-size: 0.9rem;
            vertical-align: top;
        }
        
        .question-cell {
            font-weight: 600;
            color: #333;
        }
        
        .answers-list {
            line-height: 1.5;
        }
        
        .answer-option {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 0.3rem;
        }
        
        .answer-letter {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #f0f0f0;
            font-weight: 600;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        
        .selected-answer, .correct-answer {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.3rem 0.6rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8rem;
            min-width: 30px;
        }
        
        .selected-answer {
            background: var(--blue);
            color: white;
        }
        
        .selected-answer.wrong {
            background: var(--red);
            color: white;
        }
        
        .selected-answer.correct {
            background: var(--green);
            color: white;
        }
        
        .correct-answer {
            background: #E8F5E9;
            color: var(--dark);
            border: 1px solid #C8E6C9;
        }
        
        /* Actions */
        .attempt-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            border: none;
            cursor: pointer;
        }
        
        .action-delete {
            background: #FFEBEE;
            color: var(--red);
            border: 1px solid #FFCDD2;
        }
        
        .action-delete:hover {
            background: var(--red);
            color: white;
        }
        
        .action-download {
            background: #E8F5E9;
            color: var(--dark);
            border: 1px solid #C8E6C9;
        }
        
        .action-download:hover {
            background: var(--green);
            color: white;
        }
        
        /* Message vide */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #888;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .main-content {
                padding: 1.5rem;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .attempt-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .attempt-meta {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .answers-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .filter-btn {
                width: 100%;
                text-align: center;
            }
            
            .attempt-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-key"></i> Accès aux réponses</h1>
        <p>Consultez toutes les réponses détaillées des étudiants</p>
    </div>

    <!-- Statistiques -->
    <?php
    $totalAttempts = count($allAttempts);
    $totalQuestions = 0;
    $totalCorrect = 0;
    $totalTime = 0;
    
    foreach ($allAttempts as $attempt) {
        if (isset($attempt['answers']) && is_array($attempt['answers'])) {
            $totalQuestions += count($attempt['answers']);
            foreach ($attempt['answers'] as $answer) {
                if (isset($answer['selected'], $answer['correcte']) && $answer['selected'] === $answer['correcte']) {
                    $totalCorrect++;
                }
            }
        }
        $totalTime += intval($attempt['time_seconds'] ?? 0);
    }
    
    $avgScore = $totalQuestions > 0 ? round(($totalCorrect / $totalQuestions) * 100, 1) : 0;
    $avgTime = $totalAttempts > 0 ? round($totalTime / $totalAttempts, 0) : 0;
    ?>
    
    <div class="stats-container">
        <div class="stat-card">
            <h3><i class="fas fa-clipboard-list"></i> Total de tentatives</h3>
            <div class="stat-value"><?= $totalAttempts ?></div>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-question-circle"></i> Questions répondues</h3>
            <div class="stat-value"><?= $totalQuestions ?></div>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-percentage"></i> Score moyen</h3>
            <div class="stat-value"><?= $avgScore ?><span class="stat-unit">%</span></div>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-clock"></i> Temps moyen</h3>
            <div class="stat-value"><?= $avgTime ?><span class="stat-unit">sec</span></div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters">
        <button class="filter-btn active" data-filter="all">
            <i class="fas fa-list"></i> Toutes (<?= $totalAttempts ?>)
        </button>
        <button class="filter-btn" data-filter="high">
            <i class="fas fa-trophy"></i> Scores élevés
        </button>
        <button class="filter-btn" data-filter="low">
            <i class="fas fa-exclamation-triangle"></i> Scores bas
        </button>
        <button class="filter-btn" data-filter="recent">
            <i class="fas fa-calendar-alt"></i> Récentes
        </button>
    </div>

    <!-- Liste des tentatives -->
    <div class="attempts-container">
        <?php if(!empty($allAttempts)): ?>
            <?php foreach ($allAttempts as $attempt): 
                $attemptId = $attempt['id'] ?? 0;
                $studentName = htmlspecialchars($attempt['student_name'] ?? 'Anonyme');
                $score = $attempt['score'] ?? 0;
                $totalQ = $attempt['total_questions'] ?? 0;
                $timeSeconds = $attempt['time_seconds'] ?? 0;
                $date = isset($attempt['created_at']) ? date('d/m/Y H:i', strtotime($attempt['created_at'])) : 'Date inconnue';
                $chapitre = htmlspecialchars($attempt['chapitre_titre'] ?? $attempt['id_chapitre'] ?? 'Non spécifié');
                $answers = $attempt['answers'] ?? [];
                
                $scorePercent = $totalQ > 0 ? round(($score / $totalQ) * 100, 1) : 0;
                $createdTimestamp = isset($attempt['created_at']) ? strtotime($attempt['created_at']) : time();
            ?>
                <div class="attempt-item" data-score="<?= $scorePercent ?>" 
                     data-date="<?= $createdTimestamp ?>">
                    <div class="attempt-header" onclick="toggleAttempt(<?= $attemptId ?>)">
                        <div class="attempt-title">
                            <span class="attempt-id">Tentative #<?= $attemptId ?></span>
                            <span class="attempt-student">
                                <i class="fas fa-user"></i> <?= $studentName ?>
                            </span>
                            <span class="attempt-date">
                                <i class="far fa-calendar-alt"></i> <?= $date ?>
                            </span>
                        </div>
                        <div class="attempt-meta">
                            <span class="attempt-score">
                                <i class="fas fa-chart-bar"></i> <?= $score ?>/<?= $totalQ ?> (<?= $scorePercent ?>%)
                            </span>
                            <span class="attempt-time">
                                <i class="fas fa-clock"></i> <?= $timeSeconds ?>s
                            </span>
                            <span class="attempt-toggle" id="toggle-<?= $attemptId ?>">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>
                    
                    <div class="attempt-content" id="content-<?= $attemptId ?>">
                        <div style="margin-bottom: 1rem;">
                            <strong>Chapitre:</strong> <?= $chapitre ?><br>
                            <strong>Date:</strong> <?= $date ?>
                        </div>
                        
                        <?php if(!empty($answers)): ?>
                            <table class="answers-table">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">Question</th>
                                        <th style="width: 40%;">Réponses</th>
                                        <th style="width: 15%;">Choisie</th>
                                        <th style="width: 15%;">Correcte</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $correctCount = 0;
                                    foreach ($answers as $index => $a): 
                                        $isCorrect = (isset($a['selected'], $a['correcte']) && $a['selected'] === $a['correcte']);
                                        $isEmpty = (!isset($a['selected']) || $a['selected'] === null);
                                        
                                        if ($isCorrect && !$isEmpty) {
                                            $correctCount++;
                                        }
                                        
                                        $selectedLetter = $isEmpty ? '-' : chr(65 + $a['selected']);
                                        $correctLetter = isset($a['correcte']) ? chr(65 + $a['correcte']) : '?';
                                    ?>
                                        <tr>
                                            <td class="question-cell">
                                                <strong>Q<?= $index + 1 ?>:</strong> <?= htmlspecialchars($a['question'] ?? 'Question non disponible') ?>
                                            </td>
                                            <td class="answers-list">
                                                <?php 
                                                $reponses = [
                                                    $a['rep1'] ?? '',
                                                    $a['rep2'] ?? '',
                                                    $a['rep3'] ?? '',
                                                    $a['rep4'] ?? ''
                                                ];
                                                foreach ($reponses as $i => $rep): 
                                                    $letter = chr(65 + $i);
                                                ?>
                                                    <div class="answer-option">
                                                        <span class="answer-letter"><?= $letter ?></span>
                                                        <span class="answer-text"><?= htmlspecialchars($rep) ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </td>
                                            <td>
                                                <?php if($isEmpty): ?>
                                                    <span class="selected-answer">-</span>
                                                <?php else: ?>
                                                    <?php 
                                                    $class = $isCorrect ? 'correct' : 'wrong';
                                                    echo '<span class="selected-answer ' . $class . '">' . $selectedLetter . '</span>';
                                                    ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="correct-answer"><?= $correctLetter ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <?php if(count($answers) > 0): ?>
                            <div style="margin-top: 1rem; padding: 1rem; background: #f9f9f9; border-radius: 8px;">
                                <strong>Résumé:</strong> <?= $correctCount ?> réponses correctes sur <?= count($answers) ?> 
                                (<?= count($answers) > 0 ? round(($correctCount / count($answers)) * 100, 1) : 0 ?>%)
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p style="color: #666; font-style: italic; padding: 1rem; background: #f9f9f9; border-radius: 8px;">
                                Aucune réponse enregistrée pour cette tentative.
                            </p>
                        <?php endif; ?>
                        
                        <div class="attempt-actions">
                            <a href="answers.php?delete=<?= $attemptId ?>" 
                               class="action-btn action-delete"
                               onclick="return confirm('Supprimer cette tentative et toutes ses réponses ?');">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                            <button class="action-btn action-download" onclick="downloadAttempt(<?= $attemptId ?>)">
                                <i class="fas fa-download"></i> Télécharger PDF
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucune tentative</h3>
                <p>Les étudiants n'ont pas encore passé de quiz.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Fonction pour ouvrir/fermer un accordéon
    function toggleAttempt(attemptId) {
        const content = document.getElementById('content-' + attemptId);
        const toggle = document.getElementById('toggle-' + attemptId);
        const header = content.parentElement.querySelector('.attempt-header');
        
        if (content && toggle) {
            content.classList.toggle('active');
            toggle.classList.toggle('active');
            if (header) header.classList.toggle('active');
        }
    }
    
    // Filtrer les tentatives
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Mettre à jour les boutons actifs
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filtrer les tentatives
            const attempts = document.querySelectorAll('.attempt-item');
            attempts.forEach(attempt => {
                const score = parseInt(attempt.getAttribute('data-score'));
                const date = parseInt(attempt.getAttribute('data-date'));
                const now = Math.floor(Date.now() / 1000);
                const oneDay = 24 * 60 * 60;
                
                let show = true;
                
                switch(filter) {
                    case 'high':
                        show = score >= 70;
                        break;
                    case 'low':
                        show = score < 50;
                        break;
                    case 'recent':
                        show = (now - date) <= (7 * oneDay); // Moins de 7 jours
                        break;
                    default:
                        show = true;
                }
                
                attempt.style.display = show ? 'block' : 'none';
            });
        });
    });
    
    // Télécharger en PDF (simulation)
    function downloadAttempt(attemptId) {
        alert('Fonction PDF: Export de la tentative #' + attemptId + '\n(À implémenter avec une librairie PDF)');
        // window.open('generate_pdf.php?attempt_id=' + attemptId, '_blank');
    }
    
    // Ouvrir le premier accordéon par défaut
    document.addEventListener('DOMContentLoaded', function() {
        const firstAttempt = document.querySelector('.attempt-item');
        if (firstAttempt) {
            const firstId = firstAttempt.querySelector('.attempt-id')?.textContent?.match(/#(\d+)/)?.[1];
            if (firstId) {
                toggleAttempt(firstId);
            }
        }
    });
</script>

</body>
</html>