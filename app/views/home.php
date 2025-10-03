<?php
require __DIR__ . '/layouts/header.php'; 
?>

<?php if (!empty($notes)): ?>
    <?php foreach ($notes as $note): ?>
        <h3><?= htmlspecialchars($note['TITRE']) ?></h3>
        <p><?= nl2br(htmlspecialchars($note['CONTENU'])) ?></p>
        <hr>
    <?php endforeach; ?>
<?php else: ?>
    <p>Vous n'avez pas de note.</p>
<?php endif; ?>

<?php require __DIR__ . '/layouts/footer.php'; ?>
