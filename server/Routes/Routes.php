<?php
require_once __DIR__ . "/../controllers/UserController.php";
require_once  __DIR__ ."/../controllers/ProductController.php";

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$userController = new UserController();
$productController = new ProductController();



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
elseif($uri=="/product" && $method === "GET"){
    $productController->getAll();
}
elseif($uri=="/product" && $method==="POST"){
    $productController->create();
}

else{
    http_response_code(400);
    echo json_encode(["message"=>"The route is not existing"]);
}