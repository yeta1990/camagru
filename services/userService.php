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
            $this->user = new User(223, 33, 44);
            return $this->user;
        }
    }

?>