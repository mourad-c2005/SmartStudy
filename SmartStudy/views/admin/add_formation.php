<form method="POST">
    Titre : <input type="text" name="titre"><br>
    URL : <input type="text" name="url"><br>
    Prix : <input type="number" step="0.01" name="prix"><br>
    Catégorie :
    <select name="id_categorie">
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['nom'] ?></option>
        <?php endforeach ?>
    </select>

    <button type="submit">Ajouter</button>
</form>
