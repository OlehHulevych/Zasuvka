<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Product.php";
require_once __DIR__ . "/../models/FavoriteList.php";
require_once  __DIR__ . "/../models/FavoriteListItem.php";
class ProductController
{
    private $User;
    private $Product;
    private $FavoriteList;
    private $FavoriteListItem;

    public function __construct(){
        $this->User = new User();
        $this->Product = new Product();
        $this->FavoriteList = new FavoriteList();
        $this->FavoriteListItem = new FavoriteListItem();
    }

    public function getAll(){
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $page  = $_GET['page'] ?? 0;
        $lowcost = $_GET['low_cost'] ?? 0;
        $bigcost = $_GET["big_cost"] ?? 0;
        $products = $this->Product->getAll($page, $category, $search, (int)$lowcost, (int)$bigcost);
        echo json_encode(["message"=>"The products are retrieved", "products"=>$products], JSON_PRETTY_PRINT);

    }
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
            echo json_encode(["message"=>"Something went wrong", "items"=>$items], JSON_PRETTY_PRINT);
        }
    }
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
    public function update(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(["message"=>"access is denied"], JSON_PRETTY_PRINT);
        }
        $productId = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? null;
        $price = $_POST['price'] ?? null;
        $description = $_POST['description'] ?? null;
        $deletePhotos = $_POST['delete_photos'] ?? null;
        $newPhotosForProduct = $_POST['new_photos'] ?? null;

        if(isset($_POST['delete_photos'])){
            $deletePhotos = $_POST['delete_photos'];
            foreach ($deletePhotos as $deletePhoto) {
                $fullPath = __DIR__ . '/../' . $deletePhoto;
                echo "Hello Everybody";
                if(file_exists($fullPath)){
                    unlink($fullPath);
                }
            }
        }
        if(isset($_FILES['new_photos'])){

            $newPhotos = $_FILES['new_photos'];
            if(!is_array($_FILES['new_photos'])){
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
