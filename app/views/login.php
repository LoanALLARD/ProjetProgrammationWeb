<body>
    <div class="page-wrapper">
        <?php require __DIR__ . '/layouts/header.php'; ?>

        <main class="content" style="text-align:center; padding: 20px;">
            <p class="pageTitle">Connexion</p>

            <form method="POST" action="/index.php?url=login/login">
                <?php
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                if (isset($_SESSION['error'])) {
                    echo '<div style="color:red; text-align: center; margin: 10px 0;">' . htmlspecialchars($_SESSION['error']) . '</div>';
                    unset($_SESSION['error']);
                }

                if (isset($_SESSION['success'])) {
                    echo '<div style="color:green; text-align: center; margin: 10px 0;">' . htmlspecialchars($_SESSION['success']) . '</div>';
                    unset($_SESSION['success']);
                }
                ?>

                <p>Identifiant</p>
                <input type="text" name="identifiant" required>

                <p>Mot de passe</p>
                <input type="password" name="password" required>

                <br><br>
                <input type="submit" value="Connexion">

                <br><br>
                <a href="/index.php?url=register/index">Vous n'avez pas de compte ?</a>
                <br>
                <a href="/index.php?url=forgottenpassword/index">Mot de passe oublié ?</a>
            </form>
        </main>

        <?php require __DIR__ . '/layouts/footer.php'; ?>
    </div>
</body>
