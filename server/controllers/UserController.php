<?php
require_once __DIR__ .'/../models/User.php';
class UserController
{
    private $User;
    public function __construct()
    {
        $this->User = new User();
    }

    public function register(){

        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password']?? null;
        $phone  = $_POST['phone']??null;
        $role = $_POST['role'] ?? 'user';
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode(["message"=>"Bad format of email"],JSON_PRETTY_PRINT);
        }

        if(!$name || !$email || !$password || !$phone ){
            http_response_code(404);
            echo json_encode(["message"=>"Something is missing.Please check once again"], JSON_PRETTY_PRINT);

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
                echo json_encode(["message"=>"Something went wrong on the server"],JSON_PRETTY_PRINT);
            }
            $newUser = $this->User->create($name, $email, $photoPath, $phone, $password, $role);
            if($newUser){
                echo json_encode(["message"=>"The user is created", "user"=>$newUser], JSON_PRETTY_PRINT);

            }
            else{
                http_response_code(404);
                echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);

            }

        }
    }
    public function login(){
        $email = $_POST['email'];
        $password = $_POST['password'];

        if(!$email || !$password){
            http_response_code(400);
            echo json_encode(["message"=>"Something is missing"], JSON_PRETTY_PRINT);
        }
        else{
            $loggedUser = $this->User->login($email, $password);
            if(!isset($loggedUser)){
                http_response_code(401);
                echo json_encode(["message"=>"Email or password is wrong","result"=>false],JSON_PRETTY_PRINT);
            }
            else{
                $_SESSION["user_id"] = $loggedUser['id'];
                echo json_encode(["message"=>"login was succesfull","user_id"=>$loggedUser['id'],"result"=>true], JSON_PRETTY_PRINT);
            }
        }
    }

    public function update(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(400);
            echo json_encode(["The user is not logged","status"=>false]);
        }

        $name = $_POST['name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $phone  = $_POST['phone'] ?? null;




        $id = $_SESSION['user_id'];
        $foundedUser = $this->User->getUserById($id);
        if(!$foundedUser){
            http_response_code(400);
            echo json_encode(["message"=>"The user is not found","status"=>false],JSON_PRETTY_PRINT);
        }
        if(isset($_FILES['photo'])){
            $uploadDir = __DIR__ . "/../uploads/avatars/";
            $filename = basename($foundedUser['photoPath']);
            $targetPath = $uploadDir . $filename;
            if(file_exists($targetPath)){
                unlink($targetPath);
                move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath);


            }
            else{
                http_response_code(500);
                echo json_encode(["message"=>"Something went wrong with photo","status"=>false],JSON_PRETTY_PRINT);
            }
        }



        //echo "This is id: " . $id;
        $updatedUser = $this->User->update($id, $name, $email, $phone, $password);
        if($updatedUser){
            echo json_encode(["message"=>"The user is updated", "user"=>$updatedUser, "status"=>true], JSON_PRETTY_PRINT);

        }
        else{
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong","status"=>false], JSON_PRETTY_PRINT);

        }



    }

    public function getALl(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(404);
            echo json_encode(["message"=>"The user is not loged in"]);
            return;
        }
        $admin = $this->User->getUserById($_SESSION['user_id'], JSON_PRETTY_PRINT);
        if($admin['role'] !== 'admin'){
            http_response_code(404);
            echo json_encode(["message"=>"Access denied"], JSON_PRETTY_PRINT);
            return;
        }
        $users = $this->User->getAll();
        echo json_encode(["users"=>$users]);

    }

    public function authorize ():void{
        if(!isset($_SESSION['user_id'])){
            http_response_code(404);
            echo json_encode(["message"=>"The is not logged"], JSON_PRETTY_PRINT);
        }
        $user = $this->User->getUserById($_SESSION['user_id']);
        if(!$user){
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);

        }
        echo json_encode(["message"=>"The data is retrieved", "user"=>$user], JSON_PRETTY_PRINT);
    }

    public function getById():void{
        $user = $this->User->getUserById($_GET['id']);
        if(!$user){
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);

        }
        echo json_encode(["message"=>"The data is retrieved", "user"=>$user], JSON_PRETTY_PRINT);
    }
    public function logout(){
        session_destroy();
        http_response_code(200);
        echo json_encode(["message"=>"The user is logged out"]);
    }
    public function delete(){
        if(!$_SESSION['user_id']){
            http_response_code(403);
            echo json_encode(["message"=>"The user is not logged"]);
        }
        $idForDeleting = $_GET['id'];
        $user = $this->User->getUserById($_SESSION['user_id']);
        if($user['role']=='admin' || $user['id']==$idForDeleting){
            $status = $this->User->delete($idForDeleting);
            echo json_encode(["message"=>"The user was deleted", "status"=>$status], JSON_PRETTY_PRINT);
        }
        else{
            echo json_encode(["message"=>"No acsess"]);
        }
    }

    


}