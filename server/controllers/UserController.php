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

        if(!$name || !$email || !$password || !$phone ){
            echo "checking";
            http_response_code(400);
            echo json_encode(["message"=>"Something is missing.Please check once again"], JSON_PRETTY_PRINT);
            return;
        }
        echo "next step";
        $photoPath = null;
        if(isset($_FILES['photo'])){
            $uploadDir = __DIR__ . "/../uploads/avatars/";
            $filename = $name . "_" . $email. "_" . basename($_FILES['photo']['name']) ;
            $target_path = $uploadDir . $filename;

            if(move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)){
                $photoPath = "/uploads/avatars" . $filename;
            }
            else{
                http_response_code(500);
                echo json_encode(["message"=>"Something went wrong on the server"]);
            }
            echo "creating";
            $newUser = $this->User->create($name, $email, $photoPath, $phone, $password);
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


}