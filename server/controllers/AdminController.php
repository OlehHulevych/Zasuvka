<?php

require_once __DIR__."/../models/User.php";
require_once  __DIR__. "/../models/Product.php";

/**
 * Třída AdminController
 *
 * Zpracovává administrativní požadavky týkající se správy uživatelů a produktů.
 * Zajišťuje ověření oprávnění (zda je uživatel admin) a vrací odpovědi ve formátu JSON.
 *
 * @package App\Controllers
 */
class AdminController{
    /**
     * @var User Instance modelu pro práci s uživateli.
     */
    private User $User;

    /**
     * @var Product Instance modelu pro práci s produkty.
     */
    private Product $Product;

    /**
     * Konstruktor třídy.
     * Inicializuje modely User a Product.
     */
    public function __construct()
    {
        $this->User = new User();
        $this->Product = new Product();
    }


    /**
     * Získá seznam všech produktů.
     *
     * Umožňuje filtrování podle kategorie, vyhledávání a stránkování.
     * Očekává parametry v URL (GET request).
     *
     * @api
     * @param string|null $_GET['category'] Kategorie produktu (volitelné).
     * @param string      $_GET['search']   Vyhledávací dotaz (povinné).
     * @param int|null    $_GET['page']     Číslo stránky pro stránkování (volitelné).
     * @return void Vypíše JSON odpověď se seznamem produktů.
     */
    public function getAllProducts(){
        $category = trim($_GET['category']) ?? null;
        $search = $_GET['search'] ?? null;
        $page  = $_GET['page'] ?? null;
        if(!isset($search)){
            http_response_code(400);
            echo json_encode(["message"=>"There is no search query"]);
        }
        $products = $this->Product->getAll($page, $category, $search);
        echo json_encode(["message"=>"The products are retrieved", "products"=>$products], JSON_PRETTY_PRINT);

    }

    /**
     * Povýší uživatele na administrátora nebo změní jeho roli.
     *
     * Vyžaduje přihlášení a roli administrátora.
     *
     * @api
     * @param int $_GET['id'] ID uživatele, který má být povýšen.
     * @return void Vypíše JSON odpověď o úspěchu nebo chybě.
     */
    public function toPromote(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"The user is not authorized","status"=>false]);
        }
        $checkedUser = $this->User->getUserById($_SESSION['user_id']);
        if($checkedUser['role'] != 'admin'){
            http_response_code(401);
            echo json_encode(["message"=>"You don`t have access","status"=>false]);
        }
        $id = $_GET['id'];
        $response = $this->User->promoteToAdminOrUser($id);
        if(!isset($response)){
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong on the server","status"=>false]);
        }
        else{
            echo json_encode(["message"=>"The use is promoted","result"=>true]);
        }
    }

    /**
     * Získá celkový počet uživatelů v systému.
     *
     * @return void Vypíše JSON odpověď obsahující počet uživatelů ({length: int}).
     */
    public function getCounOfUsers(){
        $length = $this->User->getCountOfUsers();
        if($length){
            echo json_encode(["length"=>$length],JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(404);
            echo json_encode(["message"=>"Something went wrong"]);
        }

    }

    /**
     * Získá celkový počet produktů v systému.
     *
     * @return void Vypíše JSON odpověď obsahující počet produktů ({length: int}).
     */
    public function getCounOfProducts(){
        $length = $this->Product->getCountOfProducts();
        if($length){
            echo json_encode(["length"=>$length],JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(404);
            echo json_encode(["message"=>"Something went wrong"]);
        }

    }

    /**
     * Získá seznam všech uživatelů se stránkováním.
     *
     * Vyžaduje přihlášení a roli administrátora.
     *
     * @api
     * @param int $_GET['page'] Číslo stránky.
     * @return void Vypíše JSON odpověď se seznamem uživatelů.
     */
    public function getAllUsers(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"The user is not authorized"],JSON_PRETTY_PRINT);
        }
        $user = $this->User->getUserById($_SESSION['user_id']);
        if($user['role']!='admin'){
            http_response_code(403);
            echo json_encode(["message"=>"Access denied"], JSON_PRETTY_PRINT);
        }
        if(!isset($_GET['page'])){
            http_response_code(400);
        }
        $page = $_GET['page'];
        $users = $this->User->getAllUser($page);
        if($users){
            echo json_encode(["message"=>"The users are retrived", "users"=>$users]);
        }
        else{
            http_response_code(404);
            echo json_encode(["message"=>"Something went wrong"]);
        }
    }

    /**
     * Smaže konkrétní produkt.
     *
     * Vyžaduje přihlášení.
     *
     * @api
     * @param int $_GET['id'] ID produktu ke smazání.
     * @return void Vypíše JSON odpověď o výsledku operace.
     */
    public function deleteProduct(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"The user is not authorized"],JSON_PRETTY_PRINT);
        }
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(["message"=>"The ID is not found"]);
        }

        $id = $_GET['id'];

        $result = $this->Product->delete($id, $_SESSION['user_id']);
        if($result){
            echo json_encode(["message"=>"The item was deleted", "result"=>$result],JSON_PRETTY_PRINT);

        }
        else{
            echo json_encode(["message"=>"Something went wrong"],JSON_PRETTY_PRINT);
        }
    }

    /**
     * Smaže konkrétního uživatele.
     *
     * Vyžaduje přihlášení.
     *
     * @api
     * @param int $_GET['user_id'] ID uživatele ke smazání.
     * @return void Vypíše JSON odpověď o výsledku operace.
     */
    public function deleteUser(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"The user is not authorized"],JSON_PRETTY_PRINT);
        }
        if(!isset($_GET['user_id'])){
            http_response_code(400);
            echo json_encode(["message"=>"The user id is not found"]);
        }
        $id = $_GET['user_id'];
        $result = $this->User->delete($id);
        if($result){
            echo json_encode(["message"=>"The user is deleted", "result"=>$result]);

        }
        else{
            http_response_code(500);
            echo json_encode(["Something went wring"]);
        }
    }


}