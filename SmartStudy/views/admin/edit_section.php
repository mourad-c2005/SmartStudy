<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="views/admin/assets/admin.css">
</head>
<body>

<h1>Modifier la section</h1>

<form method="post">
    <label>Nom :</label>
    <input type="text" name="nom" value="<?= $section['nom']; ?>" required>

    <button type="submit">Modifier</button>
</form>

</body>
</html>
