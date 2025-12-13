<?php
// app/views/cours/show.php
// $data['cours'] contient le cours complet
$c = $data['cours'];
?>

<div class="welcome-card" style="max-width:950px; margin:auto;">

    <h1 style="color:#2e7d32; margin-bottom:20px;">
        <?= htmlspecialchars($c['titre']) ?>
    </h1>

    <!-- Contenu du cours -->
    <div class="cours-text" style="
        background:#f9f9f9;
        border-left:4px solid #4CAF50;
        padding:20px;
        border-radius:12px;
        box-shadow:0 2px 10px rgba(0,0,0,0.05);
        line-height:1.7;
        font-size:1.05rem;
        color:#444;
    ">
        <?= nl2br(htmlspecialchars($c['contenu'])) ?>
    </div>

    <!-- PDF si existe -->
    <?php if (!empty($c['pdf'])): ?>
    <div style="margin-top:25px;">
        <h3 style="color:#2e7d32;">📄 Document PDF</h3>
        <iframe src="<?= URLROOT ?>/uploads/pdf/<?= htmlspecialchars($c['pdf']) ?>"
                style="width:100%; height:450px; border-radius:10px; border:1px solid #ddd;">
        </iframe>
    </div>
    <?php endif; ?>

    <!-- Video intégrée -->
    <?php if (!empty($c['lien_video'])): ?>
    <div style="margin-top:30px;">
        <h3 style="color:#2e7d32;">🎥 Vidéo explicative</h3>

        <?php
            // Préparer une URL d'intégration pour YouTube ou Vimeo
            $raw = trim($c['lien_video']);
            $embedUrl = '';

            // YouTube patterns
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{6,})/i', $raw, $m)) {
                $id = $m[1];
                $embedUrl = 'https://www.youtube.com/embed/' . $id;
            } elseif (preg_match('/youtube\.com\/embed\/([A-Za-z0-9_-]{6,})/i', $raw, $m)) {
                $embedUrl = $raw; // déjà embed
            }

            // Vimeo pattern
            if (!$embedUrl && preg_match('/vimeo\.com\/(?:channels\/[A-Za-z0-9_]+\/|groups\/[A-Za-z0-9_]+\/videos\/)?([0-9]+)/i', $raw, $m)) {
                $id = $m[1];
                $embedUrl = 'https://player.vimeo.com/video/' . $id;
            }

            // Si l'URL donnée est déjà une URL complète et non identifiée, essayer de l'utiliser telle quelle
            if (!$embedUrl && preg_match('/^https?:\/\//i', $raw)) {
                $embedUrl = $raw;
            }
        ?>

        <div style="
            position:relative;
            padding-bottom:56.25%;
            height:0;
            overflow:hidden;
            border-radius:14px;
            box-shadow:0 3px 12px rgba(0,0,0,0.1);
            margin-top:10px;
        ">
            <?php if ($embedUrl): ?>
                <iframe
                    src="<?= htmlspecialchars($embedUrl) ?>"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    style="
                        position:absolute;
                        top:0; left:0;
                        width:100%; height:100%;
                    ">
                </iframe>
            <?php else: ?>
                <div style="padding:30px; text-align:center; color:#666;">
                    <p>Impossible d'intégrer cette vidéo automatiquement.</p>
                    <p>Voici le lien : <a href="<?= htmlspecialchars($raw) ?>" target="_blank" rel="noopener">Ouvrir la vidéo</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- QUIZ liés à ce cours (avec jointure) -->
    <?php if (!empty($data['quiz'])): ?>
    <div style="margin-top:40px;">
        <h3 style="color:#2e7d32; margin-bottom:15px;">📝 Quiz liés à ce cours</h3>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#e8f5e9; text-align:left;">
                    <th style="padding:10px; border-bottom:2px solid #ccc;">Question</th>
                    <th style="padding:10px; border-bottom:2px solid #ccc;">Cours</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($data['quiz'] as $q): ?>
                    <tr style="border-bottom:1px solid #ddd;">
                        <td style="padding:10px;">
                            <?= htmlspecialchars($q['question']) ?>
                        </td>

                        <!-- Jointure : afficher le titre du cours (fallback si clé absente) -->
                        <td style="padding:10px;">
                            <?= htmlspecialchars(isset($q['cours_titre']) ? $q['cours_titre'] : ($c['titre'] ?? 'Cours')) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Bouton Quiz -->
    <div style="text-align:center; margin-top:35px;">
        <a href="<?= URLROOT ?>/Quiz/index/<?= urlencode($c['id_chapitre']) ?>" 
           class="btn-start" 
           style="padding:12px 25px; border-radius:25px; font-size:1.1rem;">
            📝 Faire le Quiz
        </a>
    </div>

</div>

</main>
<script src="<?= URLROOT ?>/public/js/script.js"></script>
</body>
</html>
