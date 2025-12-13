<div class="welcome-card">
    <h1>Quiz : <?= htmlspecialchars($data['cours']['titre']) ?></h1>

    <div id="student-info">
        <?php 
        // Récupérer le nom de l'utilisateur connecté
        $studentName = '';
        if (isset($_SESSION['user']['nom'])) {
            $studentName = htmlspecialchars($_SESSION['user']['nom']);
        } elseif (isset($_SESSION['admin_name'])) {
            $studentName = htmlspecialchars($_SESSION['admin_name']);
        }
        ?>
        
        <label>Nom et prénom :</label>
        <?php if (!empty($studentName)): ?>
            <!-- Nom pré-rempli depuis la session -->
            <input type="text" id="student_name" value="<?= $studentName ?>" readonly 
                   style="background-color:#f5f5f5; cursor:not-allowed;">
            <p style="color:#4CAF50; font-size:14px; margin-top:5px;">
                <i class="fas fa-check-circle"></i> Connecté en tant que <?= $studentName ?>
            </p>
        <?php else: ?>
            <!-- Champ vide si pas connecté -->
            <input type="text" id="student_name" placeholder="Votre nom et prénom" required>
            <p style="color:#777; font-size:14px; margin-top:5px;">
                <i class="fas fa-info-circle"></i> Veuillez entrer votre nom
            </p>
        <?php endif; ?>

        <button id="startQuiz">Commencer le quiz</button>
    </div>

    <form id="quizForm" method="POST" action="<?= URLROOT ?>/Quiz/submit/<?= $data['cours']['id'] ?>" style="display:none;">
        <div id="quiz-container"></div>

        <!-- BOUTON AUDIO -->
        <button type="button" id="btnAudio" onclick="lireQuestion()" style="
            margin-top:15px;
            padding:10px 18px;
            background:#2196f3;
            color:white;
            border:none;
            border-radius:14px;
            cursor:pointer;
            font-weight:600;
        ">🔊 Écouter la question</button>

        <div id="quiz-controls" style="margin-top:15px;">
            <button type="button" id="prevBtn" onclick="prevQuestion()" style="display:none;">Précédent</button>
            <button type="button" id="nextBtn" onclick="nextQuestion()">Suivant</button>
        </div>

        <input type="hidden" name="time_seconds" id="time_seconds" value="0">
        <input type="hidden" name="student_name" id="hidden_student_name" 
               value="<?= !empty($studentName) ? $studentName : '' ?>">
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
    background: #E8F5EB;
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    font-family: "Poppins", sans-serif;
}

.welcome-card h1{
    text-align:center;
    font-size:32px;
    font-weight:700;
    color:#2e7d32;
    margin-bottom:20px;
}

#student-info{
    padding:20px;
    border-radius:18px;
    background:white;
    box-shadow:0 5px 20px rgba(0,0,0,0.18);
}

#student-info label{
    font-weight:600;
    font-size:18px;
    display:block;
    margin-bottom:10px;
    color:#333;
}

#student_name{
    width:100%;
    padding:12px 15px;
    font-size:16px;
    border-radius:12px;
    border:2px solid #4CAF50;
    outline:none;
    margin-bottom:12px;
    box-sizing:border-box;
}

#student_name:read-only {
    background-color:#f0f9f0;
    border-color:#81C784;
    color:#2e7d32;
}

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
    display:block;
    width:100%;
    margin-top:15px;
}

#startQuiz:hover{
    transform:scale(1.05);
    background:#388E3C;
}

#quizForm{
    background:#e8f5e8;
    padding:25px;
    margin-top:20px;
    border-radius:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.18);
}

#quiz-container h3{
    font-size:22px;
    font-weight:700;
    margin-bottom:10px;
    background:#4CAF50;
    padding:15px;
    border-radius:15px;
    color:white;
}

#quiz-container p{
    font-size:18px;
    margin-bottom:12px;
    line-height:1.5;
}

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

#quiz-container label:hover{
    background:#e8f5e9;
    border-color:#4CAF50;
}

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
    background:#bdbdbd;
}

#nextBtn{
    background:#4caf50;
    color:white;
}

#nextBtn:hover{
    background:#388E3C;
}

#quizForm button[type="submit"]{
    display:block;
    width:100%;
    padding:15px;
    font-size:18px;
    font-weight:700;
    border:none;
    border-radius:18px;
    margin-top:20px;
    background:#FF9800;
    color:white;
    cursor:pointer;
    transition:.2s;
}

#quizForm button[type="submit"]:hover{
    transform:scale(1.03);
    background:#F57C00;
}

#timer{
    margin-top:15px;
    font-size:19px;
    text-align:center;
    color:#2e7d32;
    font-weight:bold;
}

.user-connected {
    background:#E8F5E9;
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
    border-left:4px solid #4CAF50;
}

.user-connected i {
    color:#4CAF50;
    margin-right:8px;
}
</style>

<script>
// ===================== DONNÉES QUESTIONS ========================

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

// ===================== DEMARRER LE QUIZ ========================
document.getElementById('startQuiz').addEventListener('click', () => {
    const nameInput = document.getElementById('student_name');
    const name = nameInput.value.trim();
    
    // Si le champ est en readonly (nom de session), utiliser cette valeur
    const sessionName = document.getElementById('hidden_student_name').value;
    
    let finalName = name;
    if (nameInput.hasAttribute('readonly')) {
        finalName = sessionName;
    }
    
    if (!finalName) { 
        alert("Veuillez saisir votre nom et prénom !"); 
        return; 
    }
    
    // S'assurer que le nom caché a la bonne valeur
    document.getElementById('hidden_student_name').value = finalName;
    
    document.getElementById('student-info').style.display = 'none';
    quizForm.style.display = 'block';
    startTimer();
    render();
});

// ===================== TIMER ========================
function startTimer() {
    timerInterval = setInterval(() => {
        seconds++;
        document.getElementById('time_seconds').value = seconds;
        document.getElementById('timer').innerText = `Temps écoulé: ${seconds} secondes`;
    }, 1000);
}

// ===================== AFFICHER QUESTION ========================
function render() {
    if (!questions.length) { 
        quizContainer.innerHTML = '<p>Aucune question disponible.</p>'; 
        return; 
    }

    const q = questions[idx];

    let html = `<h3>Question ${idx+1} / ${questions.length}</h3>
                <p id='questionText'>${q.question}</p>
                <div>`;

    q.reponses.forEach((r,i) => {
        html += `<label>
                    <input type="radio" name="q${idx}" value="${i}" 
                     ${answers[idx]===i?'checked':''} onchange="selectAnswer(${i})">
                     <strong>${String.fromCharCode(65 + i)}.</strong> ${r}
                 </label>`;
    });

    html += `</div>`;
    quizContainer.innerHTML = html;

    prevBtn.style.display = idx === 0 ? 'none' : 'inline-block';
    nextBtn.style.display = idx === questions.length-1 ? 'none' : 'inline-block';
}

// ===================== AUDIO : LECTURE QUESTION ========================
function lireQuestion() {
    const q = questions[idx];

    // Texte complet : question + réponses
    let text = "Question " + (idx + 1) + " : " + q.question + ". ";
    text += "Réponses : ";
    q.reponses.forEach((r, i) => {
        text += String.fromCharCode(65 + i) + " : " + r + ". ";
    });

    let v = new SpeechSynthesisUtterance(text);
    v.lang = "fr-FR";
    v.rate = 1;
    v.pitch = 1;

    speechSynthesis.cancel();
    speechSynthesis.speak(v);
}

// ===================== NAVIGATION ========================
function selectAnswer(i) { answers[idx] = i; }
function nextQuestion() { if(idx < questions.length-1) { idx++; render(); } }
function prevQuestion() { if(idx > 0) { idx--; render(); } }

// ===================== ENVOI FINAL ========================
quizForm.addEventListener('submit', function(e){
    e.preventDefault();
    
    const studentName = document.getElementById('hidden_student_name').value.trim();
    if (!studentName) {
        alert("Nom et prénom obligatoire !");
        return;
    }
    
    // Vérifier si toutes les questions ont été répondues
    const unanswered = answers.filter(a => a === null).length;
    if (unanswered > 0) {
        if (!confirm(`Il reste ${unanswered} question(s) sans réponse. Voulez-vous vraiment terminer le quiz ?`)) {
            return;
        }
    }
    
    clearInterval(timerInterval);
    this.submit();
});

// Initialiser le champ avec le nom de session
window.addEventListener('DOMContentLoaded', function() {
    const sessionName = document.getElementById('hidden_student_name').value;
    if (sessionName) {
        const nameInput = document.getElementById('student_name');
        nameInput.value = sessionName;
        nameInput.setAttribute('readonly', 'readonly');
    }
});
</script>