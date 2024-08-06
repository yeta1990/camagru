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
            $this->addRoute('GET', 'api/user/verify', 'verify');
            $this->addRoute('POST', 'api/user/login', 'loginCheck');
            $this->addRoute('POST', 'api/user/signup', 'signup');
            $this->addRoute('POST', 'api/user/edit', 'edit');
            $this->addRoute('POST', 'api/user/recover', 'recover');
            $this->addRoute('POST', 'api/user/notifications', 'toggleNotifications');
        }

        protected function loginCheck(){
            $request_body = json_decode(file_get_contents('php://input'), true);
            if (isset($request_body["email"]) && isset($request_body["password"])){
                $username = $request_body['email'];
                $password = $request_body['password'];
                $user = $this->userService->checkPassword($username, $password);
                if ($user && $user["confirmed"]){ 
                    $token = $this->jwtService->generateToken($user["id"]);
                    echo json_encode(["token" => $token]);
                }
                else if ($user){
                    echo json_encode(["message" => "Before your first login you must confirm your account. Check your email!"]);
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
                $user_id = $this->userService->signUp($input_parsed["email"], $input_parsed["username"], $input_parsed["password"]);
                $confirmationToken = $this->jwtService->generateConfirmationAccountToken($user_id);
                MailService::send(
                    $input_parsed['email'],
                    $input_parsed['username'],
                    'Confirm registration in camagru-albgarci',
                    'Verify your account in camagru-albgarci: <a href="http://localhost:8080/api/user/verify?token=' . $confirmationToken . '">Verify</a>');
                echo json_encode(["code" => 200, "message"=>"ok"]);
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

        protected function verify(){
            if(isset($this->query["token"]) && $this->jwtService->validate($this->query["token"])){
                $token = $this->query["token"];
                $userId = $this->jwtService->getUserId($token);
                $this->userService->confirmUser($userId);
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: /verified");
            }
            else{
                //to do, send a new verification email
                echo "not verified";
            }
        }


        protected function recover(){
            $request_body = json_decode(file_get_contents('php://input'), true);
            if(isset($request_body["email"]) ){
                $this->userService->recover($request_body["email"]);
            }
            echo json_encode(["code" => 200, "message"=>"ok"]);

        }

        protected function toggleNotifications(){
            $token = $this->jwtService->getBearerToken();
            $userId = $this->jwtService->getUserId($token);
            $this->userService->toggleNotifications($userId);
            return $this->whoami();
        }
    }
?>