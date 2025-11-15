<?php

class FavoriteItemController{
    public User $User;
    public Product $Product;
    public FavoriteListItem $FavoriteListItem;
    public FavoriteList $FavoriteList;

    public function __construct()
    {
        $this->FavoriteListItem = new FavoriteListItem();
        $this->User = new User();
        $this->Product = new Product();
        $this->FavoriteList = new FavoriteList();
    }

    public function create(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"Access denies.User is not authorized"], JSON_PRETTY_PRINT);
        }
        $productId = $_POST['productId'];
        $result = $this->FavoriteListItem->create($_SESSION['user_id'], $productId);
        if($result){
            echo json_encode(["message"=>"the favorite item was created", "favoriteItem"=>$result]);
        }
        else{
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);
        }

    }
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

