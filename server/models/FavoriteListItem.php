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
        //print_r($favoriteList); // Debug výpis

        $product = $this->Product->getById($productId);
        //print_r($product); // Debug výpis

        // Generování nového ID pro položku v rámci seznamu
        $newId = count($favoriteList['items'])?end($favoriteList['items'])['id']+1:1;

        // Vytvoření objektu položky (snapshot dat produktu v čase přidání)
        $newFavoriteListItem = ["id"=>$newId,"name"=>$product['name'], "price"=>$product['price'], "currency"=>$product['currency'], "photo"=>$product['photos'][0], "productId"=>$product['id'], "FavoriteListId"=>$favoriteList['id']];

        $favoriteList['items'][] = $newFavoriteListItem;

        // Aktualizace hlavního pole všech seznamů
        foreach ($favoriteLists as &$list) {
            if($list['userId'] == $userId){
                $list['items'][] = $newFavoriteListItem;
                break;
            }
        }
        print_r($favoriteLists); // Debug výpis
        $this->FavoriteList->saveData($favoriteLists);
        return $newFavoriteListItem;

    }


    public function delete($id, $userId){
        $favoriteList = $this->FavoriteList->getByUserId($userId);

        // Filtrace: Ponechá vše, co NENÍ zadané ID produktu
        $FilteredFavoriteList = array_filter($favoriteList['items'], fn($item)=> $item['productId'] != $id);

        $favoriteLists = $this->FavoriteList->getAll();

        // Aktualizace seznamu v hlavním poli
        foreach ($favoriteLists as &$list){
            if($list['userId'] == $userId){
                $list['items'] = $FilteredFavoriteList;
            }
        }
        $this->FavoriteList->saveData($favoriteLists);
        return true;
    }


}