<?php
/**
 * Hlavní směrovač (Router) API.
 *
 * Tento skript slouží jako vstupní bod pro všechny příchozí HTTP požadavky.
 * Zpracovává URL adresu, určuje metodu požadavku (GET, POST, atd.) a
 * přesměrovává volání na příslušnou metodu v konkrétním Controlleru.
 *
 * Logika směrování:
 * 1. Načtení závislostí (Controllers).
 * 2. Získání a očištění URI z požadavku.
 * 3. Instanciace všech potřebných Controllerů.
 * 4. Rozhodovací strom (if/elseif) pro spárování URL s metodou.
 * 5. Návrat chybového kódu 400, pokud trasa neexistuje.
 *
 *
 *
 */

// -----------------------------------------------------------------------------
// 1. Načtení závislostí (Controllers)
// -----------------------------------------------------------------------------
require_once __DIR__ . "/../controllers/UserController.php";
require_once __DIR__ . "/../controllers/ProductController.php";
require_once __DIR__ . "/../controllers/FavoriteItemController.php";
require_once __DIR__ . "/../controllers/AdminController.php";

// -----------------------------------------------------------------------------
// 2. Příprava požadavku a nastavení
// -----------------------------------------------------------------------------

/** * @var string $uri Cesta URL aktuálního požadavku (např. "/user/login").
 * Používáme parse_url pro oddělení cesty od query parametrů (?id=1).
 */
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Odstranění kořenové složky pro lokální vývojové prostředí.
// @todo Při nasazení na produkci tuto část upravte nebo odstraňte podle konfigurace serveru.
$uri = str_replace('/Zasuvka/server', '', $uri);

/** * @var string $method HTTP sloveso požadavku (GET, POST, DELETE, atd.).
 */
$method = $_SERVER['REQUEST_METHOD'];

// -----------------------------------------------------------------------------
// 3. Inicializace Controllerů
// -----------------------------------------------------------------------------
// Používáme anotace @var, aby IDE napovídalo metody uvnitř těchto objektů.

/** @var UserController $userController Správa registrace, přihlášení a profilu uživatele. */
$userController = new UserController();

/** @var ProductController $productController Správa produktů (výpis, vytváření, mazání). */
$productController = new ProductController();

/** @var FavoriteItemController $favoriteItemController Správa oblíbených položek uživatele. */
$favoriteItemController = new FavoriteItemController();

/** @var AdminController $adminController Administrativní úkony a statistiky. */
$adminController = new AdminController();


// -----------------------------------------------------------------------------
// 4. Směrování požadavků (Routing)
// -----------------------------------------------------------------------------

/* --- Trasy pro Uživatele (User Routes) --- */

if($uri == "/user/register" && $method === 'POST'){
    $userController->register();
}
elseif($uri == "/user/login" && $method === 'POST'){
    $userController->login();
}
elseif($uri == "/user/update" && $method === 'POST'){
    $userController->update();
}
elseif ($uri == '/user' && $method === 'GET'){
    $userController->getALl();
}
elseif ($uri == '/user/id' && $method === 'GET'){
    // Získá uživatele podle ID (očekává parametr v URL nebo Session)
    $userController->getById();
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

/* --- Trasy pro Produkty (Product Routes) --- */

elseif($uri == "/product" && $method === "GET"){
    $productController->getAll();
}
elseif($uri == "/product" && $method === "POST"){
    $productController->create();
}
elseif($uri =="/product/id" && $method === "GET"){
    $productController->getProductById();
}
elseif($uri == "/product/update" && $method === "POST"){
    $productController->update();
}
elseif ($uri == "/product" && $method === "DELETE"){
    $productController->delete();
}
elseif ($uri == "/productByUser" && $method === "GET"){
    $productController->getProductByUserId();
}

/* --- Trasy pro Oblíbené položky (Favorite Items) --- */

elseif ($uri == "/favoriteItem" && $method === "POST"){
    $favoriteItemController->create();
}
elseif($uri == "/favorites" && $method === "GET"){
    $favoriteItemController->getFavorites();
}
elseif($uri == "/favoriteItem" && $method === "DELETE"){
    $favoriteItemController->deleteFavorite();
}

/* --- Trasy pro Administrátora (Admin Routes) --- */

elseif($uri == "/admin/user" && $method === "GET"){
    $adminController->getAllUsers();
}
elseif($uri == "/admin/user/promote" && $method === "GET"){
    // Povýšení uživatele na admina nebo jinou roli
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

/* --- Neexistující trasa (404/400) --- */

else{
    // Pokud žádná z podmínek výše neplatí, vrátíme chybu
    http_response_code(400);
    echo json_encode(["message" => "Tato trasa neexistuje (Route not found)"]);
}