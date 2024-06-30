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
            return $this->jwtService->validate($this->getBearerToken());
        }

        private function isWhiteListRoute() {
            foreach ($this->whitelist as $route) {
                if (trim($route["path"],'/') == $this->path && $route["method"] == $this->method) {
                    return true;
                }
            }
            return false;
        }

        public function checkPath(){
            if (!$this->isWhiteListRoute() && !$this->hasValidToken()){
                http_response_code(401);
            }
        }
    }

?>