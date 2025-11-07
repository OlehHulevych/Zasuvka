<?php
require_once __DIR__ . "/../controllers/UserController.php";

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$userController = new UserController();


if($uri=="/user/register" && $method === 'POST'){
    $userController->register();
}
elseif($uri=="/user/login" && $method === 'POST'){
    $userController->login();
}
elseif($uri=="/user" && $method === 'PUT'){
    $userController->update();
}
else{
    http_response_code(400);
    echo json_encode(["message"=>"The route is not existing"]);
}