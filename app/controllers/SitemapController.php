<?php

namespace controllers;

class SitemapController
{
    public function index() {
        $pageTitle = "Plan du site";
        require __DIR__ . '/../views/siteMap.php';
    }

    public function generateSiteMap() {

    }
}