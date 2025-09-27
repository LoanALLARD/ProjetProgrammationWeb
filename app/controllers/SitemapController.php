<?php
namespace controllers;

class SitemapController {

    private $pages = [];

    public function getPages() {
        return $this->pages;
    }

    public function index() {
        $controllerFiles = glob(__DIR__ . '/*Controller.php');

        foreach ($controllerFiles as $file) {
            $filename = basename($file, '.php');
            if ($filename === 'SitemapController') continue;

            $pageName = str_replace('Controller', '', $filename);
            $url = "/index.php?url=" . strtolower($pageName) . "/index";

            $this->pages[] = [
                'title' => $pageName,
                'url'   => $url
            ];
        }

        $pages = $this->getPages();
        $pageTitle = "Plan du site";

        require __DIR__ . '/../views/sitemap.php';
    }
}
