<h1 style="text-align:center; color:#2e7d32;">Résultats des étudiants</h1>

<div style="max-width:1000px; margin:auto; margin-top:25px;">
    <table style="width:100%; border-collapse:collapse; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        <thead style="background:#4CAF50; color:#fff;">
            <tr>
                <th style="padding:12px;">Nom</th>
                <th style="padding:12px;">Prénom</th>
                <th style="padding:12px;">Quiz</th>
                <th style="padding:12px;">Score</th>
                <th style="padding:12px;">Temps</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($data['results'] as $r): ?>
            <tr style="border-bottom:1px solid #ddd;">
                <td style="padding:10px;"><?= htmlspecialchars($r['nom']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($r['prenom']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($r['quiz_titre']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($r['score']) ?> / <?= htmlspecialchars($r['total_questions']) ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($r['temps']) ?> s</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
