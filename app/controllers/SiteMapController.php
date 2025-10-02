<?php
namespace controllers;

class SiteMapController {

    // Array to store the different pages
    private $pages = [];

    // Getter : getPages
    public function getPages() {
        return $this->pages;
    }

    public function index() {
        // Stores files matching the pattern in an array
        $controllerFiles = glob(__DIR__ . '/*.php');

        // Browse the array $controllerFiles
        foreach ($controllerFiles as $file) {
            // Sets $fileName relative to the controller name
            $filename = basename($file, '.php');

            // Ignore SiteMapController
            if ($filename === 'SiteMapController') {
                continue;
            }

            // Rename the pages by removing “controller”
            $pageName = str_replace('Controller', '', $filename);

            $url = "/index.php?url=" . strtolower($pageName) . "/index";

            // Stores the page and URL in the array
            $this->pages[] = [
                'title' => $pageName,
                'url'   => $url
            ];
        }

        $pages = $this->getPages();
        $pageTitle = "Plan du site";

        // Directory of the view
        $viewPathSiteMap = __DIR__ . '/../views/siteMap.php';

        // Try to display the view
        if (file_exists($viewPathSiteMap)) {
            require $viewPathSiteMap;
        } else {
            echo "Erreur lors du chargement de la vue " . $viewPathSiteMap;
        }
    }
}