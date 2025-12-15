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
    }

    public function getAll($page, $category=null , $search=null, $lowcost=null, $bigcost=null ){

        $items = $this->getData();
        if($category){
            $items = array_filter($items, fn($item)=> $item['category']==$category);

        }
        if($search){
            $items = array_filter($items,  fn($item)=>str_contains(strtolower($item['name']), strtolower($search)));

        }
        if($lowcost || $bigcost){
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
    public function getById($id){
        $products = $this->getData();
        foreach ($products as $product){
            if($product['id']==$id){
                return $product;
            }
        }
        return null;
    }
    public function getByUserId($userId){
        $products = $this->getData();
        return array_filter($products, fn($product)=>$product["userId"]==$userId);
    }



    public function create($name, $userId,  $price, $photos, $description, $category){
        $products = $this->getData();
        $newId= count($products)?end($products)['id']+1:1;
        $productUser = $this->User->getUserById($userId);
        $newProduct = ["id"=>$newId,"name"=>$name, "userId"=>$userId, "photos"=>$photos, "price"=>$price, "author"=>$productUser['name'], "phone"=>$productUser['phone'], "description"=>$description, "category"=>$category];
        $products[] = $newProduct;
        $this->saveData($products);
        return $newProduct;

    }


    public function update($id, $userId, $name, $price, $description, $deletePhotos, $newPhotos){
        $products = $this->getData();
//        foreach ($deletePhotos as $deletePhoto){
//            echo "This is" . $deletePhoto;
//        }
        foreach ($products as &$product){
            if($product['id']==$id){
                if($product['userId']!==$userId){

                    return null;
                }
                $product['name'] = $name ?? $product['name'];
                $product['price'] = (int)$price ?? $product['price'];
                $product['description'] = $description?? $product['description'];
                if (!empty($deletePhotos)) {
                    foreach ($deletePhotos as $deletePhoto){
                        $newArray = array_filter($product['photos'], fn($photo)=>$photo != $deletePhoto);
                        $product['photos'] = $newArray;

                    }


                }
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

    private function paginate($items, $page){
        $limit = 10;
        $total = count($items);
        $totalPages = ceil($total/$limit);
        $offset = ($page-1)*$limit;
        $paginated = array_slice($items, $offset,$limit);
        return ["items"=>$paginated, "totalPages"=>$totalPages, "page"=>$page];
    }
    public function delete($id, $userId){
        $products = $this->getData();
        $productForDeleting = $this->getById($id);
        $photos = $productForDeleting['photos'];
        foreach ($photos as &$photo){
            $fullPath = __DIR__ . "/.." . $photo;
            if(file_exists($fullPath)){
                unlink($fullPath);
            }
        }
        $user = $this->User->getUserById($userId);
        //print_r($user);
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