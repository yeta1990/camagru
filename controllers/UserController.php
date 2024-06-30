<?php

    class UserController extends Controller{

        private $userService;
        private $jwtService;

        public function __construct(){
            $this->jwtService = new JwtService("keyff");
            $this->userService = new UserService();
            $this->initRoutes();
        }

        protected function initRoutes() {
            $this->addRoute('GET', 'api/user/view', 'viewProfile');
            $this->addRoute('GET', 'api/user/edit', 'edit');
            $this->addRoute('GET', 'api/user/whoami', 'whoami');
            $this->addRoute('POST', 'api/user/update', 'update');
            $this->addRoute('POST', 'api/user/login', 'loginCheck');
            $this->addRoute('POST', 'api/user/signup', 'signup');
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
            try {
                $id = 1;
                $user = $this->userService->getUserById($id)->getObjectVars();
                exit;
                //to do: return result
    
            } catch (Exception $e) {
                http_response_code(401);
            }
        }

        protected function update(){
            $input_parsed = array();
            parse_str(file_get_contents('php://input'), $input_parsed);

            if (isset($input_parsed['id'], $input_parsed['username'], $input_parsed['email'])) {
                $userId = $input_parsed['id'];
                $username = $input_parsed['username'];
                $email = $input_parsed['email'];
                $userToUpdate = new User($email, $username);
                $userToUpdate->setId($userId);
                $this->userService->update($userToUpdate);
            } else {
                header("HTTP/1.0 400 Bad Request");
                return;
            }
        }

        protected function signup(){
            $input_parsed = array();
            parse_str(file_get_contents('php://input'), $input_parsed);
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
                //require_once 'views/user/viewUser.php';
            } else {
                header("HTTP/1.0 400 Bad Request");
                return;
            }
        }

    }
?>