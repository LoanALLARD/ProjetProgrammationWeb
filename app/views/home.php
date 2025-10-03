<body>
    <div class="page-wrapper">
        <?php require __DIR__ . '/layouts/header.php'; ?>

        <main class="content">
            <h2>Mes notes</h2>
            <button onclick="window.location.href='index.php?url=home/addNote'">Ajouter une note</button>
            <?php if (!empty($showForm)) : ?>
                <form method="POST" action="index.php?url=home/addNote">
                    <input type="text" name="titre" placeholder="Titre de la note" required>
                    <textarea name="contenu" placeholder="Écris ta note ici..." required></textarea>
                    <button type="submit">Enregistrer</button>
                </form>
            <?php endif; ?>

            <div class="notes-container">
                <?php if (!empty($notes) && is_array($notes)) : ?>
                    <?php foreach($notes as $note) : ?>
                        <article class="note">
                            <h3><?= htmlspecialchars($note['titre']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($note['contenu'])) ?></p>
                            <button onclick="window.location.href='index.php?url=home/deleteNote'">Supprimer <lax></lax> note</button>
                            <button onclick="window.location.href='index.php?url=home/modifyNote'">Modifier la note</button>
                        </article>
                        <hr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>Aucune note à afficher.</p>
                <?php endif; ?>
            </div>
        </main>

        <?php require __DIR__ . '/layouts/footer.php' ?>
    </div>
</body>