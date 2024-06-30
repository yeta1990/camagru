<?php

    class UserController extends Controller{

        private $userService;
        private $jwtService;
        private $authService;

        public function __construct(){
            $this->jwtService = new JwtService("keyff");
            $this->userService = new UserService();
            $this->authService = new AuthService([]);
            $this->initRoutes();
        }

        protected function initRoutes() {
            $this->addRoute('GET', 'api/user/view', 'viewProfile');
            $this->addRoute('GET', 'api/user/whoami', 'whoami');
            $this->addRoute('POST', 'api/user/update', 'update');
            $this->addRoute('POST', 'api/user/login', 'loginCheck');
            $this->addRoute('POST', 'api/user/signup', 'signup');
            $this->addRoute('POST', 'api/user/edit', 'edit');
        }

        protected function loginCheck(){
            $request_body = json_decode(file_get_contents('php://input'), true);
            if (isset($request_body["email"]) && isset($request_body["password"])){
                $username = $request_body['email'];
                $password = $request_body['password'];
                $user = $this->userService->checkPassword($username, $password);
                if ($user){
                    $token = $this->jwtService->generateToken($user["id"]);
                    echo json_encode(["token" => $token]);
                }
                else {
                    http_response_code(401);
                }
            }else{
                http_response_code(400);
            }
        }

        protected function whoami(){
            $token = $this->jwtService->getBearerToken();
            $id = $this->jwtService->getUserId($token);
            echo json_encode($this->userService->getUserById($id)->getObjectVars());
        }

        protected function edit(){
            if (!$this->authService->hasEnoughPrivileges()){
                http_response_code(400);
                exit ;
            }
            $request_body = json_decode(file_get_contents('php://input'), true);

            if (isset($request_body['id'], $request_body['username'], $request_body['email'])) {
                $this->userService->update($request_body["id"],$request_body["email"], $request_body["username"]);
            }
            if (isset($request_body['id'], $request_body['password']) && strlen($request_body['password']) > 0){
                $this->userService->changePassword($request_body['id'], $request_body['password']);
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($request_body);
        }

        protected function signup(){
            $input_parsed = json_decode(file_get_contents('php://input'), true);
            if (isset($input_parsed['username'], $input_parsed['email'], $input_parsed["password"])) {
                $this->userService->signUp($input_parsed["email"], $input_parsed["username"], $input_parsed["password"]);
            } 
            else{
                http_response_code(400);
            }
        }

        protected function viewProfile(){
            if (isset($this->query["id"])){
                $user = $this->userService->getUserById($this->query["id"])->getObjectVars();
                echo json_encode($user);
            } else {
                header("HTTP/1.0 400 Bad Request");
                return;
            }
        }

    }
?>