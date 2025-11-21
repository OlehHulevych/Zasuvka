<?php

require_once __DIR__."/../models/User.php";
require_once  __DIR__. "/../models/Product.php";

class AdminController{
    private User $User;
    private Product $Product;
    public function __construct()
    {
        $this->User = new User();
        $this->Product = new Product();
    }

    public function register(){

        $name = trim($_POST['name']) ?? null;
        $email = trim($_POST['email']) ?? null;
        $password = trim($_POST['password'])?? null;
        $phone  = trim($_POST['phone'])??null;
        $role = "admin";

        if(!$name || !$email || !$password || !$phone ){
            echo "checking";
            http_response_code(400);
            echo json_encode(["message"=>"Something is missing.Please check once again"], JSON_PRETTY_PRINT);
            return;
        }
        $photoPath = null;
        if(isset($_FILES['photo'])){
            $uploadDir = __DIR__ . "/../uploads/avatars/";
            $filename = $name . "_" . $email. "_" . basename($_FILES['photo']['name']) ;
            $target_path = $uploadDir . $filename;

            if(move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)){
                $photoPath = "/uploads/avatars/" . $filename;
            }
            else{
                http_response_code(500);
                echo json_encode(["message"=>"Something went wrong on the server"]);
            }
            $newUser = $this->User->create($name, $email, $photoPath, $phone, $password, $role);
            if($newUser){
                echo json_encode(["message"=>"The user is created", "user"=>$newUser], JSON_PRETTY_PRINT);
                echo "success";
            }
            else{
                http_response_code(400);
                echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);
                echo "error";
            }

        }
    }
    public function login(){
        $loggedUser = null;
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if(!$email || !$password){
            http_response_code(400);
            echo json_encode(["message"=>"Something is missing"], JSON_PRETTY_PRINT);
            return;
        }
        else{
            $loggedUser = $this->User->login($email, $password);
            if(!$loggedUser){
                echo json_encode(["message"=>"Email or password is wrong"]);
            }
            if($loggedUser['role'] != "admin"){
                http_response_code(403);
                echo json_encode(["message"=>"You are not admin"]);
            }
            else{
                $_SESSION["user_id"] = $loggedUser['id'];
                echo json_encode(["message"=>"login was succesfull "], JSON_PRETTY_PRINT);
            }
        }
    }
    public function getAllProducts(){
        $category = trim($_GET['category']) ?? null;
        $search = $_GET['search'] ?? null;
        $page  = $_GET['page'] ?? null;
        if(!isset($search)){
            http_response_code(400);
            echo json_encode(["message"=>"There is no search query"]);
        }
        $products = $this->Product->getAll($page, $category, $search);
        echo json_encode(["message"=>"The products are retrieved", "products"=>$products], JSON_PRETTY_PRINT);

    }

    public function getCounOfUsers(){
        $length = $this->User->getCountOfUsers();
        if($length){
            echo json_encode(["length"=>$length],JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(404);
            echo json_encode(["message"=>"Something went wrong"]);
        }

    }
    public function getCounOfProducts(){
        $length = $this->Product->getCountOfProducts();
        if($length){
            echo json_encode(["length"=>$length],JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(404);
            echo json_encode(["message"=>"Something went wrong"]);
        }

    }

    public function getAllUsers(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"The user is not authorized"],JSON_PRETTY_PRINT);
        }
        $user = $this->User->getUserById($_SESSION['user_id']);
        if($user['role']!='admin'){
            http_response_code(403);
            echo json_encode(["message"=>"Access denied"], JSON_PRETTY_PRINT);
        }
        if(!isset($_GET['page'])){
            http_response_code(400);
        }
        $page = $_GET['page'];
        $users = $this->User->getAllUser($page);
        if($users){
            echo json_encode(["message"=>"The users are retrived", "users"=>$users]);
        }
        else{
            http_response_code(404);
            echo json_encode(["message"=>"Something went wrong"]);
        }
    }

    public function deleteProduct(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"The user is not authorized"],JSON_PRETTY_PRINT);
        }
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(["message"=>"The ID is not found"]);
        }

        $id = $_GET['id'];

        $result = $this->Product->delete($id, $_SESSION['user_id']);
        if($result){
            echo json_encode(["message"=>"The item was deleted", "result"=>$result],JSON_PRETTY_PRINT);

        }
        else{
            echo json_encode(["message"=>"Something went wrong"],JSON_PRETTY_PRINT);
        }
    }
    public function deleteUser(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(401);
            echo json_encode(["message"=>"The user is not authorized"],JSON_PRETTY_PRINT);
        }
        if(!isset($_GET['user_id'])){
            http_response_code(400);
            echo json_encode(["message"=>"The user id is not found"]);
        }
        $id = $_GET['user_id'];
        $result = $this->User->delete($id);
        if($result){
            echo json_encode(["message"=>"The user is deleted", "result"=>$result]);

        }
        else{
            http_response_code(500);
            echo json_encode(["Something went wring"]);
        }
    }


}