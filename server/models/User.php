<?php
require_once __DIR__ . "/FavoriteList.php";
class User {
    private $file = __DIR__ . "/../data/users.json";
    private $FavoriteList;

    public function __construct(){
        $this->FavoriteList = new FavoriteList();
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
       return $this->file;

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

    public function create($name, $email, $photoPath, $phone, $password){
        $users = $this->getData();
        $newid = count($users) ? end($users)['id']+1:1;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $newUser = ["id"=>$newid, "name"=>$name, "email"=>$email, "photoPath" => $photoPath, "phone"=>$phone, "password"=>$hashedPassword ];
        $items[] = $newUser;
        $this->saveData($items);
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

    public function update($id,$name, $email, $photoPath, $phone){
        $users = $this->getData();
        foreach ($users as $user){
             if($user==$id){
                 $user['name'] = $name;
                 $user['email'] = $email;
                 $user['photoPath'] = $photoPath;
                 $user['phone'] = $phone;
                 $this->saveData($users);
                 return $user;
             }
        }
        return null;
    }
    public function delete($id){
        $users = $this->getData();
        $filteredUsers = array_filter($users, fn($user)=>$user['id'] != $id);
        $this->saveData($filteredUsers);
        $this->FavoriteList->delete($id);
        return true;
    }



}
