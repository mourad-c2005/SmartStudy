// public/js/chatbot.js
document.addEventListener('DOMContentLoaded', function(){
    const coursSelect = document.getElementById('coursSelect');
    const loadBtn = document.getElementById('loadCoursBtn');
    const summarizeBtn = document.getElementById('summarizeBtn');
    const contentDiv = document.getElementById('coursContent');
    const summaryDiv = document.getElementById('summary');
    const statusDiv = document.getElementById('status');

    let currentText = '';

    loadBtn.addEventListener('click', function(){
        const id = coursSelect.value;
        if (!id) {
            alert('Veuillez sélectionner un cours.');
            return;
        }
        statusDiv.innerText = 'Chargement du cours...';
        fetch(`index.php?url=Chatbot/getCourse/${id}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    currentText = data.cours.contenu || '';
                    contentDiv.innerText = currentText;
                    summaryDiv.innerText = '';
                    statusDiv.innerText = 'Cours chargé.';
                } else {
                    statusDiv.innerText = data.error || 'Erreur';
                }
            })
            .catch(err => { statusDiv.innerText = 'Erreur de chargement'; console.error(err); });
    });

    summarizeBtn.addEventListener('click', function(){
        if (!currentText || currentText.trim() === '') {
            alert('Aucun contenu de cours à résumer. Chargez d’abord un cours ou entrez du texte.');
            return;
        }
        statusDiv.innerText = 'Génération du résumé...';
        fetch('index.php?url=Chatbot/summarize', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text: currentText })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                summaryDiv.innerText = data.summary;
                statusDiv.innerText = 'Résumé généré.';
            } else {
                statusDiv.innerText = data.error || 'Erreur lors du résumé';
            }
        })
        .catch(err => { statusDiv.innerText = 'Erreur réseau'; console.error(err); });
    });
});
