<!-- app/views/chapitres/edit.php -->
<?php
// $data['chapitre'] fourni par le contrôleur
?>

<h2>Modifier Chapitre</h2>

<form id="editChapitreForm" method="POST">
    <div class="form-group">
        <label for="titre">Titre</label>
        <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($data['chapitre']['titre']) ?>">
    </div>

    <div id="errorZone" class="error-msg"></div>

    <button type="submit" class="btn-start">Mettre à jour</button>
</form>
<div>
 <script>
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
 </script></div>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
<style>
.btn-start { padding:10px 20px; background:#4CAF50; color:#fff; border:none; border-radius:5px; cursor:pointer; }
.btn-start:hover { background:#388E3C; }
.error-msg { color:red; margin-top:10px; font-weight:bold; }
.form-group { margin-bottom:15px; }
</style>
