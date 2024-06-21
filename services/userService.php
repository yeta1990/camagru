<?php

    require_once "dbService.php";
    require_once "models/UserModel.php";

    class UserService {

        private $db;
        private $user;

        public function __construct(){
            $this->db = new DbService();
        }

        public function getUserById($id){
            $this->db->query('SELECT * from users where id = ' + $id);

        }

        public function createUser($userObject){
            $this->user = new User(1, 2, 3, 4, 5);
            return $this->user;
        }
    }

?>