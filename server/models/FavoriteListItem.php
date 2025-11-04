<?php
require_once __DIR__ . "/FavoriteList.php";
require_once __DIR__ . "/Product.php";
class FavoriteListItem
{
    private $FavoriteList;
    private $Product;
    public function __construct()
    {
        $this->FavoriteList = new FavoriteList();
        $this->Product = new Product();
    }

    public function create($userId , $productId){
        $favoriteLists = $this->FavoriteList->getAll();
        $favoriteList = $this->FavoriteList->getByUserId($userId);
        $product = $this->Product->getById($productId);
        $newId = count($favoriteList['items'])?end($favoriteList['items'])['id']+1:1;
        $newFavoriteListItem = ["id"=>$newId,"name"=>$product['name'], "price"=>$product['price'], "currency"=>$product['currency'], "photo"=>$product['photos'][0], "productId"=>$product['id'], "FavoriteListId"=>$favoriteList['id']];
        $favoriteList['items'][] = $newFavoriteListItem;
        $this->FavoriteList->saveData($favoriteLists);
        return $newFavoriteListItem;

    }

    public function delete($id, $userId){
        $favoriteList = $this->FavoriteList->getByUserId($userId);
        $FilteredFavoriteList = array_filter($favoriteList['items'], fn($item)=> $item['id'] !=$id);
        $this->FavoriteList->saveData($FilteredFavoriteList);
        return true;
    }


}