<?php

namespace core;

spl_autoload_register(function ($class) {
    // Remplace les namespaces par des chemins de dossier
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
