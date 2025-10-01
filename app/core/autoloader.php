<?php

namespace core;

spl_autoload_register(function ($class) {
    // Replaces namespaces with folder paths
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';

    // Pour déboguer : décommentez ces lignes temporairement
    // echo "Classe recherchée : $class<br>";
    // echo "Fichier cherché : $file<br>";
    // echo "Existe ? " . (file_exists($file) ? 'OUI' : 'NON') . "<br><br>";

    if (file_exists($file)) {
        require_once $file;
    } else {
        // Message d'erreur plus explicite
        throw new \Exception("Impossible de charger la classe $class. Fichier attendu : $file");
    }
});