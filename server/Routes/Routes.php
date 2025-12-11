<?php
require_once __DIR__ . "/../controllers/UserController.php";
require_once  __DIR__ ."/../controllers/ProductController.php";
require_once __DIR__ . "/../controllers/FavoriteItemController.php";
require_once  __DIR__ . "/../controllers/AdminController.php";

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/Zasuvka/server', '', $uri);
$method = $_SERVER['REQUEST_METHOD'];
$userController = new UserController();
$productController = new ProductController();
$favoriteItemController = new FavoriteItemController();
$adminController = new AdminController();



if($uri=="/user/register" && $method === 'POST'){
    $userController->register();
}
elseif($uri=="/user/login" && $method === 'POST'){
    $userController->login();
}
elseif($uri=="/user/update" && $method === 'POST'){
    $userController->update();
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

elseif($uri == "/admin/user" && $method === "GET"){
    $adminController->getAllUsers();
}
elseif($uri == "/admin/user/promote" && $method === "GET"){
    $adminController->toPromote();
}
elseif($uri == "/admin/user/count" && $method === "GET"){
    $adminController->getCounOfUsers();
}
elseif($uri == "/admin/user" && $method === "DELETE"){
    $adminController->deleteUser();
}

elseif($uri == "/admin/product" && $method === "GET"){
    $adminController->getAllProducts();
}
elseif($uri == "/admin/product/count" && $method === "GET"){
    $adminController->getCounOfProducts();
}
elseif($uri == "/admin/product" && $method === "DELETE"){
    $adminController->deleteProduct();
}

else{
    http_response_code(400);
    echo json_encode(["message"=>"The route is not existing"]);
}