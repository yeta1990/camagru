<?php

    class UserController extends Controller{

        private $userService;

        public function __construct(){
            $this->userService = new UserService();
        }

        protected function handleGet($action, $query) {
            if (count($action) == 0){
                if (isset($query["id"])){
                    $this->userService->getUserById($query["id"])->print();
                }
            } else {
                switch ($action[0]) {
                    case 'edit':
                        $this->edit($query);
                        break;
                    // Add more GET actions here
                    default:
                        header("HTTP/1.0 404 Not Found");
                        echo "404 Not Found";
                        break;
                }
            }
        }

        protected function handlePost($action, $params) {
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
                //$userId = $decoded->sub; // Asumiendo que el ID del usuario está en 'sub'
                $id = 1;
                $user = $this->userService->getUserById($id)->getObjectVars();
                $this->renderEditView($user);
    
                // Load user data and show edit view
            } catch (Exception $e) {
                header("HTTP/1.0 401 Unauthorized");
                echo "401 Unauthorized";
            }
        }

        private function renderEditView($user){
            require_once 'views/user/editUser.php';
        }

    }
?>