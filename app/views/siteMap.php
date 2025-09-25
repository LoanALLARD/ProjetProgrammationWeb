<?php require __DIR__ . '/layouts/header.php'; ?>

<p>Plan du site</p>
<ul>
    <?php foreach ($tabPages as $page): ?>
        <li><a href="<?= $page['url']; ?>"><?= $page['title']; ?></a></li>
    <?php endforeach; ?>
</ul>

<?php require __DIR__ . '/layouts/footer.php'; ?>
