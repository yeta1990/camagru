<?php

    class UserController extends Controller{

        private $userService;
        private $jwtService;

        public function __construct(){
            $this->jwtService = new JwtService("keyff");
            $this->userService = new UserService();
        }

        protected function handleGet($action, $query) {
            if (count($action) == 0){
                header("HTTP/1.0 404 Not Found");
                echo "404 Not Found";
                return ;
            }
            switch ($action[0]) {
                case 'view':
                    $this->viewProfile($query);
                    break;
                case 'edit':
                    $this->edit($query);
                    break;
                default:
                    header("HTTP/1.0 404 Not Found");
                    echo "404 Not Found";
                    break;
            }
        }

        protected function handlePost($action, $query) {
            if (count($action) == 0){
                header("HTTP/1.0 404 Not Found");
                echo "404 Not Found";
                return ;
            }
            switch ($action[0]) {
                case 'update':
                    $this->update($query);
                    break;
                case 'login':
                    $this->loginCheck($query);
                    break;
                default:
                    header("HTTP/1.0 404 Not Found");
                    echo "404 Not Found";
                    break;
            }
        }

        private function loginCheck($query){
            $request_body = json_decode(file_get_contents('php://input'), true);
            $username = $request_body['email'];
            $password = $request_body['password'];
            $result = $this->userService->checkPassword($username, $password);
            if ($result){
                $token = $this->jwtService->generateToken("1");
                header("ok", true, 200);
                echo json_encode(["token" => $token]);
                exit ;
            }
            else {
                http_response_code(401);
                echo "invalid auth";
                return;
            }
        }

        private function edit(){
            $headers = getallheaders();
            /*
            if (!isset($headers['Authorization'])) {
                header("HTTP/1.0 401 Unauthorized");
                echo "401 Unauthorized";
                return;
            }
            */
            try {
                //$decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
                //$userId = $decoded->user_id; // Asumiendo que el ID del usuario está en 'user_id'
                $id = 1;
                $user = $this->userService->getUserById($id)->getObjectVars();
                $this->renderEditView($user);
    
                // Load user data and show edit view
            } catch (Exception $e) {
                header("HTTP/1.0 401 Unauthorized");
                echo "401 Unauthorized";
            }
        }

        private function update(){
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
            require_once 'views/user/editUserOk.php';
        }

        private function viewProfile($query){
            if (isset($query["id"])){
                $user = $this->userService->getUserById($query["id"])->getObjectVars();
                echo json_encode($user);
                //require_once 'views/user/viewUser.php';
            } else {
                header("HTTP/1.0 400 Bad Request");
                return;
            }
        }

        private function renderEditView($user){
            require_once 'views/user/editUser.php';
        }

    }
?>