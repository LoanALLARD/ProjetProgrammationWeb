<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDW - <?= isset($pageTitle) ? $pageTitle : 'Sans titre' ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <nav>
            <ul style="list-style:none; display:flex; gap:15px; padding:0;">
                <li><a href="/index.php?url=home/index">Accueil</a></li>
                <li><a href="/index.php?url=sitemap/index">Plan du site</a></li>
                <li><a href="/index.php?url=login/index">Connexion</a></li>
                <li><a href="/index.php?url=register/index">S'inscrire</a></li>
            </ul>
        </nav>
    </header>