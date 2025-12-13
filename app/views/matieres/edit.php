<!-- app/views/matieres/edit.php -->
<h2>Modifier la matière</h2>

<form id="editMatiereForm" method="POST">
  <label>Nom</label>
  <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($data['matiere']['nom']) ?>" style="width:100%;margin-bottom:10px;"><br>

  <label>Description</label>
  <textarea id="description" name="description" style="width:100%;height:100px;margin-bottom:10px;"><?= htmlspecialchars($data['matiere']['description']) ?></textarea><br>

  <div id="errorZone" class="error-msg"></div>

  <button class="btn-start" type="submit">Mettre à jour</button>
</form>

<script>
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
</script>

<style>
.error-msg { color:red; font-weight:bold; margin-bottom:10px; }
button.btn-start { padding:8px 15px; background:#4CAF50; color:#fff; border:none; border-radius:4px; cursor:pointer; }
button.btn-start:hover { background:#388E3C; }
</style>
