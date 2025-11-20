<div class="welcome-card">
    <h1>Quiz : <?= htmlspecialchars($data['cours']['titre']) ?></h1>

    <div id="student-info">
        <label>Nom et prénom :</label>
        <input type="text" id="student_name" placeholder="Votre nom et prénom">
        <button id="startQuiz">Commencer le quiz</button>
    </div>

    <form id="quizForm" method="POST" action="<?= URLROOT ?>/Quiz/submit/<?= $data['cours']['id'] ?>" style="display:none;">
        <div id="quiz-container"></div>
        <div id="quiz-controls" style="margin-top:15px;">
            <button type="button" id="prevBtn" onclick="prevQuestion()" style="display:none;">Précédent</button>
            <button type="button" id="nextBtn" onclick="nextQuestion()">Suivant</button>
        </div>
        <input type="hidden" name="time_seconds" id="time_seconds" value="0">
        <input type="hidden" name="student_name" id="hidden_student_name">
        <button type="submit" style="margin-top:10px;">Terminer le quiz</button>
    </form>

    <p id="timer" style="font-weight:600;"></p>
</div>

<script>
let questions = <?= json_encode(array_map(function($q){
    return [
        'id'=> (int)$q['id'],
        'question'=> $q['question'],
        'reponses'=> [$q['rep1'],$q['rep2'],$q['rep3'],$q['rep4']],
        'correcte'=> (int)$q['correcte']
    ];
}, $data['questions']), JSON_UNESCAPED_UNICODE) ?>;

let idx = 0;
let answers = new Array(questions.length).fill(null);
let seconds = 0;
let timerInterval;

const quizForm = document.getElementById('quizForm');
const quizContainer = document.getElementById('quiz-container');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

document.getElementById('startQuiz').addEventListener('click', () => {
    const name = document.getElementById('student_name').value.trim();
    if (!name) { alert("Veuillez saisir votre nom et prénom !"); return; }
    document.getElementById('hidden_student_name').value = name;
    document.getElementById('student-info').style.display = 'none';
    quizForm.style.display = 'block';
    startTimer();
    render();
});

function startTimer() {
    timerInterval = setInterval(() => {
        seconds++;
        document.getElementById('time_seconds').value = seconds;
        document.getElementById('timer').innerText = `Temps: ${seconds}s`;
    }, 1000);
}

function render() {
    if (!questions.length) { quizContainer.innerHTML = '<p>Aucune question.</p>'; return; }
    const q = questions[idx];
    let html = `<h3>Question ${idx+1} / ${questions.length}</h3><p>${q.question}</p><div>`;
    q.reponses.forEach((r,i) => {
        html += `<label style="display:block;margin-bottom:5px;">
                    <input type="radio" name="q${idx}" value="${i}" ${answers[idx]===i?'checked':''} onchange="selectAnswer(${i})">
                    ${r}
                 </label>`;
    });
    html += `</div>`;
    quizContainer.innerHTML = html;

    // Affichage des boutons
    prevBtn.style.display = idx === 0 ? 'none' : 'inline-block';
    nextBtn.style.display = idx === questions.length-1 ? 'none' : 'inline-block';
}

function selectAnswer(i) {
    answers[idx] = i;
}

function nextQuestion() {
    if(idx < questions.length-1) { idx++; render(); }
}

function prevQuestion() {
    if(idx > 0) { idx--; render(); }
}

quizForm.addEventListener('submit', function(e){
    e.preventDefault();
    const hidden = document.getElementById('hidden_student_name');
    if (!hidden.value.trim()) { alert("Nom et prénom obligatoire !"); return; }

    const payload = [];
    questions.forEach((q,index) => {
        payload.push({question_id:q.id, selected: answers[index] !== null ? answers[index] : null});
    });
    hidden.value = hidden.value.trim();

    this.submit(); // envoi final
});
</script>
