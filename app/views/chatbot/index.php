<!-- app/views/chatbot/index.php -->
<h2>Chatbot - Résumé de cours</h2>

<div style="display:flex;gap:20px;align-items:flex-start;">
    <div style="flex:1;min-width:300px;">
        <label for="coursSelect">Sélectionner un cours</label><br>
        <select id="coursSelect" style="width:100%;margin-bottom:10px;">
            <option value="">-- Choisir --</option>
            <?php foreach($data['courses'] as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['titre']) ?></option>
            <?php endforeach; ?>
        </select>

        <button id="loadCoursBtn" class="btn-start">Charger le cours</button>
        <button id="summarizeBtn" class="btn-start" style="margin-left:10px;">Générer résumé</button>

        <div id="status" style="margin-top:10px;color:#555;"></div>
    </div>

    <div style="flex:2;min-width:400px;">
        <h3>Contenu du cours</h3>
        <div id="coursContent" style="white-space:pre-wrap;padding:10px;border:1px solid #ddd;border-radius:4px;min-height:150px;background:#fafafa;"></div>

        <h3 style="margin-top:15px;">Résumé</h3>
        <div id="summary" style="white-space:pre-wrap;padding:10px;border:1px solid #ddd;border-radius:4px;min-height:100px;background:#fff;"></div>
    </div>
</div>

<script src="<?= URLROOT ?>/public/js/chatbot.js"></script>

<style>
/* small styles for buttons already present in layout; keep coherent */
.btn-start { padding:8px 14px; background:#1976D2; color:#fff; border-radius:4px; border:none; cursor:pointer }
.btn-start:hover { background:#115293 }
</style>
