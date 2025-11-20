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

        <div style="
            position:relative;
            padding-bottom:56.25%;
            height:0;
            overflow:hidden;
            border-radius:14px;
            box-shadow:0 3px 12px rgba(0,0,0,0.1);
            margin-top:10px;
        ">
            <iframe 
                src="<?= htmlspecialchars($c['lien_video']) ?>" 
                frameborder="0" 
                allowfullscreen
                style="
                    position:absolute;
                    top:0; left:0;
                    width:100%; height:100%;
                ">
            </iframe>
        </div>
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
