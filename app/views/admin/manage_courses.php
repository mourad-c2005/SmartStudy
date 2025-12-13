<h1 style="text-align:center; color:#2e7d32;">Gestion des cours</h1>

<div style="text-align:center; margin:20px 0;">
    <a href="<?= URLROOT ?>/Cours/add" class="btn-start">+ Ajouter un cours</a>
</div>

<div style="max-width:1000px; margin:auto;">
    <table style="width:100%; border-collapse:collapse; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        <thead style="background:#4CAF50; color:#fff;">
            <tr>
                <th style="padding:12px;">Chapitre</th>
                <th style="padding:12px;">Titre du cours</th>
                <th style="padding:12px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['cours'] as $c): ?>
            <tr style="border-bottom:1px solid #ddd;">
                <td style="padding:10px;"><?= htmlspecialchars($c['chapitre_titre']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($c['titre']) ?></td>
                <td style="padding:10px;">
                    <a href="<?= URLROOT ?>/Cours/edit/<?= $c['id'] ?>" style="color:#ff9800; font-weight:bold;">✏ Modifier</a> |
                    <a href="<?= URLROOT ?>/Cours/delete/<?= $c['id'] ?>" onclick="return confirm('Supprimer ce cours ?');" style="color:#e53935; font-weight:bold;">❌ Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
