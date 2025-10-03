<?php

namespace core;

class Router {

    // Special cases
    private array $controllerMap = [
        'forgottenpassword' => 'ForgottenPassword',
        'updatepassword' => 'UpdatePassword',
        'resetpassword' => 'ResetPassword',
        'sitemap' => 'SiteMap',
        'mentionslegales' => 'MentionsLegales',
    ];

    public function run() {
        $url = $_GET['url'] ?? 'home/index';
        $params = explode('/', $url);

        $controllerName = 'controllers\\' . $this->toUpperCase($params[0]) . 'Controller';
        $methodName = $params[1] ?? 'index';

        if (class_exists($controllerName)) {
            $controller = new $controllerName();

            if(method_exists($controller, $methodName)) {
                $controller->$methodName();
            } else {
                echo "Méthode $methodName inexistante.";
            }
        } else {
            echo "Contrôleur $controllerName inexistant.";
        }
    }

    private function toUpperCase($string) {
        $lowerString = strtolower($string);

        // Check if is in special cases
        if (isset($this->controllerMap[$lowerString])) {
            return $this->controllerMap[$lowerString];
        }

        // If the string already contains capital letters, keep them
        if (preg_match('/[A-Z]/', $string)) {
            return ucfirst($string);
        }

        // Otherwise, treat separators
        $string = str_replace(['-', '_'], ' ', $lowerString);
        $string = ucwords($string);
        return str_replace(' ', '', $string);
    }
}