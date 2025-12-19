# Zásuvka - Backend API

Vítejte v dokumentaci backendové části projektu **Zásuvka**.
Tato aplikace slouží jako REST API pro správu inzerátů (produktů), uživatelů a oblíbených položek. Jako datové úložiště využívá **JSON soubory**, takže nevyžaduje MySQL databázi.

## 🚀 Funkce
* **Autentizace:** Registrace, přihlášení, odhlášení, správa session.
* **Uživatelé:** Úprava profilu, nahrávání profilových fotek (avatarů), role (admin/user).
* **Produkty:** Vytváření inzerátů, nahrávání fotek, filtrování, vyhledávání, stránkování.
* **Oblíbené:** Přidávání a odebírání produktů z Wishlistu.
* **Admin:** Mazání libovolných produktů/uživatelů, povyšování uživatelů, statistiky.

## 🛠 Použité technologie
* **Jazyk:** PHP 8+
* **Databáze:** JSON soubory (uloženo v `/data`)
* **Dokumentace:** phpDocumentor
* **Server:** Apache (XAMPP)