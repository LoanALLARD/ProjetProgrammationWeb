<?php require __DIR__ . '/layouts/header.php'; ?>

    <main class="content">
        <div class="notes-header">
            <h2 class="pageTitle">Mes notes</h2>
            <button class="btn-primary" onclick="window.location.href='index.php?url=home/showAddForm&action=add'">Ajouter une note</button>
        </div>

        <!-- Error and success messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Add form -->
        <?php if (!empty($showForm)) : ?>
            <form method="POST" action="index.php?url=home/addNote" class="note-form">
                <p>Nouvelle note</p>
                <input type="text" name="titre" placeholder="Titre de la note" required>
                <textarea name="contenu" placeholder="Écris ta note ici..." rows="6" required></textarea>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='index.php?url=home/index'">Annuler</button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Change form -->
        <?php if (!empty($showEditForm) && !empty($noteToEdit)) : ?>
            <form method="POST" action="index.php?url=home/modifyNote" class="note-form">
                <p>Modifier la note</p>
                <input type="hidden" name="id" value="<?= htmlspecialchars($noteToEdit['id']) ?>">
                <input type="text" name="titre" value="<?= htmlspecialchars($noteToEdit['titre']) ?>" required>
                <textarea name="contenu" rows="6" required><?= htmlspecialchars($noteToEdit['contenu']) ?></textarea>
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Mettre à jour</button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='index.php?url=home/index'">Annuler</button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Delete button -->
        <div class="notes-container">
            <?php if (!empty($notes) && is_array($notes)) : ?>
                <?php foreach($notes as $note) : ?>
                    <article class="note-card">
                        <div class="note-content">
                            <h3 class="note-title"><?= htmlspecialchars($note['titre']) ?></h3>
                            <p class="note-text"><?= nl2br(htmlspecialchars($note['contenu'])) ?></p>
                        </div>
                        <div class="note-actions">
                            <button class="btn-edit" onclick="window.location.href='index.php?url=home/modifyNote&id=<?= htmlspecialchars($note['id']) ?>'"><i class="fa-solid fa-file-pen"></i></button>
                            <form method="POST" action="index.php?url=home/deleteNote" class="delete-form">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($note['id']) ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?')"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state">
                    <p>Aucune note à afficher.</p>
                    <p class="empty-subtitle">Commencez par créer votre première note !</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

<?php require __DIR__ . '/layouts/footer.php' ?>