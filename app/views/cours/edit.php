<!-- app/views/cours/edit.php -->
<h2>Modifier le cours : <?= htmlspecialchars($data['cours']['titre']) ?></h2>

<form id="editCoursForm" method="POST">
    <label>Titre :</label><br>
    <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($data['cours']['titre']) ?>" style="width:100%;margin-bottom:10px;"><br>

    <label>Contenu :</label><br>
    <textarea id="contenu" name="contenu" style="width:100%;height:150px;margin-bottom:10px;"><?= htmlspecialchars($data['cours']['contenu']) ?></textarea><br>

    <label>Lien vidéo :</label><br>
    <input type="text" id="lien_video" name="lien_video" value="<?= htmlspecialchars($data['cours']['lien_video']) ?>" style="width:100%;margin-bottom:10px;"><br>

    <div id="errorZone" class="error-msg"></div>

    <h3>Quiz du cours</h3>
    <div id="quiz-container"></div>
    <button type="button" onclick="addQuestion()">Ajouter une question</button><br><br>

    <button type="submit">Mettre à jour le cours et les quiz</button>
</form>

<script>
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
</script>

<style>
.error-msg { color:red; font-weight:bold; margin-bottom:10px; }
.quiz-item { margin-bottom:15px; padding:10px; border-radius:5px; border:1px solid #ccc; }
button { padding:8px 15px; background:#4CAF50; color:#fff; border:none; border-radius:4px; cursor:pointer; }
button:hover { background:#388E3C; }
</style>
