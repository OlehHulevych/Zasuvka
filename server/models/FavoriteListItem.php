<?php
require_once __DIR__ . "/FavoriteList.php";
require_once __DIR__ . "/Product.php";

/**
 * Třída FavoriteListItem
 *
 * Reprezentuje konkrétní položku uvnitř seznamu oblíbených.
 * Řeší logiku přidání produktu do seznamu (vytvoření záznamu s kopií dat produktu)
 * a jeho následné odebrání.
 *
 * @package App\Models
 */
class FavoriteListItem
{
    /** @var FavoriteList Model pro práci s celými seznamy. */
    private $FavoriteList;

    /** @var Product Model pro načítání dat o produktech. */
    private $Product;

    /**
     * Konstruktor třídy.
     * Inicializuje modely FavoriteList a Product.
     */
    public function __construct()
    {
        $this->FavoriteList = new FavoriteList();
        $this->Product = new Product();
    }

    /**
     * Vytvoří novou položku v seznamu oblíbených.
     *
     * Načte aktuální data produktu (název, cena, fotka) a vytvoří jejich "snapshot",
     * který uloží do seznamu uživatele.
     *
     * @param int $userId    ID uživatele, kterému se má položka přidat.
     * @param int $productId ID produktu, který se přidává.
     * @return array Nově vytvořená položka (pole s daty).
     */
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

    /**
     * Odstraní produkt ze seznamu oblíbených.
     *
     * Vyfiltruje položky seznamu tak, že odstraní tu, která odpovídá zadanému ID produktu.
     *
     * @param int $id     ID produktu (productId), který se má smazat.
     * @param int $userId ID uživatele, kterému seznam patří.
     * @return bool Vrací true po úspěšném uložení.
     */
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