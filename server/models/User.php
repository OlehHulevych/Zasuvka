<?php
/**
 * ... popis ...
 * @package App\Models
 */
require_once __DIR__ . "/FavoriteList.php";
require_once  __DIR__ . "/User.php";

/**
 * Třída User
 *
 * Slouží jako Model pro správu uživatelů.
 * Data jsou ukládána do JSON souboru (`data/users.json`).
 * Zajišťuje registraci (vytváření), přihlášení (autentizaci), úpravu profilu,
 * správu rolí a mazání uživatelů.
 *
 *
 * @package App\Models
 */
class User {
    /** @var string Cesta k souboru s daty uživatelů. */
    private $file = __DIR__ . "/../data/users.json";

    /** @var FavoriteList Instance modelu pro správu oblíbených položek. */
    private $FavoriteList;


    /**
     * Konstruktor třídy.
     * Inicializuje model FavoriteList.
     */
    public function __construct(){
        $this->FavoriteList = new FavoriteList();

    }

    /**
     * Načte data ze souboru JSON.
     *
     * Pokud soubor neexistuje, vytvoří nový.
     *
     * @return array Pole všech uživatelů.
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
     * Získá všechny uživatele bez stránkování.
     *
     * @return array Seznam všech uživatelů.
     */
    public function getAll(){
        return $this->getData();

    }

    /**
     * Získá uživatele se stránkováním.
     *
     * @param int $page Číslo požadované stránky.
     * @return array|null Data stránky (items, totalPages...) nebo null při chybě.
     */
    public function getAllUsers($page){
        $users = $this->getData();
        $userPaginated = $this->paginate($users, $page);
        if($userPaginated){
            return $userPaginated;
        }
        else{
            return null;
        }
    }

    /**
     * Vrátí celkový počet uživatelů v systému.
     *
     * @return int Počet uživatelů.
     */
    public function getCountOfUsers(){
        $users =$this->getData();
        return count($users);
    }

    /**
     * Vyhledá uživatele podle ID.
     *
     * @param int $id ID uživatele.
     * @return array|null Nalezený uživatel nebo null.
     */
    public function getUserById($id){
        $users = $this->getData();
        foreach ($users as $user){
            if($user['id']==$id){
                return $user;
            }
        }
        return null;

    }

    /**
     * Vytvoří nového uživatele (Registrace).
     *
     * Kontroluje duplicitu emailu. Heslo je před uložením zahashováno.
     * Po vytvoření uživatele mu automaticky založí prázdný seznam oblíbených položek.
     *
     * @param string $name      Jméno.
     * @param string $email     Email (unikátní identifikátor).
     * @param string $photoPath Cesta k nahrané fotce.
     * @param string $phone     Telefon.
     * @param string $password  Heslo (plain text).
     * @param string $role      Role ('user' nebo 'admin').
     * @return array|null Nově vytvořený uživatel nebo null, pokud email již existuje.
     */
    public function create($name, $email, $photoPath, $phone, $password, $role){
        $checkuser = false;
        $users = $this->getData();
        foreach ($users as $user){
            if($user['email']==$email){
                $checkuser = true;
            }
        }
        if($checkuser){
            return "Účet s tímto e-mailem již existuje";
        }
        $newid = count($users) ? end($users)['id']+1:1;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $newUser = ["id"=>$newid, "name"=>$name, "email"=>$email, "photoPath" => $photoPath, "phone"=>$phone, "password"=>$hashedPassword, "role"=>$role ];
        $users[] = $newUser;
        $this->saveData($users);

        // Vytvoření prázdného seznamu oblíbených pro nového uživatele
        $this->FavoriteList->create($newid);
        return $newUser;
    }


    /**
     * Ověří přihlášení uživatele.
     *
     * Porovná zadaný email a ověří heslo pomocí `password_verify`.
     *
     * @param string $email    Zadaný email.
     * @param string $password Zadané heslo.
     * @return array|null Data uživatele při úspěchu, jinak null.
     */
    public function login($email, $password){
        $users = $this->getData();
        $foundUser = null;
        foreach ($users as $user){
            if($user['email']==$email){
                $foundUser = $user;
            }
        }
        if($foundUser==null){
            return "Uživatel nebyl nalezen";
        }
        else{
            if(password_verify($password, $foundUser['password'])){
                return $foundUser;
            }
            else{
                return "Zadal jste neplatné heslo";
            }
        }
    }

    /**
     * Aktualizuje data uživatele.
     *
     * Pokud je zadáno nové heslo, automaticky se zahashuje.
     * Pokud je hodnota parametru null, zachová se původní hodnota.
     *
     * @param int         $id       ID uživatele.
     * @param string|null $name     Nové jméno.
     * @param string|null $email    Nový email.
     * @param string|null $phone    Nový telefon.
     * @param string|null $password Nové heslo.
     * @return array|null Aktualizovaný uživatel nebo null, pokud ID neexistuje.
     */
    public function update($id,$name, $email,  $phone, $password){
        $users = $this->getData();
        $updatedUser = null;

        foreach ($users as &$user){
            if($user['id']==$id){
                $user['name'] = $name ?? $user['name'];
                $user['email'] = $email ?? $user['email'];
                $user['phone'] = $phone ?? $user['phone'];
                // Pokud je heslo zadáno, zahashujeme ho, jinak necháme původní
                $user['password'] = $password ? password_hash($password, PASSWORD_DEFAULT) : $user['password'];
                $updatedUser = $user;
                break;
            }
        }
        if(isset($updatedUser)){
            $this->saveData($users);
            return $updatedUser;
        }
        else{
            return null;
        }

    }

    /**
     * Pomocná metoda pro stránkování.
     *
     * @param array $items Vstupní pole.
     * @param int   $page  Číslo stránky.
     * @return array Data stránky.
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
     * Přepíná roli uživatele mezi 'admin' a 'user'.
     *
     * @param int $id ID uživatele.
     * @return array|null Aktualizovaný uživatel nebo null.
     */
    public function promoteToAdminOrUser(int $id){
        $users = $this->getData();
        $updatedUser = null;
        foreach($users as &$user){
            if($user['id']==$id){
                $updatedUser = $user;
                if($user['role']=='user'){
                    $user['role'] = 'admin';
                }
                else{
                    $user['role'] = 'user';
                }

            }
        }
        if(isset($updatedUser)){
            $this->saveData($users);
            return $updatedUser;
        }
        else{
            return null;
        }
    }

    /**
     * Smaže uživatele.
     *
     * Kromě smazání záznamu z JSONu také:
     * 1. Smaže soubor s profilovou fotkou (pokud existuje).
     * 2. Smaže seznam oblíbených položek tohoto uživatele.
     *
     * @param int $id ID uživatele.
     * @return bool Vždy vrací true (pokud nenastane fatální chyba).
     */
    public function delete($id){
        $users = $this->getData();
        $userForDeleting  = $this->getUserById($id);

        // Smazání fyzického souboru s fotkou
        $photo = $userForDeleting['photoPath'];
        $fullPath = __DIR__ . "/../" . $photo;
        if(file_exists($fullPath)){
            unlink($fullPath);
        }
        $product = new Product();
        $products = $product->getData();
        foreach ($products as &$item){
            if($item['userId']==$id){
                $product->delete($item['id'], $id);
            }
        }
        // Smazání záznamu uživatele
        $filteredUsers = array_filter($users, fn($user)=>$user['id'] != $id);
        $this->saveData($filteredUsers);

        // Smazání navázaného seznamu oblíbených
        $this->FavoriteList->delete($id);
        return true;
    }
}