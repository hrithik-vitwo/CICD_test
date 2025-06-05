<?php

require_once("vendor/autoload.php");
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class JwtToken{
    // private $key = "Vitwo@1234#Auth#Token";
    private $key = "96fgv7650TKZChXkUMoGDwg72J4Qk7uWU0TKZChXkUMo30sJGNiJ9";
    function createToken($data=[], $tokenDurationInSeconds = null){
        $payload = [
            'iss' => 'vitwo.ai',
            'aud' => 'vitwo.ai',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time()+(60*60*24*30) //1month
        ]+$data;

        if($tokenDurationInSeconds>0){
            $payload['exp'] = time()+$tokenDurationInSeconds;
        }

        return JWT::encode($payload, $this->key, 'HS256');
    }
    function verifyToken($jwtToken){
        try{
            $decoded = JWT::decode($jwtToken, new Key($this->key, 'HS256'));
            return [
                "status"=>"success",
                "data" => (array) $decoded
            ];
        }catch(Exception $e){
            return [
                "status"=>"warning",
                "data" => []
            ];
        }
    }
}



?>