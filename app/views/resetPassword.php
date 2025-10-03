<?php require __DIR__ . '/layouts/header.php'; ?>

<main class="content" style="text-align:center; padding: 20px;">
    <p class="pageTitle">Validation du code</p>

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

    <form method="POST" action="index.php?url=resetpassword/verificationCode">
        <p>Veuillez saisir le code reçu par mail</p>
        <input type="text" minlength="6" maxlength="6" required name="enteredCode" placeholder="123456">
        <br><br>
        <input type="submit" value="Valider le code">
    </form>
</main>

<?php require __DIR__ . '/layouts/footer.php'; ?>