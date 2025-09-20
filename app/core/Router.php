<?php

namespace core;
class Router {
    public function run() {
        $url = $_GET['url'] ?? 'home/index';
        $params = explode('/', $url);

        $controllerName = 'controllers\\' . ucfirst($params[0]) . 'Controller';
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
}
