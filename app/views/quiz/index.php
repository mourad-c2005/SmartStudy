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
<style>
/* ----------- CARD PRINCIPALE ----------- */
.welcome-card{
    max-width: 850px;
    margin: 40px auto;
    padding: 30px;
    border-radius: 25px;
    background: #E8F5EB
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    font-family: "Poppins", sans-serif;
}

/* ----------- TITRE DU QUIZ ----------- */
.welcome-card h1{
    text-align:center;
    font-size:32px;
    font-weight:700;
    color:#FFEB3B;
    margin-bottom:20px;
}

/* ----------- INPUT ÉTUDIANT ----------- */
#student-info{
    padding:20px;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,0.18);
}

#student-info label{
    font-weight:600;
    font-size:18px;
    display:block;
    margin-bottom:10px;
}

#student_name{
    width:100%;
    padding:12px 15px;
    font-size:16px;
    border-radius:12px;
    border:2px solid #ccc;
    outline:none;
    margin-bottom:12px;
}

/* ----------- BOUTON START ----------- */
#startQuiz{
    padding:12px 25px;
    background:#4CAF50;
    color:#fff;
    font-size:17px;
    font-weight:600;
    border:none;
    border-radius:14px;
    cursor:pointer;
    transition:.2s;
}
#startQuiz:hover{
    transform:scale(1.05);
    background:#4CAF50;
}

/* ----------- QUIZ FORM ----------- */
#quizForm{
    background:#e8f5e8;
    padding:25px;
    margin-top:20px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.18);
}

/* ----------- QUESTION ----------- */
#quiz-container h3{
    font-size:22px;
    font-weight:700;
    margin-bottom:10px;
    background:#ffeb3b;
    padding:15px;
    border-radius:15px;
    color:white;
}

#quiz-container p{
    font-size:18px;
    margin-bottom:12px;
}

/* ----------- RÉPONSES ----------- */
#quiz-container label{
    display:block;
    background:#f5f5f5;
    padding:12px 15px;
    border-radius:12px;
    margin-bottom:10px;
    cursor:pointer;
    border:2px solid transparent;
    transition:.2s;
}

#quiz-container input[type="radio"]{
    transform:scale(1.25);
    margin-right:8px;
}

#quiz-container label:hover{
    background:#eceaff;
    border-color:#6739ff;
}

/* ----------- BOUTONS PRECEDENT / SUIVANT ----------- */
#prevBtn,#nextBtn{
    padding:10px 20px;
    font-size:16px;
    font-weight:600;
    border:none;
    border-radius:14px;
    cursor:pointer;
    margin-right:10px;
    transition:.2s;
}

#prevBtn{
    background:#ccc;
    color:#333;
}
#prevBtn:hover{
    background:#b4b4b4;
}

#nextBtn{
    background:#4caf50;
    color:white;
}
#nextBtn:hover{
    background:#3d9141;
}

/* ----------- BOUTON TERMINER ----------- */
#quizForm button[type="submit"]{
    display:block;
    width:100%;
    padding:15px;
    font-size:18px;
    font-weight:700;
    border:none;
    border-radius:18px;
    margin-top:20px;
    background:#ffeb3b;
    color:white;
    cursor:pointer;
    transition:.2s;
}

#quizForm button[type="submit"]:hover{
    transform:scale(1.03);
    background:#e58800;
}

/* ----------- TIMER ----------- */
#timer{
    margin-top:15px;
    font-size:19px;
    text-align:center;
    color:#111;
}

/* ----------- RESPONSIVE ----------- */
@media(max-width:600px){
    .welcome-card{padding:20px;}
    #quiz-container h3{font-size:18px;}
    #quiz-container p{font-size:15px;}
}
</style>
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
