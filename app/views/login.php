<?php require __DIR__ . '/layouts/header.php'; ?>

<main class="content">
    <p class="pageTitle">Connexion</p>

    <form method="POST" action="/index.php?url=login/login">

        <!-- Error and success messages -->
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($successMessage)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <p>Identifiant</p>
        <input type="text" name="identifiant" required>

        <p>Mot de passe</p>
        <input type="password" name="password" required>

        <input type="submit" value="Connexion">

        <a href="/index.php?url=register/index">Vous n'avez pas de compte ?</a>
        <a href="/index.php?url=forgottenpassword/index">Mot de passe oublié ?</a>
    </form>
</main>

<?php require __DIR__ . '/layouts/footer.php'; ?>