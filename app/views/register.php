<?php require __DIR__ . '/layouts/header.php'; ?>

<form>
    <p>Identifiant</p>
    <input type="text" name="identifiant" required>

    <p>Adresse mail</p>
    <input type="text" name="email" required>

    <p>Téléphone</p>
    <input type="text" name="telephone" required>

    <p>Mot de passe</p>
    <input type="password" name="password" required>

    <p>Confirmation du Mot de passe</p>
    <input type="password" name="passwordConfirmation" required>

    <input type="hidden" name="action" value="register">
    <input type="submit" value="S'inscrire">

    <a href="/index.php?url=login/index">Déjà un de compte ?</a>

    <p></p>
</form>

<?php require __DIR__ . '/layouts/footer.php' ?>