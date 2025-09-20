<?php require __DIR__ . '/layouts/header.php'; ?>

<form>
    <p>Identifiant</p>
    <input type="text" name="identifiant" required>

    <p>Mot de passe</p>
    <input type="password" name="password" required>

    <input type="hidden" name="action" value="login">
    <input type="submit" value="Connexion">

    <a href="/index.php?url=register/index">Vous n'avez pas de compte ?</a>
    <p>Mot de passe oublié ?</p>
</form>

<?php require __DIR__ . '/layouts/footer.php' ?>