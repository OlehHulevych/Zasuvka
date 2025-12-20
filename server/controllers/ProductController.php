<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../models/FavoriteList.php";
require_once  __DIR__ . "/../models/FavoriteListItem.php";

/**
 * Třída ProductController
 *
 * Zajišťuje kompletní správu produktů (inzerátů).
 * Řeší výpis, filtrování, vytváření, úpravu (včetně nahrávání obrázků) a mazání produktů.
 *
 * @package App\Controllers
 */
class ProductController
{
    /** @var User Model pro práci s uživateli. */
    private $User;

    /** @var Product Model pro práci s produkty. */
    private $Product;

    /** @var FavoriteList Model pro seznamy oblíbených. */
    private $FavoriteList;

    /** @var FavoriteListItem Model pro položky v oblíbených. */
    private $FavoriteListItem;

    /**
     * Konstruktor třídy.
     * Inicializuje instance všech potřebných modelů.
     */
    public function __construct(){
        $this->User = new User();
        $this->Product = new Product();
        $this->FavoriteList = new FavoriteList();
        $this->FavoriteListItem = new FavoriteListItem();
    }

    /**
     * Získá seznam produktů s možností filtrování.
     *
     * Přijímá parametry z URL (GET) pro vyhledávání, kategorie, cenu a stránkování.
     *
     * @api
     * @param string|null $_GET['category'] Kategorie pro filtrování.
     * @param string|null $_GET['search']   Hledaný výraz.
     * @param int         $_GET['page']     Číslo stránky (offset).
     * @param int         $_GET['low_cost'] Minimální cena.
     * @param int         $_GET['big_cost'] Maximální cena.
     * @return void Vypíše JSON s nalezenými produkty.
     */
    public function getAll(){
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $page  = $_GET['page'] ?? 1;
        $lowcost = $_GET['low_cost'] ?? null;
        $bigcost = $_GET["big_cost"] ?? null;

        $products = $this->Product->getAll($page, $category, $search, (int)$lowcost, (int)$bigcost);
        echo json_encode(["message"=>"The products are retrieved", "products"=>$products], JSON_PRETTY_PRINT);

    }

    /**
     * Získá detail konkrétního produktu podle ID.
     *
     * @api
     * @param int $_GET['id'] ID produktu.
     * @return void Vypíše JSON s daty produktu nebo chybu 404.
     */
    public function getProductById(){
        $id = $_GET['id'];
        $product = $this->Product->getById($id);
        if(!$product){
            http_response_code(404);
            echo json_encode(["message"=>"The product is not found"], JSON_PRETTY_PRINT);
        }
        else{
            echo json_encode(["message"=>"The product is found", "item"=>$product]);
        }
    }

    /**
     * Získá produkty vytvořené aktuálně přihlášeným uživatelem.
     *
     * Vyžaduje přihlášení uživatele.
     *
     * @api
     * @return void Vypíše JSON se seznamem produktů uživatele.
     */
    public function getProductByUserId(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(["message"=>"The user is not authorized"]);
        }
        $userId = $_SESSION['user_id'];
        $items = $this->Product->getByUserId($userId);
        if(!isset($items)){
            http_response_code(404);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);
        }
        else{
            echo json_encode(["message"=>"You got items", "items"=>$items], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Vytvoří nový produkt.
     *
     * Zpracuje POST data a nahraje obrázky (Files) na server do složky `uploads/products/`.
     * Vyžaduje přihlášení.
     *
     * @api
     * @param string $_POST['name']        Název produktu.
     * @param string $_POST['category']    Kategorie.
     * @param int    $_POST['price']       Cena.
     * @param string $_POST['description'] Popis produktu.
     * @param array  $_FILES['photos']     Obrázky k nahrání.
     * @return void Vypíše JSON s vytvořeným produktem.
     */
    public function create(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(["message"=>"The user is not authorized"]);
        }
        $id = $_SESSION['user_id'];
        $name = trim($_POST['name']);
        $category = trim($_POST['category']);
        $price = (int)$_POST['price'];
        $description = trim($_POST['description']);
        $photos = [];
        if(!$name || !$category || !$price || !$description){
            echo json_encode(["message"=>"Something is missing"], JSON_PRETTY_PRINT);
        }

        $uploadDir =  __DIR__ . "/../uploads/products/";
        foreach ($_FILES['photos']['name'] as $key=>$originalName){
            $filename = $name . "_" . $key . "_" . basename($originalName);
            $tmpName = $_FILES['photos']['tmp_name'][$key];
            $formatedName = str_replace(" ", "_", $filename);
            $targetPath = $uploadDir . $formatedName;
            if(move_uploaded_file($tmpName , $targetPath)){
                $photos[] = '/uploads/products/' . $formatedName;
            }
            else{
                echo "The photo is not saved";
            }

        }


        $newProduct = $this->Product->create($name, $id, $price, $photos, $description, $category);
        if($newProduct){
            echo json_encode(['message'=>"The product is created", 'product'=>$newProduct], JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);
        }

    }

    /**
     * Aktualizuje existující produkt.
     *
     * Umožňuje změnu textových údajů, smazání starých fotek a nahrání nových.
     * Vyžaduje přihlášení a vlastnictví produktu (kontroluje se v modelu/DB).
     *
     * @api
     * @param int   $_GET['id']             ID produktu k úpravě.
     * @param array $_POST['deletePhotos']  Seznam cest k fotkám, které se mají smazat.
     * @param array $_FILES['newPhotos']    Nové fotky k nahrání.
     * @return void Vypíše JSON s aktualizovaným produktem.
     */
    public function update(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(["message"=>"access is denied"], JSON_PRETTY_PRINT);
        }
        $productId = $_GET['id'] ?? null;
        $name = $_POST['name'] ?? null;
        $price = $_POST['price'] ?? null;
        $description = $_POST['description'] ?? null;
        $deletePhotos = $_POST['deletePhotos'] ?? null;
        $newPhotosForProduct = $_POST['newPhotos'] ?? null;

        // Mazání starých fotek ze serveru
        if(isset($_POST['deletePhotos'])){
            $deletePhotos = $_POST['deletePhotos'];
            foreach ($deletePhotos as $deletePhoto) {
                $fullPath = __DIR__ . '/../' . $deletePhoto;
                if(file_exists($fullPath)){
                    unlink($fullPath);
                }
            }
        }
        // Nahrávání nových fotek
        if(isset($_FILES['newPhotos'])){

            $newPhotos = $_FILES['newPhotos'];
            if(!is_array($_FILES['newPhotos'])){
                $newPhotos = explode('/', $newPhotos);
            }
            foreach ($newPhotos['name'] as $key=>$originalName){
                $filename = $name . "_" . $key . "_" . basename($originalName);
                $tmp_name = $newPhotos['tmp_name'][$key];
                $formatedName = str_replace(" ", "_", $filename);
                $targetPath = __DIR__ . "/../uploads/products/" . $formatedName;
                if(move_uploaded_file($tmp_name, $targetPath)){
                    $newPhotosForProduct[] = '/uploads/products/' . $formatedName;
                }

            }
        }

        $updatedProduct = $this->Product->update($productId, $_SESSION['user_id'], $name,  $price, $description, $deletePhotos,$newPhotosForProduct );
        if($updatedProduct){
            echo json_encode(['message'=>"The product is updated", "updatedProduct"=>$updatedProduct], JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(400);
            echo json_encode(["message"=>"The product is not updated"],JSON_PRETTY_PRINT);
        }
    }

    /**
     * Smaže produkt.
     *
     * Vyžaduje přihlášení a kontrolu oprávnění (zda produkt patří uživateli).
     *
     * @api
     * @param int $_GET['id'] ID produktu ke smazání.
     * @return void Vypíše JSON potvrzení nebo chybu.
     */
    public function delete(){
        if(!isset($_SESSION['user_id'])){
            echo "access is denied";
        }
        $id = $_GET['id'];
        $result = $this->Product->delete($id, $_SESSION['user_id']);
        if($result){
            echo json_encode(["message"=>"The product was deleted", "status"=>$result], JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(401);
            echo json_encode(["message"=>"Access denied"], JSON_PRETTY_PRINT);
        }


    }

}