<?php require __DIR__ . '/layouts/header.php'; ?>

    <p>Mot de passe oublié</p>
    <form method="POST" action="/index.php?url=forgottenpassword/verificationCode">
        <p>Veuillez saisir le code reçu par mail</p>
        <input type="text" minlength="6" maxlength="6" required name="enteredCode">

        <input type="submit" value="Envoyer">
    </form>

<?php require __DIR__ . '/layouts/footer.php' ?>