<div class="welcome-card">
  <h1>Quiz du chapitre : <?= htmlspecialchars($data['chapitre']['titre']) ?></h1>

  <h3>Temps : <span id="timer">00:00</span></h3>

  <div id="quiz-container"></div>

  <button class="btn-start" id="validateBtn" onclick="finishQuiz()" style="display:none;">
      Terminer et calculer score
  </button>

  <p id="result"></p>
</div>

<script>
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
</script>
