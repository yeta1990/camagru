<?php

    class LoginController extends Controller {

        public function __construct(){
        }

        protected function handleGet($action, $query){
            switch ($action[0]) {
                case '':
                    $this->viewLoginForm();
                    break;
                default:
                    header("HTTP/1.0 404 Not Found");
                    echo "404 Not Found";
                    break;
            }
        }

        protected function handlePost($action, $params){
            if (count($action) == 0){
                header("HTTP/1.0 404 Not Found");
                echo "404 Not Found";
                return ;
            }
            switch ($action[0]) {
                default:
                    header("HTTP/1.0 404 Not Found");
                    echo "404 Not Found";
                    break;
            }
            
        }

        protected function viewLoginform(){
            require_once 'views/login/login.php';
        }
    }

?>