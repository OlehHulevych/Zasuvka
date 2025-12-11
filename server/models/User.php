<?php
require_once __DIR__ . "/FavoriteList.php";
class User {
    private $file = __DIR__ . "/../data/users.json";
    private $FavoriteList;

    public function __construct(){
        $this->FavoriteList = new FavoriteList();
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

    public function getAll(){
       return $this->getData();

    }

    public function getAllUser($page){
        $users = $this->getData();
        $userPaginated = $this->paginate($users, $page);
        if($userPaginated){
            return $userPaginated;
        }
        else{
            return null;
        }
    }

    public function getCountOfUsers(){
        $users =$this->getData();
        return count($users);
    }
    public function getUserById($id){
        $users = $this->getData();
        foreach ($users as $user){
            if($user['id']==$id){
                return $user;
            }
        }
        return null;

    }

    public function create($name, $email, $photoPath, $phone, $password, $role){
        $checkuser = false;
        $users = $this->getData();
        foreach ($users as $user){
            if($user['email']==$email){
                $checkuser = true;
            }
        }
        if($checkuser){
            return null;
        }
        $newid = count($users) ? end($users)['id']+1:1;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $newUser = ["id"=>$newid, "name"=>$name, "email"=>$email, "photoPath" => $photoPath, "phone"=>$phone, "password"=>$hashedPassword, "role"=>$role ];
        $users[] = $newUser;
        $this->saveData($users);
        $this->FavoriteList->create($newid);
        return $newUser;
    }



    public function login($email, $password){
        $users = $this->getData();
        $foundUser = null;
        foreach ($users as $user){
            if($user['email']==$email){
                $foundUser = $user;
            }
        }
        if($foundUser==null){
            return null;
        }
        else{
            if(password_verify($password, $foundUser['password'])){
                return $foundUser;
            }
            else{
                return null;
            }
        }
    }

    public function update($id,$name, $email,  $phone, $password){
        $users = $this->getData();
        $updatedUser = null;

        foreach ($users as &$user){
             if($user['id']==$id){
                 $user['name'] = $name ?? $user['name'];
                 $user['email'] = $email ?? $user['email'];
                 $user['phone'] = $phone ?? $user['phone'];
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
    private function paginate($items, $page){
        $limit = 5;
        $total = count($items);
        $totalPages = ceil($total/$limit);
        $offset = ($page-1)*$limit;
        $paginated = array_slice($items, $offset,$limit);
        return ["items"=>$paginated, "totalPages"=>$totalPages, "page"=>$page];
    }
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
    public function delete($id){
        $users = $this->getData();
        $userForDeleting  = $this->getUserById($id);
        $photo = $userForDeleting['photoPath'];
        $fullPath = __DIR__ . "/../" . $photo;
        if(file_exists($fullPath)){
            unlink($fullPath);
        }
        $filteredUsers = array_filter($users, fn($user)=>$user['id'] != $id);
        $this->saveData($filteredUsers);
        $this->FavoriteList->delete($id);
        return true;
    }



}
