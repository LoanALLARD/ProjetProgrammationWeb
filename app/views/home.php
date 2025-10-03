<body>
    <div class="page-wrapper">
        <?php require __DIR__ . '/layouts/header.php'; ?>

        <main class="content">
            <h2>Mes notes</h2>
            <button onclick="window.location.href='index.php?url=home/showAddForm&action=add'">Ajouter une note</button>
            <button onclick="window.location.href='index.php?url=home/deleteNote'">Supprimer une note</button>
            
            <?php if (!empty($showForm)) : ?>
                <form method="POST" action="index.php?url=home/addNote">
                    <input type="text" name="titre" placeholder="Titre de la note" required>
                    <textarea name="contenu" placeholder="Écris ta note ici..." required></textarea>
                    <button type="submit">Enregistrer</button>
                </form>
            <?php endif; ?>

            <p>Voici ma page d'accueil</p>
        </main>

        <?php require __DIR__ . '/layouts/footer.php' ?>
    </div>
</body>
