<?php

class JwtService{
    private function base64url_encode($data):string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64url_decode($data):string {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public function verifyToken(string $token):bool{
        try {
            $tokenParts=explode('.',$token);
            $headers_encoded=$tokenParts[0];
            $payload_encoded=$tokenParts[1];
            $userSignature=$tokenParts[2];

            $payload_decodedArray=json_decode($this->base64url_decode($payload_encoded),true);
            //checking time
            $currentTime=time();
            if($currentTime>$payload_decodedArray['exp']){
                throw new Exception('Токен истек');
                return false;
            }

            //build the signature to verify
            $key = 'wefjnnjwjef34230r0fewf';
            $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
            $signature_encoded = $this->base64url_encode($signature);

            if ($signature_encoded===$userSignature){
                return true;
            } else {
                throw new Exception('Токен не валиден');
                return false;
            }
        }
        catch (Exception $e){
            http_response_code(402);
            $err=['message'=>$e->getMessage()];
            die(json_encode($err));
        }
    }

    public function issueAccessToken(array $userData):string{
//        $userData=json_decode($userDataJson,true);
        $userTokenInfo=[];
        try {
            if (isset($userData['userId']) && isset($userData['email'])) {
                $userTokenInfo['userId'] = $userData['userId'];
                $userTokenInfo['email'] = $userData['email'];
                $userTokenInfo['iat'] = time();
                $userTokenInfo['exp'] = time() + 7200;
                //build the headers
                $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
                $headers_encoded = $this->base64url_encode(json_encode($headers));

                //build the payload
                //$payload = ['sub'=>'1234567890','name'=>'John Doe', 'admin'=>true];
                $payload_encoded = $this->base64url_encode(json_encode($userTokenInfo));

                //build the signature
                $key = 'wefjnnjwjef34230r0fewf';
                $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
                $signature_encoded = $this->base64url_encode($signature);

                //build and return the token
                $token = "$headers_encoded.$payload_encoded.$signature_encoded";
                return $token;
            } else {
                throw new Exception('Ошибка создания access токена');
            }
        } catch (Exception $e){
            die($e->getMessage());
        }

    }

    public function issueRefreshToken(array $userData):string{
//        $userData=json_decode($userDataJson,true);
        $userTokenInfo=[];
        try {
            if (isset($userData['userId']) && isset($userData['email'])) {
                $userTokenInfo['userId'] = $userData['userId'];
                $userTokenInfo['email'] = $userData['email'];
                $userTokenInfo['iat'] = time();
                $userTokenInfo['exp'] = time() + 604800;
                //build the headers
                $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
                $headers_encoded = $this->base64url_encode(json_encode($headers));

                //build the payload
                //$payload = ['sub'=>'1234567890','name'=>'John Doe', 'admin'=>true];
                $payload_encoded = $this->base64url_encode(json_encode($userTokenInfo));

                //build the signature
                $key = 'wefjnnjwjef34230r0fewf';
                $signature = hash_hmac('sha256', "$headers_encoded.$payload_encoded", $key, true);
                $signature_encoded = $this->base64url_encode($signature);

                //build and return the token
                $token = "$headers_encoded.$payload_encoded.$signature_encoded";
                return $token;
            } else {
                throw new Exception('Ошибка создания refresh токена');
            }
        } catch (Exception $e){
            return $e->getMessage();
        }

    }

    public function refreshTokenPair(string $refreshToken):array{
        $isValidToken=$this->verifyToken($refreshToken);
        $tokenParts=explode('.',$refreshToken);
        $payload_encoded=$tokenParts[1];

        $userData=json_decode($this->base64url_decode($payload_encoded),true);
        $newAccessToken='';
        $newRefreshToken='';
        try {
            if ($isValidToken) {
                $newAccessToken = $this->issueAccessToken($userData);
                $newRefreshToken = $this->issueRefreshToken($userData);
                return [
                    'accessToken' => $newAccessToken,
                    'refreshToken' => $newRefreshToken
                ];
            } else {
                throw new Exception('Токен невалиден');
            }
        } catch (Exception $e){
            http_response_code(400);
            die($e->getMessage());
        }
    }

    public function createNewTokenPair(array $userData):array{
        try {
            $accessToken=$this->issueAccessToken($userData);
            $refreshToken=$this->issueRefreshToken($userData);
            return [
              'accessToken'=>$accessToken,
              'refreshToken'=>$refreshToken
            ];

        } catch (Exception $e){
            http_response_code(400);
            die($e->getMessage());
        }
    }

    public function identifyUsersIdAndVerifyToken(string $token){
        $isValidToken=$this->verifyToken($token);
        if($isValidToken===false){
            http_response_code(401);
            return null;
        }
        if ($token===null){
            return null;
        }

        $tokenParts=explode('.',$token);
        $payload=$tokenParts[1];
        $decodedTokenPayload=json_decode($this->base64url_decode($payload),true);
        $userId=$decodedTokenPayload['userId'];
        return $userId;
    }
}