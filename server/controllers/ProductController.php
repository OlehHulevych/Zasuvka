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
        $category = $_GET['category'];
        $page  = $_GET['page'];
        $products = $this->Product->getAll($page, $category);
        echo json_encode(["message"=>"The products are retrieved", "products"=>$products]);

    }
    public function getProductById(){
        $id = $_GET['id'];
        $product = $this->Product->getById($id);
        if(!$product){
            http_response_code(404);
            echo json_encode(["message"=>"The product is not found"], JSON_PRETTY_PRINT);
        }
        else{
            echo json_encode(["message"=>"The product is found", "product"=>$product]);
        }
    }
    public function create(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(403);
            echo json_encode(["message"=>"The user is not authorized"]);
        }
        $id = $_SESSION['user_id'];
        $name = $_POST['name'];
        $category = $_POST['category'];
        $currency = $_POST['currency'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $photos = [];
        $uploadDir =  __DIR__ . "/../uploads/products/";
        foreach ($_FILES['photos']['name'] as $key=>$originalName){
            $filename = $name . "_" . $key . "_" . basename($originalName);
            $tmpName = $_FILES['photos']['tmp_name'][$key];
            $targetPath = $uploadDir . $filename;
            if(move_uploaded_file($tmpName , $targetPath)){
                $photos[] = '/uploads/products/' . $filename;
            }
            else{
                echo "The photo is not saved";
            }

        }


        $newProduct = $this->Product->create($name, $id, $currency, $price, $photos, $description, $category);
        if($newProduct){
            echo json_encode(['message'=>"The product is created", 'product'=>$newProduct], JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"]);
        }

    }

}
