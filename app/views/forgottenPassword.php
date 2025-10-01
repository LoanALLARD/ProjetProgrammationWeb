<?php require __DIR__ . '/layouts/header.php'; ?>

    <p class="pageTitle">Mot de passe oublié</p>

<?php if (isset($errorMessage)): ?>
    <div style="color:red; text-align: center; margin: 10px 0;">
        <?= htmlspecialchars($errorMessage) ?>
    </div>
<?php endif; ?>

<?php if (isset($successMessage)): ?>
    <div style="color:green; text-align: center; margin: 10px 0;">
        <?= htmlspecialchars($successMessage) ?>
    </div>
<?php endif; ?>

    <form method="POST" action="index.php?url=forgottenpassword/changePassword">
        <p>Veuillez saisir votre adresse mail afin de recevoir un mail de récupération.</p>
        <input type="email" name="email" required placeholder="votre@email.com">
        <input type="submit" value="Envoyer">
    </form>

<?php require __DIR__ . '/layouts/footer.php' ?>