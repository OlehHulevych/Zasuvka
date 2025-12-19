<?php

/**
 * Třída FavoriteItemController
 *
 * Zajišťuje správu oblíbených položek uživatele (tzv. Wishlist).
 * Umožňuje přidávat produkty do oblíbených, zobrazovat seznam uložených položek
 * a odstraňovat je.
 *
 * @package App\Controllers
 */
class FavoriteItemController{
    /** @var User Model pro práci s uživateli. */
    public User $User;

    /** @var Product Model pro práci s produkty. */
    public Product $Product;

    /** @var FavoriteListItem Model pro manipulaci s jednotlivými položkami v oblíbených. */
    public FavoriteListItem $FavoriteListItem;

    /** @var FavoriteList Model pro práci s celým seznamem oblíbených. */
    public FavoriteList $FavoriteList;

    /**
     * Konstruktor třídy.
     * Inicializuje všechny potřebné modely.
     */
    public function __construct()
    {
        $this->FavoriteListItem = new FavoriteListItem();
        $this->User = new User();
        $this->Product = new Product();
        $this->FavoriteList = new FavoriteList();
    }

    /**
     * Přidá produkt do oblíbených položek.
     *
     * Očekává ID produktu v URL parametru `id`.
     * Vyžaduje, aby byl uživatel přihlášen (kontroluje `$_SESSION['user_id']`).
     *
     * @api
     * @param int $_GET['id'] ID produktu, který se má přidat do oblíbených.
     * @return void Vypíše JSON odpověď s vytvořenou položkou nebo chybovou hlášku.
     */
    public function create(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"Access denies.User is not authorized"], JSON_PRETTY_PRINT);
        }
        $productId = $_GET['id'];
        $result = $this->FavoriteListItem->create($_SESSION['user_id'], $productId);
        if($result){
            echo json_encode(["message"=>"the favorite item was created", "favoriteItem"=>$result]);
        }
        else{
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);
        }

    }

    /**
     * Získá seznam všech oblíbených položek aktuálně přihlášeného uživatele.
     *
     * @api
     * @return void Vypíše JSON odpověď obsahující pole oblíbených položek.
     */
    public function getFavorites(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"Access denies.User is not authorized"], JSON_PRETTY_PRINT);
        }
        $list = $this->FavoriteList->getAllById($_SESSION['user_id']);
        if(!$list){
            http_response_code(404);
            echo json_encode(["message"=>"The list is not found or something else"],JSON_PRETTY_PRINT);
        }
        else{
            echo json_encode(["message"=>"The list is received", "list"=>$list], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Odstraní položku ze seznamu oblíbených.
     *
     * Očekává ID položky (nikoliv produktu, ale vazby) v URL parametru `id`.
     *
     * @api
     * @param int $_GET['id'] ID položky v seznamu oblíbených, která se má smazat.
     * @return void Vypíše JSON potvrzení o smazání nebo chybu.
     */
    public function deleteFavorite(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"Access denies.User is not authorized"], JSON_PRETTY_PRINT);
        }
        $id = $_GET['id'];
        $result = $this->FavoriteListItem->delete($id, $_SESSION['user_id']);
        if(!$result){
            http_response_code(400);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);
        }
        else{
            echo json_encode(["message"=>"Item was deleted"], JSON_PRETTY_PRINT);
        }

    }
}