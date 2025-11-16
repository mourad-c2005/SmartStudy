<?php include 'views/header.php'; ?>
<main class="container-main">
<h1 style="color:var(--green);text-align:center;margin-bottom:1.5rem">Forum & Discussions</h1>
<a href="index.php?controller=forum&action=create" class="btn" style="background:var(--green);color:#fff">Nouveau sujet</a>

<div id="topicsList" style="margin-top:1rem">
<?php foreach($topics as $t): ?>
  <div class="topic-card">
    <a href="index.php?controller=forum&action=view&id=<?= $t['TOPIC_ID'] ?>"><?= htmlspecialchars($t['TITLE']) ?></a>
    <span><?= htmlspecialchars($t['CATEGORY']) ?></span>
    <span>Par <?= htmlspecialchars($t['USER_ID']) ?></span>
  </div>
<?php endforeach; ?>
</div>
</main>
<?php include 'views/footer.php'; ?>
