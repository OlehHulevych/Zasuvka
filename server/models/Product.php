<?php
require_once __DIR__ . "/User.php";
class Product{
    private $file = __DIR__ . "/../data/Products.json";
    private $User;
    public function __construct(){
        $this-> User = new User();
    }
    private function getData(){
        if(!file_exists($this->file)){
            file_put_contents($this->file,json_encode([]));
        }
        $items = json_decode(file_get_contents($this->file),true);
        return $items?:[];

    }
    private function saveData ($data)
    {
        file_put_contents($this->file, json_encode($data,JSON_PRETTY_PRINT));
        echo "The data is saved";
    }

    public function getAll($page, $category=null ){
        $items = $this->getData();
        if(!$category){
            return $this->paginate($items, $page);

        }
        else{
            $filteredItems = array_filter($items, fn($item)=> $item.$category==$category);
            return $this->paginate($filteredItems,$page);
        }

    }
    public function getById($id){
        $products = $this->getData();
        foreach ($products as $product){
            if($product['id']==$id){
                return $product;
            }
        }
        return null;
    }



    public function create($name, $userId, $currency, $price, $photos, $description, $category){
        $products = $this->getData();
        $newId= count($products)?end($products)['id']+1:1;
        $productUser = $this->User->getUserById($userId);
        $newProduct = ["id"=>$newId,"name"=>$name, "userId"=>$userId, "photos"=>$photos, "price"=>$price, "author"=>$productUser['name'], "phone"=>$productUser['phone'], "currency"=>$currency, "description"=>$description, "category"=>$category];
        $products[] = $newProduct;
        $this->saveData($products);
        return $newProduct;

    }

    public function update($id, $name, $currency, $price, $photos, $description){
        $products = $this->getData();
        foreach ($products as $product){
            if($product['id']==$id){
                $product['name'] = $name;
                $product['currency'] = $currency;
                $product['price'] = $price;
                $product['photos'] = $photos;
                $product[$description] = $description;
                $this->saveData($products);
                return $product;
            }
        }
        return null;
    }

    private function paginate($items, $page){
        $limit = 5;
        $total = count($items);
        $totalPages = ceil($total/$limit);
        $offset = ($page-1)*$limit;
        $paginated = array_slice($items, $offset,$limit);
        return ["items"=>$paginated, "totalPages"=>$totalPages, "page"=>$page];
    }
    public function delete($id){
        $products = $this->getData();
        $filteredProducts = array_filter($products, fn($product)=>$product['id'] != $id);
        $this->saveData($filteredProducts);
        return true;
    }

}