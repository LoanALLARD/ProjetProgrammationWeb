<?php require __DIR__ . '/layouts/header.php'; ?>

<main class="content" style="text-align:center; padding: 20px;">
    <p class="pageTitle">Nouveau mot de passe</p>

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

    <form method="POST" action="index.php?url=updatepassword/updatePassword">
        <p>Veuillez saisir votre nouveau mot de passe</p>
        <input type="password" required name="password" minlength="8" placeholder="Minimum 8 caractères">

        <p>Confirmation du mot de passe</p>
        <input type="password" required name="passwordConfirmation" minlength="8" placeholder="Confirmez le mot de passe">

        <br><br>
        <input type="submit" value="Réinitialiser le mot de passe">
    </form>
</main>

<?php require __DIR__ . '/layouts/footer.php'; ?>