<?php

/**
 * Třída FavoriteList
 *
 * Slouží k manipulaci se seznamy oblíbených položek (Wishlists), které jsou uloženy
 * v lokálním souboru JSON (`data/FavoriteLists.json`).
 * Funguje jako náhrada databáze pro ukládání vztahu Uživatel <-> Seznam oblíbených.
 *
 * @package App\Models
 */
class FavoriteList{

    /** * @var string Cesta k JSON souboru, kam se ukládají data.
     */
    private $file = __DIR__ . "/../data/FavoriteLists.json";

    /**
     * Načte data ze souboru.
     *
     * Pokud soubor neexistuje, automaticky ho vytvoří s prázdným polem.
     *
     * @return array Pole všech záznamů ze souboru.
     */
    private function getData(){
        if(!file_exists($this->file)){
            file_put_contents($this->file,json_encode([]));
        }
        $items = json_decode(file_get_contents($this->file),true);
        return $items?:[];

    }

    /**
     * Uloží data do JSON souboru.
     *
     * Přepíše celý obsah souboru novými daty ve formátu JSON (Pretty Print).
     *
     * @param array $data Data k uložení (pole seznamů).
     * @return void
     */
    public function saveData ($data)
    {
        file_put_contents($this->file, json_encode($data,JSON_PRETTY_PRINT));

    }

    /**
     * Získá všechny seznamy oblíbených položek.
     *
     * @return array Pole všech seznamů.
     */
    public function getAll(){
        return $this->getData();
    }

    /**
     * Vyhledá seznam oblíbených pro konkrétního uživatele.
     *
     * Prochází všechny záznamy a hledá shodu s `userId`.
     *
     * @param int $userId ID uživatele.
     * @return array|null Vráti pole s daty seznamu nebo null, pokud seznam neexistuje.
     */
    public function getAllById($userId){
        $favoriteList =null;
        $all = $this->getAll();
        foreach ($all as &$list){
            if($list['userId'] == $userId){
                $favoriteList = $list;
            }
        }
        if(!$favoriteList){
            return null;
        }
        else{
            return $favoriteList;
        }

    }

    /**
     * Získá seznam podle ID uživatele (Alternativní metoda).
     *
     * Funguje velmi podobně jako getAllById. Vrátí první nalezený záznam.
     *
     * @param int $userId ID uživatele.
     * @return array|null Nalezený seznam nebo null.
     */
    public function getByUserId($userId){
        $favoriteLists = $this->getAll();
        foreach ($favoriteLists as &$favoriteList){
            if($favoriteList['userId'] == $userId){
                return $favoriteList;
            }
        }
        return null;
    }

    /**
     * Vytvoří nový prázdný seznam oblíbených pro uživatele.
     *
     * Vygeneruje nové ID (poslední ID + 1) a uloží novou strukturu do souboru.
     *
     * @param int $userId ID uživatele, pro kterého se seznam vytváří.
     * @return array Nově vytvořený seznam (pole).
     */
    public function create($userId){
        $favoriteLists = $this->getAll();
        // Pokud pole není prázdné, vezme poslední ID a přičte 1, jinak začne od 1.
        $newId = count($favoriteLists)? end($favoriteLists)['id']+1:1;

        $newFavoriteList = ["id"=>$newId, "userId"=>$userId, "items"=>[]];
        $favoriteLists[] = $newFavoriteList;
        $this->saveData($favoriteLists);
        return $newFavoriteList;
    }

    /**
     * Smaže seznam oblíbených pro konkrétního uživatele.
     *
     * Odfiltruje ze seznamu záznam odpovídající předanému `userId` a uloží změny.
     *
     * @param int $userId ID uživatele.
     * @return void
     */
    public function delete($userId){
        $items = $this->getAll();
        // Ponechá pouze položky, které NEPATŘÍ danému uživateli
        $FilteredItems = array_filter($items, fn($item)=>$item['userId'] != $userId);
        $this->saveData($FilteredItems);

    }
}