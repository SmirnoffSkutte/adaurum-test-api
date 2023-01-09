<?php

require_once 'jwt/JwtService.php';
require_once 'db/db.php';

class AuthService{

    public function login(string $email,string $password){
        $userData=$this->validateUser($email,$password);
//        print_r($userData);
        $jwt=new JwtService();
        $tokens=$jwt->createNewTokenPair($userData);
        $userFields=$this->getUserFields($userData);
        $userInfo=[
            'user'=>$userFields,
            'tokens'=>$tokens
        ];
        return json_encode($userInfo);
    }

    public function registration(string $email,string $password,string $name){
        try {
            $db=new Database();
            $jwt=new JwtService();
            $oldUser=$db->query("SELECT * FROM users WHERE email='$email'");
            if ($oldUser){
                throw new Exception('Пользователь с таким email уже есть');
            }
            if(filter_var($email,FILTER_VALIDATE_EMAIL)===false){
                throw new Exception('Нет email или строка не является email');
            }
            if (strlen(trim($password))<5){
                http_response_code(400);
                throw new Exception('Пароль должен быть минимум 5 символом');
            }
            $hashPassword=password_hash($password,PASSWORD_DEFAULT);
//            Creating new user
            $db->query("INSERT INTO users (email,password,name) VALUES (:email,:password,:name)",[
                'email'=>$email,
                'password'=>$hashPassword,
                'name'=>$name
            ]);
            $newUser=$db->query("SELECT * FROM users WHERE email='$email'");
            $tokens=$jwt->createNewTokenPair($newUser[0]);
            $newUserFields=$this->getUserFields($newUser[0]);
            $userInfo=[
                'user'=>$newUserFields,
                'tokens'=>$tokens
            ];
            return json_encode($userInfo);
        } catch (Exception $e){
            http_response_code(400);
            $err=['message'=>$e->getMessage()];
            die(json_encode($err));
        }

    }

    private function validateUser(string $email,string $password){
        try{
            $db=new Database();
            $isValidPassword=false;
            $userQuery=$db->query("SELECT * FROM users WHERE email='$email'");
            if (count($userQuery)===0){
                http_response_code(404);
                throw new Exception('Пользователя с таким email нет');
            }
            $user=$userQuery[0];
            if (password_verify($password,$user['password'])){
                $isValidPassword=true;
            } else {
                http_response_code(406);
                throw new Exception('Пароли не совпадают');
            }
            if ($isValidPassword && isset($user)){
                return $user;
            }

        } catch (Exception $e){
            die($e->getMessage());
        }
    }

    private function getUserFields(array $userInfo):array{
        unset($userInfo['password']);
        return $userInfo;
    }
}