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
        private $whitelistOrigins;
        private $path;
        private $method;
        private $jwtService;

        public function __construct($whitelist, $whitelistOrigins){
            $this->whitelist = $whitelist;
            $this->whitelistOrigins = $whitelistOrigins;
            $this->path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $this->method = $_SERVER['REQUEST_METHOD'];
            $this->jwtService = new JwtService("keyff");
        }

        private function getBearerToken(){
            return $this->jwtService->getBearerToken();
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


        private function isWhiteListOrigin() {
            foreach ($this->whitelistOrigins as $route) {
                if (trim($route["path"],'/') == $this->path && $route["method"] == $this->method) {
                    return true;
                }
            }
            return false;
        }

        public function checkPath(){
            if ($this->isWhiteListRoute() && $this->checkCORS()){
                return true;
            }
            $this->checkCORS();
            if (!$this->isWhiteListRoute() && !$this->hasValidToken()){
                header('HTTP/1.1 403 Forbidden');
                echo json_encode([
                    'error' => 'Invalid origin'
                ]);
                exit; 
            }
        }

        public function hasEnoughPrivileges(){
            $token = $this->jwtService->getBearerToken();
            $id = $this->jwtService->getUserId($token);
            $request_body = json_decode(file_get_contents('php://input'), true);

            if ($id != $request_body["id"]){
                return false;
            }
            return true;

        }

        private function checkCORS() {
            
            if ($this->isWhiteListOrigin()){
                return true;
            }
            

            $allowed_origin = "http://localhost:8080";
            $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

            if ($origin) {
                $referer_origin = parse_url($referer, PHP_URL_HOST);
                $allowed_origin = parse_url($allowed_origin, PHP_URL_HOST);
                if ($referer_origin != $allowed_origin){
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode([
                        'error' => 'Invalid origin'
                    ]);
                    exit; 
                }
            }


            if (!$origin && $referer) {
                $referer_host = parse_url($referer, PHP_URL_HOST);
                $allowed_host = parse_url($allowed_origin, PHP_URL_HOST);
                
                if ($referer_host !== $allowed_host) {
                    header('HTTP/1.1 403 Forbidden');
                    echo json_encode(['error' => 'Invalid referer']);
                    exit;
                }
            }

            if (!$origin && !$referer) {
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(['error' => 'No origin or referer header present']);
                exit;
            }
        }
    }

?>