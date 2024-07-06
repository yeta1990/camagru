<?php

class JwtService {


    public function __construct(private $jwtKey)
    {

    }

    private function base64URLEncode($str){
        $str64 = base64_encode($str);
        if ($str64 === false) {
            return false;
        }
        $strUrl64 = strtr($str64, '+/', '-_');
        return rtrim($strUrl64, '=');
    }

    private function base64URLDecode($strUrl64){
        $str = strtr($strUrl64, '-_', '+/');
        return base64_decode($str);
    }

    public function getBearerToken(){
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            return trim(str_replace('Bearer', '', $headers['Authorization']));
        }
        return null;
    }

    public function encode($payload){

        $header = json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]);

        $header = $this->base64URLEncode($header);
        $payload = json_encode($payload);
        $payload = $this->base64URLEncode($payload);

        $signature = hash_hmac("sha256", $header . "." . $payload, $this->jwtKey, true);
        $signature = $this->base64URLEncode($signature);
        return $header . "." . $payload . "." . $signature;

    }

    public function decode($token){
        if (preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",$token,$matches) !== 1) {
            return false;
        }
        return $matches;
    }

    public function getExpirationDate($token){
        return json_decode($this->getDecodedToken($token), true)["exp"];
    }

    public function getUserId($token){
        return json_decode($this->getDecodedToken($token), true)["user_id"];
    }

    public function getDecodedToken($token){
        $matches = $this->decode($token);
        $payload = $this->base64URLDecode($matches["payload"]);
        return $payload;
    }

    public function validate($token) {
        if (!$token){
            return false;
        }
        $matches = $this->decode($token);
        //calculating signature from the header and payload of the token, then comparing with the original signature
        if (!$matches){
            return false;
        }
        try {
            $calculated_signature = hash_hmac("sha256",$matches["header"] . "." . $matches["payload"],$this->jwtKey,true);
            $original_signature = $this->base64URLDecode($matches["signature"]);
        }
        catch (Exception $e){
            return false;
        }

        if (!hash_equals($calculated_signature, $original_signature)) {
            return false;
        }
        $payload = json_decode($this->base64URLDecode($matches["payload"]), true);
        $expiration_date = $this->getExpirationDate($token);
        if ($expiration_date < time()) {
            return false;
        }
        return $payload;
    }


    public function generateToken($user_id){

        $expiration_time = 604800; //7 days
        $payload = [
            "user_id" => $user_id,
            "exp" => time() + $expiration_time
        ];
        
        return $this->encode($payload);
    }

    public function generateConfirmationAccountToken($user_id){

        $expiration_time = 1800; //30min
        $payload = [
            "user_id" => $user_id,
            "exp" => time() + $expiration_time
        ];
        
        return $this->encode($payload);
    }


}


?>