<?php require __DIR__ . '/layouts/header.php'; ?>

<main class="content">
    <p class="pageTitle">Mot de passe oublié</p>

    <!-- Error messages -->
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>
    
    <!-- success messages -->
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?url=forgottenpassword/changePassword">
        <p>Veuillez saisir votre adresse mail afin de recevoir un mail de récupération.</p>
        <input type="email" name="email" required placeholder="votre@email.com">
        <br><br>
        <input type="submit" value="Envoyer">
    </form>
</main>

<?php require __DIR__ . '/layouts/footer.php'; ?>