<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Sans titre' ?> | MMNotes</title>
    <link rel="stylesheet" href="/public/assets/styles/style.css">
    <link rel="icon" type="image/x-icon" href="/public/assets/favicon.ico"/>
</head>
<body>
    <header>
        <nav id="navbar">
            <p id="navTitle">MMNotes</p>
            <ul>
                <li class="navElements"><a class="navElementsText" href="/index.php?url=home/index">Accueil</a></li>
                <li class="navElements"><a class="navElementsText" href="/index.php?url=sitemap/index">Plan du site</a></li>
                <li class="navElements"><a class="navElementsText" href="/index.php?url=login/index">Connexion</a></li>
                <li class="navElements"><a class="navElementsText" href="/index.php?url=register/index">S'inscrire</a></li>
            </ul>
        </nav>
    </header>