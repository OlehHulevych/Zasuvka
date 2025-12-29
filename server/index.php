<?php

ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', '0'); // MUST be 0 on HTTP
ini_set('session.cookie_httponly', '1');

session_start();

$allowedOrigins = [
    'http://zwa.toad.cz',
    'https://zwa.toad.cz'
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}

header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'Routes/Routes.php';
