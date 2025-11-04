<?php

class FavoriteList{
    private $file = __DIR__ . "/../data/FavoriteLists.json";
    private function getData(){
        if(file_exists($this->file)){
            file_put_contents($this->file,[]);
        }
        $items = file_get_contents($this->file);
        return json_decode($items,true);
    }
    public function saveData ($data)
    {
        file_put_contents($this->file, json_encode($data,JSON_PRETTY_PRINT));
        echo "The data is saved";
    }

    public function getAll(){
        return $this->getData();
    }
    public function getByUserId($userId){
        $favoriteLists = $this->getAll();
        foreach ($favoriteLists as $favoriteList){
            if($favoriteList['userId'] == $userId){
                return $favoriteList;
            }
        }
        return null;
    }
    public function create($userId){
        $favoriteLists = $this->getAll();
        $newId = count($favoriteLists)? end($favoriteLists)['id']+1:1;
        $newFavoriteList = ["id"=>$newId, "userId"=>$userId, "items"=>[]];
        $favoriteLists[] = $newFavoriteList;
        $this->saveData($favoriteLists);
        return $newFavoriteList;
    }
    public function delete($userId){
        $items = $this->getAll();
        $FilteredItems = array_filter($items, fn($item)=>$item['userId'] != $userId);
        $this->saveData($FilteredItems);

    }
}