<?php
require_once __DIR__ . "/User.php";

/**
 * Třída Product
 *
 * Slouží jako Model pro správu produktů (inzerátů).
 * Data jsou ukládána do JSON souboru (`data/Products.json`).
 * Třída řeší CRUD operace (Vytvoření, Čtení, Úprava, Mazání), filtrování,
 * stránkování a správu obrázků spojených s produkty.
 *
 * @package App\Models
 */
class Product{
    /** @var string Cesta k souboru s daty produktů. */
    private $file = __DIR__ . "/../data/Products.json";

    /** @var User Instance modelu uživatele (pro získání autora inzerátu). */
    private $User;

    /**
     * Konstruktor třídy.
     * Inicializuje model User.
     */
    public function __construct(){
        $this-> User = new User();
    }

    /**
     * Načte data ze souboru JSON.
     *
     * Pokud soubor neexistuje, vytvoří nový prázdný soubor.
     *
     * @return array Pole všech produktů.
     */
    private function getData(){
        if(!file_exists($this->file)){
            file_put_contents($this->file,json_encode([]));
        }
        $items = json_decode(file_get_contents($this->file),true);
        return $items?:[];

    }

    /**
     * Uloží data do souboru JSON.
     *
     * @param array $data Data k uložení.
     * @return void
     */
    private function saveData ($data)
    {
        file_put_contents($this->file, json_encode($data,JSON_PRETTY_PRINT));
    }

    /**
     * Získá produkty s možností filtrování a stránkování.
     *
     * Postupně aplikuje filtry (kategorie, vyhledávání textu, cenové rozpětí)
     * a nakonec vrátí pouze výřez dat pro aktuální stránku.
     *
     * @param int         $page     Číslo stránky.
     * @param string|null $category Název kategorie.
     * @param string|null $search   Hledaný řetězec v názvu.
     * @param int|null    $lowcost  Minimální cena.
     * @param int|null    $bigcost  Maximální cena.
     * @return array Pole obsahující položky ('items'), počet stránek a aktuální stránku.
     */
    public function getAll($page, $category=null , $search=null, $lowcost=null, $bigcost=null ){

        $items = $this->getData();
        if($category && $category!=='null'){
            $items = array_filter($items, fn($item)=> $item['category']===$category);

        }
        if($search && $search !== 'null'){
            $items = array_filter($items,  fn($item)=>str_contains(strtolower($item['name']), strtolower($search)));


        }
        if(($lowcost && $lowcost!=="null") || ($bigcost && $bigcost!=="null")){
            if($bigcost && !$lowcost){
                $items = array_filter($items, fn($item)=>$item['price']<=$bigcost);
            }
            else if($lowcost && !$bigcost){
                $items = array_filter($items,fn($item)=>$item['price']>$lowcost);
            }
            else{
                $items = array_filter($items, fn($item)=> $item['price']>=$lowcost && $item['price']<=$bigcost);
            }
        }

        return $this->paginate($items, $page);


    }

    /**
     * Vrátí celkový počet všech produktů v databázi.
     *
     * @return int|null Počet produktů nebo null, pokud žádné nejsou.
     */
    public function getCountOfProducts()
    {
        $items = $this->getData();
        if($items){
            return count($items);
        }
        else{
            return null;
        }
    }

    /**
     * Vyhledá konkrétní produkt podle ID.
     *
     * @param int $id ID produktu.
     * @return array|null Nalezený produkt nebo null.
     */
    public function getById($id){
        $products = $this->getData();
        foreach ($products as $product){
            if($product['id']==$id){
                return $product;
            }
        }
        return null;
    }

    /**
     * Vyhledá všechny produkty patřící konkrétnímu uživateli.
     *
     * @param int $userId ID uživatele.
     * @return array Pole produktů daného uživatele.
     */
    public function getByUserId($userId){
        $products = $this->getData();
        return array_filter($products, fn($product)=>$product["userId"]==$userId);
    }


    /**
     * Vytvoří nový produkt.
     *
     * Vygeneruje nové ID, načte kontaktní údaje autora z modelu User a uloží záznam.
     *
     * @param string $name        Název produktu.
     * @param int    $userId      ID autora.
     * @param int    $price       Cena.
     * @param array  $photos      Pole cest k nahraným fotkám.
     * @param string $description Popis produktu.
     * @param string $category    Kategorie.
     * @return array Nově vytvořený produkt.
     */
    public function create($name, $userId,  $price, $photos, $description, $category, $location){
        $products = $this->getData();
        $newId= count($products)?end($products)['id']+1:1;
        $productUser = $this->User->getUserById($userId);
        $newProduct = ["id"=>$newId,"name"=>$name, "userId"=>$userId,"location"=>$location, "photos"=>$photos, "price"=>$price, "author"=>$productUser['name'], "phone"=>$productUser['phone'], "description"=>$description, "category"=>$category];
        $products[] = $newProduct;
        $this->saveData($products);
        return $newProduct;

    }

    /**
     * Upraví existující produkt.
     *
     * Kontroluje, zda uživatel má právo produkt upravit (shoda userId).
     * Umožňuje smazání vybraných fotek a přidání nových.
     *
     * @param int        $id           ID produktu.
     * @param int        $userId       ID uživatele (pro kontrolu oprávnění).
     * @param string     $name         Nový název.
     * @param int        $price        Nová cena.
     * @param string     $description  Nový popis.
     * @param array|null $deletePhotos Pole cest k fotkám, které se mají smazat.
     * @param array|null $newPhotos    Pole cest k novým fotkám.
     * @return array|null Upravený produkt nebo null při chybě/neoprávněném přístupu.
     */
    public function update($id, $userId, $name, $price, $description, $deletePhotos, $newPhotos, ){
        $products = $this->getData();
//        foreach ($deletePhotos as $deletePhoto){
//            echo "This is" . $deletePhoto;
//        }
        foreach ($products as &$product){
            if($product['id']==$id){
                // Kontrola vlastnictví
                if($product['userId']!==$userId){

                    return null;
                }
                $product['name'] = $name ?? $product['name'];
                $product['price'] = (int)$price ?? $product['price'];
                $product['description'] = $description?? $product['description'];


                // Mazání označených fotek ze seznamu
                if (!empty($deletePhotos)) {
                    foreach ($deletePhotos as $deletePhoto){
                        $newArray = array_filter($product['photos'], fn($photo)=>$photo != $deletePhoto);
                        $product['photos'] = $newArray;

                    }


                }
                // Přidání nových fotek
                if($newPhotos){
                    foreach ($newPhotos as $newPhoto){
                        $product['photos'][] = $newPhoto;
                    }
                }


                $this->saveData($products);
                return $product;
            }
        }
        return null;
    }

    /**
     * Pomocná metoda pro stránkování pole dat.
     *
     * @param array $items Vstupní pole dat.
     * @param int   $page  Požadovaná stránka.
     * @return array Struktura obsahující data pro danou stránku a metadata stránkování.
     */
    private function paginate($items, $page){
        $limit = 5;
        $total = count($items);
        $totalPages = ceil($total/$limit);
        $offset = ($page-1)*$limit;
        $paginated = array_slice($items, $offset,$limit);
        return ["items"=>$paginated, "totalPages"=>$totalPages, "page"=>$page];
    }

    /**
     * Smaže produkt.
     *
     * Kromě smazání záznamu z JSONu fyzicky odstraní (unlink) soubory fotek z disku.
     * Mazat může vlastník produktu nebo administrátor.
     *
     * @param int $id     ID produktu.
     * @param int $userId ID uživatele, který akci vyvolal.
     * @return bool True při úspěchu, False při neoprávněném přístupu.
     */
    public function delete($id, $userId){
        $products = $this->getData();
        $productForDeleting = $this->getById($id);

        // Fyzické smazání fotek z disku
        $photos = $productForDeleting['photos'];
        foreach ($photos as &$photo){
            $fullPath = __DIR__ . "/.." . $photo;
            if(file_exists($fullPath)){
                unlink($fullPath);
            }
        }

        $user = $this->User->getUserById($userId);
        //print_r($user);

        // Kontrola oprávnění (vlastník nebo admin)
        if($productForDeleting['userId'] == $userId or $user['role'] == "admin"){
            $filteredProducts = array_filter($products, fn($product)=>$product['id'] != $id);
            $this->saveData($filteredProducts);
            return true;
        }
        else{
            return false;
        }

    }

}