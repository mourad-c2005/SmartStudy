<?php include 'views/header.php'; ?>
<main class="container-main">
<h3><?= htmlspecialchars($topic['TITLE']) ?></h3>
<p><?= htmlspecialchars($topic['CONTENT']) ?></p>
<p>Par <?= htmlspecialchars($topic['USER_ID']) ?> • <?= $topic['CATEGORY'] ?> • <?= $topic['CREATED_AT'] ?></p>

<h5>Réponses</h5>
<?php foreach($replies as $r): ?>
<div class="reply-card">
  <p><?= htmlspecialchars($r['CONTENT']) ?></p>
  <small><?= $r['USER_ID'] ?> • <?= $r['CREATED_AT'] ?></small>
</div>
<?php endforeach; ?>

<h5>Ajouter une réponse</h5>
<form method="post" action="index.php?controller=forum&action=reply&id=<?= $topic['TOPIC_ID'] ?>">
<textarea name="content" required></textarea>
<button>Publier</button>
</form>
</main>
<?php include 'views/footer.php'; ?>
