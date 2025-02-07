<?php
$request = $_SERVER['REQUEST_URI'];

if (strpos($request, 'path') === false && isset($_GET['path'])) {
    unset($_GET['path']);
}

require_once '../Router.php';
$router = new Router();
$router->route($request);
