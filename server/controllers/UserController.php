<?php
/**
 * ... popis ...
 * @package App\Controllers
 */
require_once __DIR__ .'/../models/User.php';

/**
 * Třída UserController
 *
 * Zajišťuje správu uživatelů, včetně autentizace (login/logout), registrace,
 * úpravy profilu a nahrávání avatarů.
 * Řídí také přístupová práva (např. pouze admin může vypsat všechny uživatele).
 *
 * @package App\Controllers
 */
class UserController
{
    /** @var User Instance modelu pro práci s databází uživatelů. */
    private $User;

    /**
     * Konstruktor třídy.
     * Inicializuje model User.
     */
    public function __construct()
    {
        $this->User = new User();
    }

    /**
     * Registruje nového uživatele.
     *
     * Zpracovává POST data (jméno, email, heslo...) a nahrává profilovou fotku
     * do složky `uploads/avatars/`.
     * Provádí validaci emailu a kontrolu povinných polí.
     *
     * @api
     * @param string      $_POST['name']     Jméno uživatele.
     * @param string      $_POST['email']    Emailová adresa.
     * @param string      $_POST['password'] Heslo (bude zahashováno v modelu).
     * @param string      $_POST['phone']    Telefonní číslo.
     * @param string      $_POST['role']     Role uživatele (výchozí: 'user').
     * @param array|null  $_FILES['photo']   Profilová fotka (volitelné).
     * @return void Vypíše JSON odpověď s vytvořeným uživatelem nebo chybou.
     */
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
            if(isset($newUser['id'])){
                echo json_encode(["message"=>"The user is created", "user"=>$newUser], JSON_PRETTY_PRINT);

            }
            else{
                http_response_code(404);
                echo json_encode(["message"=>$newUser], JSON_PRETTY_PRINT);

            }

        }
    }

    /**
     * Přihlásí uživatele do systému.
     *
     * Ověří email a heslo. Při úspěchu regeneruje ID session a uloží `user_id`.
     *
     * @api
     * @param string $_POST['email']    Email uživatele.
     * @param string $_POST['password'] Heslo uživatele.
     * @return void Vypíše JSON odpověď o výsledku přihlášení.
     */
    public function login(){

        $email = $_POST['email'];
        $password = $_POST['password'];
        session_regenerate_id(true);
        if(!$email || !$password){
            http_response_code(400);
            echo json_encode(["message"=>"Něco chybi"], JSON_PRETTY_PRINT);
        }
        else{
            $loggedUser = $this->User->login($email, $password);
            if(!isset($loggedUser["id"])){
                http_response_code(401);
                echo json_encode(["message"=>$loggedUser,"result"=>false],JSON_PRETTY_PRINT);
            }
            else{
                $_SESSION["user_id"] = $loggedUser['id'];
                echo json_encode(["message"=>"login was succesfull","user_id"=>$loggedUser['id'],"result"=>true], JSON_PRETTY_PRINT);
            }
        }
    }

    /**
     * Aktualizuje profil přihlášeného uživatele.
     *
     * Umožňuje změnu údajů a profilové fotky.
     * Pokud se nahrává nová fotka, stará je smazána (unlink) a nahrazena novou.
     *
     * @api
     * @return void Vypíše JSON odpověď s aktualizovanými daty.
     */
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

    /**
     * Získá seznam všech uživatelů.
     *
     * Tato metoda je dostupná pouze pro uživatele s rolí 'admin'.
     *
     * @api
     * @return void Vypíše JSON seznam uživatelů nebo chybu 404 (Access denied).
     */
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

    /**
     * Získá data aktuálně přihlášeného uživatele (Autorizace).
     *
     * Slouží k ověření, zda je uživatel stále přihlášen a k načtení jeho dat pro frontend.
     *
     * @api
     * @return void Vypíše JSON data uživatele.
     */
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

    /**
     * Získá uživatele podle ID z URL.
     *
     * @api
     * @param int $_GET['id'] ID hledaného uživatele.
     * @return void Vypíše JSON data uživatele.
     */
    public function getById():void{
        $user = $this->User->getUserById($_GET['id']);
        if(!$user){
            http_response_code(500);
            echo json_encode(["message"=>"Something went wrong"], JSON_PRETTY_PRINT);

        }
        echo json_encode(["message"=>"The data is retrieved", "user"=>$user], JSON_PRETTY_PRINT);
    }

    /**
     * Odhlásí uživatele.
     *
     * Zničí aktuální session (session_destroy).
     *
     * @api
     * @return void Vypíše JSON potvrzení o odhlášení.
     */
    public function logout(){
        session_destroy();
        http_response_code(200);
        echo json_encode(["message"=>"The user is logged out"]);
    }

    /**
     * Smaže uživatele.
     *
     * Umožní smazání pouze pokud je přihlášený uživatel administrátor,
     * nebo pokud uživatel maže svůj vlastní účet.
     *
     * @api
     * @param int $_GET['id'] ID uživatele ke smazání.
     * @return void Vypíše JSON o úspěchu nebo chybě přístupu.
     */
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