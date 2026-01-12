<?php

class AboutController {
    
    public function index() {
        $pageTitle = 'Giới thiệu - FastFood';
        
        include 'views/layouts/header.php';
        include 'views/about/index.php';
        include 'views/layouts/footer.php';
    }
}