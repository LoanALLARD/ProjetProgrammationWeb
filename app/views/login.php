<?php require __DIR__ . '/layouts/header.php'; ?>

    <form method="POST" action="/index.php?url=login/login">
        <p>Identifiant</p>
        <input type="text" name="identifiant" required>

        <p>Mot de passe</p>
        <input type="password" name="password" required>

        <input type="submit" value="Connexion">

        <a href="/index.php?url=register/index">Vous n'avez pas de compte ?</a>
        <a href="/index.php?url=forgottenpassword/index">Mot de passe oublié ?</a>
    </form>

<?php require __DIR__ . '/layouts/footer.php' ?>