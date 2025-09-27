<?php
namespace controllers;

class SitemapController {

    private $pages = [];

    public function getPages() {
        return $this->pages;
    }

    public function index() {
        $controllerFiles = glob(__DIR__ . '/*.php');

        foreach ($controllerFiles as $file) {
            $filename = basename($file, '.php');

            // Ignore SitemapController
            if ($filename === 'SitemapController') {
                continue;
            }

            $pageName = str_replace('Controller', '', $filename);
            $url = "/index.php?url=" . strtolower($pageName) . "/index";

            $this->pages[] = [
                'title' => $pageName,
                'url'   => $url
            ];
        }

        $pages = $this->getPages();
        $pageTitle = "Plan du site";

        $viewPathSiteMap = __DIR__ . '/../views/siteMap.php';
        if (file_exists($viewPathSiteMap)) {
            require $viewPathSiteMap;
        } else {
            echo "Erreur lors du chargement de la vue siteMap.php";
        }
    }
}
?>