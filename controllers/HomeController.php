<?php
class HomeController {
    public function index() {
        if (!isset($_SESSION['organizacion'])) {
            header("Location: /login");
            exit();
        }
        require_once __DIR__ . '/../views/home.php';
    }
}