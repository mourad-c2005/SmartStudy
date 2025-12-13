// script.js
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('addChapitreForm');
    form.addEventListener('submit', function(e){
        const titre = document.getElementById('titre').value.trim();
        const errorZone = document.getElementById('errorZone');
        errorZone.innerText = '';

        if(titre === ''){
            e.preventDefault(); // bloque l'envoi
            alert("Erreur : le titre du chapitre est obligatoire");
            document.getElementById('titre').focus();
        }
    });
});
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const username = document.getElementById('username')?.value.trim();
    const password = document.getElementById('password')?.value.trim();
    const errorBox = document.getElementById('formError');

    if (!errorBox) return; // Sécurité si l'élément n'existe pas

    errorBox.style.display = "none";
    errorBox.innerText = "";

    if (!username || !password) {
        e.preventDefault();
        alert("Veuillez remplir tous les champs.");
        errorBox.style.display = "block";
        errorBox.innerText = "Veuillez remplir tous les champs.";
    }
});


document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('editChapitreForm');
    form.addEventListener('submit', function(e){
        const titre = document.getElementById('titre').value.trim();
        const errorZone = document.getElementById('errorZone');
        errorZone.innerText = '';

        if(titre === ''){
            e.preventDefault(); // bloque l'envoi
            alert ("Erreur : le titre du chapitre est obligatoire ");
            document.getElementById('titre').focus();
        }
    });
});
let quizIndex = 0;

function addQuestion() {
    const container = document.getElementById('quiz-container');
    const html = `
    <div class="quiz-item" style="border:1px solid #ccc;padding:10px;margin-bottom:10px;">
        <label>Question:</label><br>
        <input type="text" name="quiz[${quizIndex}][question]"><br>
        <label>Réponse 1:</label><input type="text" name="quiz[${quizIndex}][rep1]"><br>
        <label>Réponse 2:</label><input type="text" name="quiz[${quizIndex}][rep2]"><br>
        <label>Réponse 3:</label><input type="text" name="quiz[${quizIndex}][rep3]"><br>
        <label>Réponse 4:</label><input type="text" name="quiz[${quizIndex}][rep4]"><br>
        <label>Bonne réponse (1-4):</label><input type="number" name="quiz[${quizIndex}][correcte]" min="1" max="4" value="1"><br>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    quizIndex++;
}

// Validation JS du formulaire
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('addCoursForm');
    form.addEventListener('submit', function(e){
        const errorZone = document.getElementById('errorZone');
        errorZone.innerText = '';

        let titre = document.getElementById('titre').value.trim();
        let contenu = document.getElementById('contenu').value.trim();
        let valid = true;
        let messages = [];

        if(titre === '') { alert("Le titre du cours est obligatoire."); valid = false; }
        if(contenu === '') { alert("Le contenu du cours est obligatoire."); valid = false; }

        // Vérifier les questions du quiz
        const quizItems = document.querySelectorAll('.quiz-item');
        quizItems.forEach((item, index)=>{
            const q = item.querySelector(`input[name="quiz[${index}][question]"]`).value.trim();
            const r1 = item.querySelector(`input[name="quiz[${index}][rep1]"]`).value.trim();
            const r2 = item.querySelector(`input[name="quiz[${index}][rep2]"]`).value.trim();
            const r3 = item.querySelector(`input[name="quiz[${index}][rep3]"]`).value.trim();
            const r4 = item.querySelector(`input[name="quiz[${index}][rep4]"]`).value.trim();
            const correcte = item.querySelector(`input[name="quiz[${index}][correcte]"]`).value;

            if(q === '' || r1 === '' || r2 === '' || r3 === '' || r4 === '' || !['1','2','3','4'].includes(correcte)){
                alert(`Question ${index+1} du quiz incorrecte ou incomplète.`);
                valid = false;
            }
        });

        if(!valid){
            e.preventDefault();
            errorZone.innerHTML = messages.join('<br>');
            window.scrollTo({top:0, behavior:'smooth'});
        }
    });
});



let quizIndex = 0;
const quizContainer = document.getElementById('quiz-container');

// Charger les quiz existants
const existingQuiz = <?= json_encode($data['quizs'], JSON_UNESCAPED_UNICODE) ?>;
existingQuiz.forEach(q => addQuestion(q));

function addQuestion(q = null) {
    const div = document.createElement('div');
    div.className = 'quiz-item';
    div.style.border = "1px solid #ccc";
    div.style.padding = "10px";
    div.style.marginBottom = "10px";

    div.innerHTML = `
        ${q ? `<input type="hidden" name="quiz[${quizIndex}][id]" value="${q.id}">` : ''}
        <label>Question:</label><br>
        <input type="text" name="quiz[${quizIndex}][question]" value="${q ? q.question : ''}"><br>
        <label>Réponse 1:</label><input type="text" name="quiz[${quizIndex}][rep1]" value="${q ? q.rep1 : ''}"><br>
        <label>Réponse 2:</label><input type="text" name="quiz[${quizIndex}][rep2]" value="${q ? q.rep2 : ''}"><br>
        <label>Réponse 3:</label><input type="text" name="quiz[${quizIndex}][rep3]" value="${q ? q.rep3 : ''}"><br>
        <label>Réponse 4:</label><input type="text" name="quiz[${quizIndex}][rep4]" value="${q ? q.rep4 : ''}"><br>
        <label>Bonne réponse (1-4):</label><input type="number" name="quiz[${quizIndex}][correcte]" min="1" max="4" value="${q ? q.correcte : 1}"><br>
        <button type="button" onclick="removeQuestion(this)">Supprimer</button>
    `;
    quizContainer.appendChild(div);
    quizIndex++;
    updateQuizIndices();
}

function removeQuestion(button) {
    button.parentElement.remove();
    updateQuizIndices();
}

function updateQuizIndices() {
    const items = document.querySelectorAll('.quiz-item');
    items.forEach((div, index) => {
        div.querySelectorAll('input, textarea').forEach(input => {
            const name = input.name;
            if(name.startsWith('quiz[')) {
                input.name = name.replace(/quiz\[\d+\]/, `quiz[${index}]`);
            }
        });
    });
    quizIndex = items.length;
}

// Validation JS du formulaire
document.getElementById('editCoursForm').addEventListener('submit', function(e){
    const errorZone = document.getElementById('errorZone');
    errorZone.innerHTML = '';
    let titre = document.getElementById('titre').value.trim();
    let contenu = document.getElementById('contenu').value.trim();
    let valid = true;
    let messages = [];

    if(titre === '') { alert("Le titre du cours est obligatoire."); valid = false; }
    if(contenu === '') { alert("Le contenu du cours est obligatoire."); valid = false; }

    // Vérifier les questions du quiz
    const quizItems = document.querySelectorAll('.quiz-item');
    quizItems.forEach((item, index)=>{
        const q = item.querySelector(`input[name="quiz[${index}][question]"]`).value.trim();
        const r1 = item.querySelector(`input[name="quiz[${index}][rep1]"]`).value.trim();
        const r2 = item.querySelector(`input[name="quiz[${index}][rep2]"]`).value.trim();
        const r3 = item.querySelector(`input[name="quiz[${index}][rep3]"]`).value.trim();
        const r4 = item.querySelector(`input[name="quiz[${index}][rep4]"]`).value.trim();
        const correcte = item.querySelector(`input[name="quiz[${index}][correcte]"]`).value;

        if(q === '' || r1 === '' || r2 === '' || r3 === '' || r4 === '' || !['1','2','3','4'].includes(correcte)){
            alert(`Question ${index+1} du quiz incorrecte ou incomplète.`);
            valid = false;
        }
    });

    if(!valid){
        e.preventDefault();
        errorZone.innerHTML = messages.join('<br>');
        window.scrollTo({top:0, behavior:'smooth'});
    }
});


document.getElementById('addMatiereForm').addEventListener('submit', function(e){
    const errorZone = document.getElementById('errorZone');
    errorZone.innerHTML = '';
    let nom = document.getElementById('nom').value.trim();
    let valid = true;
    let messages = [];

    if(nom === ''){
        alert("Le nom de la matière est obligatoire.");
        valid = false;
    }

    if(!valid){
        e.preventDefault();
        errorZone.innerHTML = messages.join('<br>');
        window.scrollTo({top:0, behavior:'smooth'});
    }
});


document.getElementById('editMatiereForm').addEventListener('submit', function(e){
    const errorZone = document.getElementById('errorZone');
    errorZone.innerHTML = '';
    let nom = document.getElementById('nom').value.trim();
    let valid = true;
    let messages = [];

    if(nom === ''){
        alert("Le nom de la matière est obligatoire.");
        valid = false;
    }

    if(!valid){
        e.preventDefault();
        errorZone.innerHTML = messages.join('<br>');
        window.scrollTo({top:0, behavior:'smooth'});
    }
});
document.getElementById('editMatiereForm').addEventListener('submit', function(e){
    const errorZone = document.getElementById('errorZone');
    errorZone.innerHTML = '';
    let nom = document.getElementById('nom').value.trim();
    let valid = true;
    let messages = [];

    if(nom === ''){
        alert("Le nom de la matière est obligatoire.");
        valid = false;
    }

    if(!valid){
        e.preventDefault();
        errorZone.innerHTML = messages.join('<br>');
        window.scrollTo({top:0, behavior:'smooth'});
    }
});
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


let questions = <?= json_encode($data['questions']); ?>;
let index = 0;
let answers = {};
let total = questions.length;

// TIMER
let time = 0;
let timerInterval = setInterval(() => {
    time++;
    let m = String(Math.floor(time/60)).padStart(2, '0');
    let s = String(time%60).padStart(2, '0');
    document.getElementById("timer").innerHTML = `${m}:${s}`;
}, 1000);

function showQuestion() {
    let q = questions[index];
    let html = `<h3>${index+1}) ${q.question}</h3>`;

    html += q.rep1 ? `<button onclick="select(1)">${q.rep1}</button><br>` : '';
    html += q.rep2 ? `<button onclick="select(2)">${q.rep2}</button><br>` : '';
    html += q.rep3 ? `<button onclick="select(3)">${q.rep3}</button><br>` : '';
    html += q.rep4 ? `<button onclick="select(4)">${q.rep4}</button><br>` : '';

    document.getElementById("quiz-container").innerHTML = html;
}
function select(r) {
    answers[index] = r;
    index++;

    if (index < total) {
        showQuestion();
    } else {
        document.getElementById("quiz-container").innerHTML = "<h3>Quiz terminé</h3>";
        document.getElementById("validateBtn").style.display = "block";
        clearInterval(timerInterval);
    }
}
function finishQuiz() {
    let score = 0;
    for (let i = 0; i < total; i++) {
        if (answers[i] == questions[i].correcte) score++;
    }
    document.getElementById("result").innerHTML =
        "Score : " + score + " / " + total + "<br>Temps : " + document.getElementById("timer").innerHTML;
}
showQuestion();

      document.getElementById('loginForm').addEventListener('submit', function(e){
          const username = document.getElementById('username').value.trim();
          const password = document.getElementById('password').value.trim();
          const errorBox = document.getElementById('formError');

          errorBox.style.display = "none";
          errorBox.innerText = "";

          if(username === "" || password === ""){
              e.preventDefault();
              alert("Veuillez remplir tous les champs.");
              errorBox.style.display = "block";
          }
      });
