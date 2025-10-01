<?php

namespace core;

class Router {

    // Table de correspondance pour les cas spéciaux
    private array $controllerMap = [
        'forgottenpassword' => 'ForgottenPassword',
        'updatepassword' => 'UpdatePassword',
        // Ajoutez ici d'autres contrôleurs multi-mots si nécessaire
    ];

    public function run() {
        $url = $_GET['url'] ?? 'home/index';
        $params = explode('/', $url);

        $controllerName = 'controllers\\' . $this->toPascalCase($params[0]) . 'Controller';
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

    private function toPascalCase($string) {
        $lowerString = strtolower($string);

        // Vérifier si c'est dans la table de correspondance
        if (isset($this->controllerMap[$lowerString])) {
            return $this->controllerMap[$lowerString];
        }

        // Si la chaîne contient déjà des majuscules, on la garde
        if (preg_match('/[A-Z]/', $string)) {
            return ucfirst($string);
        }

        // Sinon, traiter les séparateurs
        $string = str_replace(['-', '_'], ' ', $lowerString);
        $string = ucwords($string);
        return str_replace(' ', '', $string);
    }
}