<?php require __DIR__ . '/layouts/header.php'; ?>

    <p class="pageTitle">Inscription</p>
    <form method="POST" action="/index.php?url=register/register">
        <p>Identifiant</p>
        <input type="text" name="identifiant" required>

        <p>Adresse mail</p>
        <input type="email" name="email" required>

        <p>Téléphone</p>
        <input type="tel" name="telephone">

        <p>Mot de passe</p>
        <input type="password" name="password" required>

        <p>Confirmation du Mot de passe</p>
        <input type="password" name="passwordConfirmation" required>

        <input type="submit" value="S'inscrire">

        <a href="/index.php?url=login/index">Déjà un compte ?</a>
    </form>

<?php require __DIR__ . '/layouts/footer.php' ?>