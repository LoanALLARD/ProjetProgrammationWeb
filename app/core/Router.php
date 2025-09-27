<?php

namespace core;
class Router {
    public function run() {
        // if $_GET['url'] is not null, use it, else use home/index
        $url = $_GET['url'] ?? 'home/index';

        // Cut the string $url with /
        // => $params = []
        $params = explode('/', $url);

        // Build the name of the controller to instantiate
        $controllerName = 'controllers\\' . ucfirst($params[0]) . 'Controller';
        // $methosName = $param[1] if exist, else, $methodName = index
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
