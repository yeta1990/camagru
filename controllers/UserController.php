<?php

    class UserController extends Controller{

        private $userService;
        private $jwtService;
        private $authService;

        public function __construct(){
            $this->jwtService = new JwtService(getenv("JWT_PASS"));
            $this->userService = new UserService();
            $this->authService = new AuthService([], []);
            $this->initRoutes();
        }

        protected function initRoutes() {
            $this->addRoute('GET', 'api/user/view', 'viewProfile');
            $this->addRoute('GET', 'api/user/whoami', 'whoami');
            $this->addRoute('GET', 'api/user/verify', 'verify');
            $this->addRoute('POST', 'api/user/login', 'loginCheck');
            $this->addRoute('GET', 'api/user/login2', 'loginGetToken');
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
                    $token = $this->jwtService->generateToken($user["id"], 600);
                    MailService::send(
                        $user['email'],
                        $user['username'],
                        'Login camagru-albgarci',
                        'To log into camagru-albgarci follow this link (valid for 10 min): <a href="http://localhost:8080/api/user/login2?token=' . $token . '">Verify</a>');
                    echo json_encode(["code" => 200, "message"=>"Good credentials provided! To finish login, click on the link sent to your email"]);
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

        protected function loginGetToken(){


            if(isset($this->query["token"]) && $this->jwtService->validate($this->query["token"])){
                $token = $this->query["token"];
                $userId = $this->jwtService->getUserId($token);
                $userToken = $this->jwtService->generateToken($userId);
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: /loginok?token=" . $userToken);
                exit;
            } else{
                http_response_code(400);
                echo json_encode("Bad token, probably expired, try to login again");
                exit;
            }
        }

        protected function whoami(){
            $token = $this->jwtService->getBearerToken();
            $id = $this->jwtService->getUserId($token);
            if ($id){
                echo json_encode($this->userService->getUserById($id)->getObjectVars());
            }

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
                $this->userService->sendVerificationEmail($input_parsed, $user_id);
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
            else if (isset($this->query["token"])){
                $token = $this->query["token"];
                $userId = $this->jwtService->getUserId($token);
                $user = $this->userService->getUserById($userId);
                if (!$user){
                    echo "user not found, are you manipulating the jwt?";
                    exit;
                }
                $userData = $user->getObjectVars();
                if ($userData["confirmed"] == 1){
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: /verified");
                    exit;
                }
                $this->userService->sendVerificationEmail($userData, $userId);
                echo json_encode("your verification link expired, we've sent to you another one by email");
                exit;
            }
            else {
                echo "not verified";
                exit;
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