<?php

class FavoriteItemController{
    public User $User;
    public Product $Product;
    public FavoriteListItem $FavoriteListItem;

    public function __construct()
    {
        $this->FavoriteListItem = new FavoriteListItem();
        $this->User = new User();
        $this->Product = new Product();
    }

    public function create(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"Access denies.User is not authorized"], JSON_PRETTY_PRINT);
        }
        $productId = $_GET['productId'];
        $result = $this->FavoriteListItem->create($_SESSION['user_id'], $productId);
        if($result){
            echo json_encode(["message"=>"the favorite item was created", "favoriteItem"=>$result]);
        }
        else{
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"]);
        }

    }
}
