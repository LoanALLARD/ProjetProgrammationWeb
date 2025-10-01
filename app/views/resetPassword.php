<?php require __DIR__ . '/layouts/header.php'; ?>

    <p class="pageTitle">Validation du code</p>

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

    <form method="POST" action="index.php?url=resetpassword/verificationCode">
        <p>Veuillez saisir le code reçu par mail</p>
        <input type="text" minlength="6" maxlength="6" required name="enteredCode" placeholder="123456">
        <input type="submit" value="Valider le code">
    </form>

<?php require __DIR__ . '/layouts/footer.php' ?>