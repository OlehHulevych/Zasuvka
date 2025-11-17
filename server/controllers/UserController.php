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
            echo "creating";
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
        $email = $_POST['email'];
        $password = $_POST['password'];

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
            else{
                $_SESSION["user_id"] = $loggedUser['id'];
                echo json_encode(["message"=>"login was succesfull "], JSON_PRETTY_PRINT);
            }
        }
    }

    public function update(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(400);
            echo json_encode(["The user is not logged"]);
        }
        $data = json_decode(file_get_contents("php://input"),true);
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $phone  = $data['phone'] ?? null;

        echo "Email is " . $email;

        $id = $_SESSION['user_id'];
        $foundedUser = $this->User->getUserById($id);
        if(!$foundedUser){
            http_response_code(400);
            echo json_encode(["message"=>"The user is not found"]);
        }

        echo "This is id: " . $id;
        $updatedUser = $this->User->update($id, $name, $email, $phone, $password);
        if($updatedUser){
            echo json_encode(["message"=>"The user is updated", "user"=>$updatedUser], JSON_PRETTY_PRINT);
            return json_encode(["message"=>"The user is updated", "user"=>$updatedUser], JSON_PRETTY_PRINT);
        }
        else{
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);

        }



    }
    public function updateAvatar(){
        if(!isset($_SESSION['user_id'])){
            http_response_code(404);
            echo json_encode(["message"=>"The user is not logged"]);
        }
        $id = $_SESSION['user_id'];
        $user = $this->User->getUserById($id);


        if(isset($_FILES['photo'])){
            $uploadDir = __DIR__ . "/../uploads/avatars/";
            $filename = basename($user['photoPath']);
            $targetPath = $uploadDir . $filename;
            if(file_exists($targetPath)){
                unlink($targetPath);
                move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath);
                echo json_encode(["message"=>"The avatar of user is updated"]);
                return;
            }
            else{
                http_response_code(500);
                echo json_encode(["message"=>"Something went wrong"]);
            }
        }
        else{
            http_response_code(400);
            echo json_encode(["message"=>"There is no any photo"]);
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