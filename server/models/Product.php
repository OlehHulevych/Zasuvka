<?php
require_once __DIR__ . "/User.php";
class Product{
    private $file = __DIR__ . "/../data/Products.json";
    private $User;
    public function __construct(){
        $this-> User = new User();
    }
    private function getData(){
        if(file_exists($this->file)){
            file_put_contents($this->file,[]);
        }
        $items = file_get_contents($this->file);
        return json_decode($items,true);
    }
    private function saveData ($data)
    {
        file_put_contents($this->file, json_encode($data,JSON_PRETTY_PRINT));
        echo "The data is saved";
    }

    public function getAll(){
        return $this->getData();
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



    public function create($name, $userId, $currency, $price, $photos, $description){
        $products = $this->getData();
        $newId= count($products)?end($products)['id']+1:1;
        $productUser = $this->User->getUserById($userId);
        $newProduct = ["id"=>$newId,"name"=>$name, "userId"=>$userId, "photos"=>$photos, "price"=>$price, "author"=>$productUser['name'], "phone"=>$productUser['phone'], "currency"=>$currency, "description"=>$description];
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
    public function delete($id){
        $products = $this->getData();
        $filteredProducts = array_filter($products, fn($product)=>$product['id'] != $id);
        $this->saveData($filteredProducts);
        return true;
    }

}