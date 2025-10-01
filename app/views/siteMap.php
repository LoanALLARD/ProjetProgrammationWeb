<?php require __DIR__ . '/layouts/header.php'; ?>

<div id="sitemapContainer">
    <p class="pageTitle">Plan du site</p>
    <ul id="sitemapList">
        <?php if (isset($pages)) {
            foreach ($pages as $page): ?>
                <li class="sitemapItem"><a class="sitemapLink" href="<?= $page['url']; ?>"><?= $page['title']; ?></a></li>
            <?php endforeach;
        } ?>
    </ul>
</div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
