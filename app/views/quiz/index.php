<?php
// app/views/quiz/index.php
// $data['chapitre'], $data['questions']
$questions = $data['questions']; // array of rows
?>
<div class="welcome-card">
  <h1>Quiz : <?= htmlspecialchars($data['chapitre']['titre']) ?></h1>
  <div id="quiz-container"></div>
  <div id="quiz-controls" style="margin-top:20px;">
    <button class="btn-start" id="prevBtn" onclick="prevQuestion()">Précédent</button>
    <button class="btn-start" id="nextBtn" onclick="nextQuestion()">Suivant</button>
    <button class="btn-start" id="finishBtn" onclick="finishQuiz()" style="display:none;">Terminer</button>
  </div>
  <p id="result" style="margin-top:15px;font-weight:600"></p>
</div>

<script>
  // construire tableau JS des questions depuis PHP (sécurisé)
  const questions = <?= json_encode(array_map(function($q){
      return [
        'id' => (int)$q['id'],
        'question' => $q['question'],
        'reponses' => [$q['rep1'],$q['rep2'],$q['rep3'],$q['rep4']],
        'correcte' => (int)$q['correcte']
      ];
  }, $questions), JSON_UNESCAPED_UNICODE) ?>;

  // initialisation du quiz (20 questions attendues)
  let idx = 0;
  let answers = new Array(questions.length).fill(null);
  function render() {
    if (!questions.length) {
      document.getElementById('quiz-container').innerHTML = '<p>Aucune question.</p>';
      return;
    }
    const q = questions[idx];
    const html = `<h3>Question ${idx+1} / ${questions.length}</h3>
      <p>${q.question}</p>
      <div class="answers">
        ${q.reponses.map((r,i)=>`<button class="ans-btn" data-i="${i}" onclick="select(${i})">${r}</button>`).join('<br>')}
      </div>
      <p style="margin-top:8px;">Ta réponse: ${answers[idx]===null?'-':(answers[idx]+1)}</p>
    `;
    document.getElementById('quiz-container').innerHTML = html;
    // highlight selected
    const sel = answers[idx];
    if (sel !== null) {
      const btns = document.querySelectorAll('.ans-btn');
      btns.forEach(b=>{ if (parseInt(b.dataset.i) === sel) b.style.background='#cfe8d8'; });
    }
    // controls
    document.getElementById('prevBtn').style.display = idx===0 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').style.display = idx===questions.length-1 ? 'none' : 'inline-block';
    document.getElementById('finishBtn').style.display = idx===questions.length-1 ? 'inline-block' : 'none';
    document.getElementById('result').innerText = '';
  }
  function select(i) {
    answers[idx] = i;
    render();
  }
  function nextQuestion(){ if (idx < questions.length-1) idx++; render(); }
  function prevQuestion(){ if (idx > 0) idx--; render(); }
  function finishQuiz(){
    let score = 0;
    for (let i=0;i<questions.length;i++){
      if (answers[i] === null) continue;
      if (answers[i] === questions[i].correcte) score++;
    }
    document.getElementById('result').innerText = `Score: ${score} / ${questions.length}`;
    // display correct/incorrect summary
    window.scrollTo(0,0);
  }
  // start
  render();
</script>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
