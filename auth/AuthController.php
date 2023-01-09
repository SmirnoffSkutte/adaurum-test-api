<?php
require_once 'controller.php';
require_once 'auth/AuthService.php';

class AuthController extends Controller {
    public function login(){
        $body=$this->getArrayedJsonBodyRequest();
        $authService=new AuthService();
        $user=$authService->login($body['email'],$body['password']);
        return $user;
    }

    public function registration(){
        $body=$this->getArrayedJsonBodyRequest();
        $authService=new AuthService();
        $newUser=$authService->registration($body['email'],$body['password'],$body['name']);
        return $newUser;
    }

    public function refreshTokens(){
        $body=$this->getArrayedJsonBodyRequest();
        $refreshToken=$body['refreshToken'];
        if($refreshToken===null){
            exit('Нет refreshToken');
        }
        $jwtService=new JwtService();
        $newTokens=$jwtService->refreshTokenPair($refreshToken);
        return json_encode($newTokens);
    }
}