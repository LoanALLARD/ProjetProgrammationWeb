<body>
    <div class="page-wrapper">
        <?php require __DIR__ . '/layouts/header.php'; ?>

        <main class="content" style="text-align:center; padding: 20px;">
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

                <br><br>
                <input type="submit" value="S'inscrire">

                <br><br>
                <a href="/index.php?url=login/index">Déjà un compte ?</a>
            </form>
        </main>

        <?php require __DIR__ . '/layouts/footer.php'; ?>
    </div>
</body>
