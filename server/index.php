<?php



session_start();


$origin = $_SERVER['HTTP_ORIGIN'] ?? "*";


header("Access-Control-Allow-Origin: $origin");


header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");


header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");


header("Access-Control-Allow-Credentials: true");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(200);
    exit;
}


require_once 'Routes/Routes.php';
?>