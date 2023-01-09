<?php

class Controller{
    protected function getJsonBodyRequest(){
        $input = file_get_contents("php://input");
        return $input;
    }
    protected function getArrayedJsonBodyRequest(){
        $data = json_decode(file_get_contents('php://input'), true);
        return $data;
    }

    public function getAuthBearerToken():string
    {
        try{
            $reqHeaders=getallheaders();
            $bearerTokenHeader=$reqHeaders['Authorization'];
            if (!isset($bearerTokenHeader)){
                throw new Exception('Отсутствует Authorization Header');
            }
            $bearerToken ='';
            if (substr($bearerTokenHeader, 0, 7) !== 'Bearer ') {
                throw new Exception('Отсутствует токен авторизации внутри заголовка');
            } else {
                $bearerToken=trim(substr($bearerTokenHeader, 7));
            }
        } catch (Exception $e){
            http_response_code(401);
            die($e->getMessage());
        }
        return $bearerToken;
    }


    protected function identifyUserId(){
            $token = $this->getAuthBearerToken();
            $jwt = new JwtService();
            $userId = $jwt->identifyUsersIdAndVerifyToken($token);
            return $userId;
    }

}