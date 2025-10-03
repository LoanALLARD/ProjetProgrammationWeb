<?php require __DIR__ . '/layouts/header.php'; ?>

<main class="content">
    <p class="pageTitle">Inscription</p>

    <form method="POST" action="/index.php?url=register/register">

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
        <input type="text" name="identifiant" required placeholder="VotreIdentifiant">

        <p>Adresse mail</p>
        <input type="email" name="email" required placeholder="votre@email.com">

        <p>Téléphone</p>
        <input type="tel" name="telephone" placeholder="0607080910">

        <p>Mot de passe</p>
        <input type="password" name="password" required  placeholder="**********">

        <p>Confirmation du Mot de passe</p>
        <input type="password" name="passwordConfirmation" required  placeholder="**********">

        <input type="submit" value="S'inscrire">
        <a href="/index.php?url=login/index">Déjà un compte ?</a>
    </form>
</main>

<?php require __DIR__ . '/layouts/footer.php'; ?>