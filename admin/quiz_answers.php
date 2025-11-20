<h1 style="text-align:center; color:#2e7d32;">Réponses du Quiz : <?= htmlspecialchars($data['quiz']['titre']) ?></h1>

<div style="max-width:950px; margin:auto; margin-top:20px;">
    <?php foreach($data['answers'] as $idx => $q): ?>
    <div style="background:#fff; padding:20px; border-radius:12px; box-shadow:0 3px 12px rgba(0,0,0,0.05); margin-bottom:15px;">
        <h4>Q<?= $idx+1 ?>: <?= htmlspecialchars($q['question']) ?></h4>
        <ul style="list-style:none; padding-left:0; margin-top:10px;">
            <?php foreach($q['options'] as $i => $opt): ?>
            <li style="margin-bottom:6px; font-weight: <?= $i === $q['correcte'] ? 'bold' : 'normal' ?>; color: <?= $i === $q['reponse_etudiant'] ? '#2196F3' : '#333' ?>;">
                <?= htmlspecialchars($opt) ?>
                <?php if($i === $q['correcte']): ?>(✅ Correcte)<?php endif; ?>
                <?php if($i === $q['reponse_etudiant']): ?>(Réponse étudiant)<?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endforeach; ?>
</div>
