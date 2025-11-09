<?php
require_once __DIR__ . "/../controllers/ProductController.php";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$ProductController = new ProductController();

if($uri=="/product" && $method === "GET"){
    $ProductController->getAll();
}
elseif($uri=="/product" && $method==="POST"){
    $ProductController->create();
}
