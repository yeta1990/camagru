<?php

    require_once "dbService.php";
    require_once "models/UserModel.php";

    class UserService {

        private $db;

        public function __construct(){
            $this->db = new DbService();
        }

        public function getUserById($id){
            $results = ($this->db->query('SELECT id, email, username, confirmed from users where id = ' . strval($id)))->fetchArray();
            if (count($results) == 0){
                return null;
            }
            return new User($results["email"], $results["username"], "", $results["confirmed"]);
        }

        public function createUser($email, $username, $password){
            $user = new User($email, $username, $password);
            $this->db->insert($user);
            //to do: catch result and exceptions
            /*return $this->user;*/
        }
    }

?>