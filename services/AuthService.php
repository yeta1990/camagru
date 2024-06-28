<?php

    class AuthService {
        //$decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
        //$userId = $decoded->user_id; // Asumiendo que el ID del usuario está en 'user_id'
        /*
        if (!isset($headers['Authorization'])) {
            header("HTTP/1.0 401 Unauthorized");
            echo "401 Unauthorized";
            return;
        }
        */

        private $whitelist;
        private $path;
        private $method;
        private $jwtService;

        public function __construct($whitelist){
            $this->whitelist = $whitelist;
            $this->path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $this->method = $_SERVER['REQUEST_METHOD'];
            $this->jwtService = new JwtService("keyff");
        }

        private function getBearerToken(): ?string{
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                return trim(str_replace('Bearer', '', $headers['Authorization']));
            }
            return null;
        }

        private function hasValidToken(){
            //$this->getBearerToken();
            return $this->jwtService->validate($this->getBearerToken());
        }

        private function isWhiteListRoute() {
            $apiStr = "api";
            $len = strlen($apiStr);
            if (substr($this->path, 0, $len) != $apiStr){
                return true;
            }
            foreach ($this->whitelist as $route) {
                if (trim($route["path"],'/') == $this->path && $route["method"] == $this->method) {
                    return true;
                }
            }
            return false;
        }

        public function checkPath(){
            //$this->hasValidToken();
            
            if (!$this->isWhiteListRoute() && !$this->hasValidToken()){
                header("HTTP/1.0 401 Unauthorized");
                echo "401 Unauthorized";
                exit;
            }
            
        }
    }

?>