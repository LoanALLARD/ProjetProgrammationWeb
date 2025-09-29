<?php require __DIR__ . '/layouts/header.php'; ?>

    <p>Mise à jour du mot de passe</p>
    <form method="POST" action="/index.php?url=forgottenpassword/updatePassword">
        <p>Veuillez saisir votre nouveau mot de passe</p>
        <input type="text" required name="password">

        <p>Confirmation du mot de passe</p>
        <input type="text" required name="confirmationPassword">

        <input type="submit" value="Envoyer">
    </form>

<?php require __DIR__ . '/layouts/footer.php' ?>