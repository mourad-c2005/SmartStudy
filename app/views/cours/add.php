<!-- app/views/cours/add.php -->
<h2>Ajouter un nouveau cours</h2>

<form id="addCoursForm" method="POST">
    <h3>Informations du cours</h3>
    <input type="text" id="titre" name="titre" placeholder="Titre du cours" style="width:100%;margin-bottom:10px;"><br>
    <textarea id="contenu" name="contenu" placeholder="Contenu du cours" style="width:100%;height:150px;margin-bottom:10px;"></textarea><br>
    <input type="text" id="lien_video" name="lien_video" placeholder="Lien vidéo" style="width:100%;margin-bottom:10px;"><br>

    <div id="errorZone" class="error-msg"></div>

    <h3>Quiz (questions spécifiques à ce cours)</h3>
    <div id="quiz-container"></div>
    <button type="button" onclick="addQuestion()">Ajouter une question</button><br><br>

    <button type="submit">Ajouter le cours et les quiz</button>
</form>

<script>
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
</script>

<style>
.btn-start, button { padding:10px 20px; background:#4CAF50; color:#fff; border:none; border-radius:5px; cursor:pointer; }
.btn-start:hover, button:hover { background:#388E3C; }
.error-msg { color:red; margin-top:10px; font-weight:bold; }
.quiz-item { margin-bottom:15px; padding:10px; border-radius:5px; }
</style>
