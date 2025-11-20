
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('addChapitreForm');
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