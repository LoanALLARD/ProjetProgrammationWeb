<?php

namespace core;

spl_autoload_register(function ($class) {
    // Replaces namespaces with folder paths
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
