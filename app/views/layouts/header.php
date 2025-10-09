<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="Blocnotes, php, projet, étudiant, Application web, iut">
    <meta name="description" content="Application web simple et intuitive pour créer, organiser et partager vos notes en ligne. Accédez à vos idées partout, en toute sécurité.">
    <title><?= isset($pageTitle) ? $pageTitle : 'Sans titre' ?> | MMNotes</title>
    <link rel="stylesheet" href="/public/assets/styles/style.css">
    <link rel="icon" type="image/x-icon" href="/public/assets/favicon.ico">
</head>
<body>
    <header>
        <nav id="navbar">
            <a id="navTitle" href="/index.php?url=home/index">MMNotes</a>
            <input type="checkbox" id="burger-toggle" />
            <label for="burger-toggle" class="burger">☰</label>
            <ul>
                <li class="navElements"><a class="navElementsText" href="/index.php?url=home/index">Accueil</a></li>
                <li class="navElements"><a class="navElementsText" href="/index.php?url=sitemap/index">Plan du site</a></li>
                <?php
                if (isset($_SESSION['user_id'])) { ?>
                    <li class="navElements"><a class="navElementsText" href="/index.php?url=login/logout">Déconnexion</a></li>
                <?php } else { ?>
                    <li class="navElements"><a class="navElementsText" href="/index.php?url=login/index">Connexion</a></li>
                <?php } ?>
                <li class="navElements"><a class="navElementsText" href="/index.php?url=register/index">S'inscrire</a></li>
            </ul>
        </nav>
    </header>