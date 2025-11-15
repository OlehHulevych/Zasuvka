<?php
require_once __DIR__ . "/../controllers/UserController.php";
require_once  __DIR__ ."/../controllers/ProductController.php";
require_once __DIR__ . "/../controllers/FavoriteItemController.php";

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/server', '', $uri);
$method = $_SERVER['REQUEST_METHOD'];
$userController = new UserController();
$productController = new ProductController();
$favoriteItemController = new FavoriteItemController();



if($uri=="/user/register" && $method === 'POST'){
    $userController->register();
}
elseif($uri=="/user/login" && $method === 'POST'){
    $userController->login();
}
elseif($uri=="/user" && $method === 'PUT'){
    $userController->update();
}
elseif ($uri == "/user/avatar" && $method === 'POST'){
    $userController->updateAvatar();
}
elseif ($uri == '/user' && $method === 'GET'){
    $userController->getALl();
}
elseif($uri == "/user/authorize" && $method === "GET"){
    $userController->authorize();
}
elseif($uri == "/user/logout" && $method == "GET"){
    $userController->logout();
}
elseif ($uri == "/user" && $method == "DELETE"){
    $userController->delete();
}
elseif($uri=="/product" && $method === "GET"){
    $productController->getAll();
}
elseif($uri=="/product" && $method==="POST"){
    $productController->create();
}
elseif($uri =="/product/id" && $method === "GET"){
    $productController->getProductById();
}
elseif($uri == "/product/update" && $method==="POST"){
    $productController->update();
}

elseif ($uri == "/product" && $method === "DELETE"){
    $productController->delete();
}
elseif ($uri == "/favoriteItem" && $method === "POST"){
    $favoriteItemController->create();
}

elseif($uri == "/favoriteItem" && $method === "GET"){
    $favoriteItemController->getFavorites();
}
else{
    http_response_code(400);
    echo json_encode(["message"=>"The route is not existing"]);
}