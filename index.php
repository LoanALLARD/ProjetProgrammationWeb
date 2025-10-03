<?php
session_start();

require_once __DIR__ . '/app/core/autoloader.php';
require_once __DIR__ . '/app/config/config.php';

$router = new \core\Router();
$router->run();
