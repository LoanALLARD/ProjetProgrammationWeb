<?php require __DIR__ . '/layouts/header.php'; ?>

<p class="pageTitle">Mot de passe oublié</p>
<p>Recevoir un mail de récupération</p>
<form method="POST" action="/index.php?url=forgottenpassword/changePassword">
    <p>Veuillez saisir votre adresse mail</p>
    <input type="email" name="email" required>

    <input type="submit" value="Envoyer">
</form>

<?php require __DIR__ . '/layouts/footer.php' ?>