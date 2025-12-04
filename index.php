<?php
// app/views/quiz/index.php
$questions = $data['questions']; // questions du cours
?>
<div class="welcome-card">
    <h1>Quiz : <?= htmlspecialchars($data['cours']['titre']) ?></h1>

    <div id="quiz-container"></div>

    <div id="quiz-controls" style="margin-top:20px;">
        <button id="prevBtn" onclick="prevQuestion()">Précédent</button>
        <button id="nextBtn" onclick="nextQuestion()">Suivant</button>
        <button id="finishBtn" onclick="finishQuiz()" style="display:none;">Terminer</button>
    </div>

    <p id="timer" style="margin-top:15px;font-weight:600;">Temps: 0s</p>
    <p id="result" style="margin-top:15px;font-weight:600;"></p>
</div>

<script>
const questions = <?= json_encode(array_map(function($q){
    return [
        'id'=> (int)$q['id'],
        'question'=> $q['question'],
        'reponses'=> [$q['rep1'],$q['rep2'],$q['rep3'],$q['rep4']],
        'correcte'=> (int)$q['correcte']
    ];
}, $questions), JSON_UNESCAPED_UNICODE) ?>;

// Variables du quiz
let idx = 0;
let answers = new Array(questions.length).fill(null);
let seconds = 0;

// Timer
const timerEl = document.getElementById('timer');
const timerInterval = setInterval(() => {
    seconds++;
    timerEl.innerText = `Temps: ${seconds}s`;
}, 1000);

// Affichage de la question actuelle
function render() {
    if (!questions.length) {
        document.getElementById('quiz-container').innerHTML = '<p>Aucune question.</p>';
        return;
    }

    const q = questions[idx];
    const html = `<h3>Question ${idx+1} / ${questions.length}</h3>
        <p>${q.question}</p>
        <div class="answers">
            ${q.reponses.map((r,i) => `
                <label style="display:block;margin-bottom:5px;">
                    <input type="radio" name="q${idx}" value="${i}" ${answers[idx]===i?'checked':''} onclick="select(${i})">
                    ${r}
                </label>
            `).join('')}
        </div>
        <p>Ta réponse: ${answers[idx]===null?'-':(answers[idx]+1)}</p>
    `;
    document.getElementById('quiz-container').innerHTML = html;

    // Boutons navigation
    document.getElementById('prevBtn').style.display = idx===0?'none':'inline-block';
    document.getElementById('nextBtn').style.display = idx===questions.length-1?'none':'inline-block';
    document.getElementById('finishBtn').style.display = idx===questions.length-1?'inline-block':'none';
    document.getElementById('result').innerText = '';
}

// Sélection d'une réponse
function select(i) {
    answers[idx] = i;
    render();
}

// Navigation
function nextQuestion() { if(idx < questions.length-1) idx++; render(); }
function prevQuestion() { if(idx > 0) idx--; render(); }

// Fin du quiz
function finishQuiz() {
    clearInterval(timerInterval); // arrêter le timer
    let score = 0;
    for(let i=0; i<questions.length; i++){
        if(answers[i] === questions[i].correcte) score++;
    }
    document.getElementById('result').innerText = `Score: ${score} / ${questions.length} | Temps: ${seconds}s`;
    window.scrollTo(0,0);
}

// Initialisation
render();
</script>
