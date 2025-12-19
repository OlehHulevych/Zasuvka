<?php
/**
 * Hlavní vstupní bod aplikace (Bootstrap).
 *
 * Tento soubor se spouští jako první při každém požadavku na API.
 * Nastavuje globální prostředí, řeší bezpečnostní hlavičky (CORS)
 * a startuje session pro udržení přihlášení.
 *
 * @package App
 */

// 1. Inicializace session
// Toto musí být úplně nahoře. Umožňuje serveru číst a zapisovat do $_SESSION (např. 'user_id').
session_start();

// 2. Nastavení CORS (Cross-Origin Resource Sharing)
// Frontend (např. na portu 3000) a backend (na portu 80) jsou různé "origins".
// Zjistíme, odkud požadavek přichází. Pokud nevíme, použijeme "*" (vše), ale to u Credentials nefunguje.
$origin = $_SERVER['HTTP_ORIGIN'] ?? "*";

// Povolíme přístup z domény, která požadavek odeslala.
header("Access-Control-Allow-Origin: $origin");

// Povolíme hlavičky, které frontend posílá (Content-Type pro JSON, Authorization pro tokeny atd.).
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Definujeme, jaké HTTP metody API podporuje.
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");

// DŮLEŽITÉ: Povolíme přenos cookies/credentials.
// Bez tohoto by nefungovalo přihlášení (session cookie by se neuložila v prohlížeči).
header("Access-Control-Allow-Credentials: true");

// 3. Obsluha "Preflight" požadavku (Metoda OPTIONS)
// Moderní prohlížeče před odesláním dat (POST/DELETE) nejprve pošlou "testovací" dotaz OPTIONS,
// aby se ujistily, že server komunikaci povolí.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Pokud je to jen testovací dotaz, vrátíme OK (200) a okamžitě ukončíme skript.
    // Nemá smysl spouštět Router ani připojovat databázi.
    http_response_code(200);
    exit;
}

// 4. Spuštění hlavní logiky
// Pokud prošla kontrola CORS a není to OPTIONS request, načteme Router,
// který rozhodne, jaký Controller spustit.
require_once 'Routes/Routes.php';
?>