<?php

    require_once "dbService.php";
    require_once "models/UserModel.php";

    class UserService {

        private $db;

        public function __construct(){
            $this->db = new DbService();
        }

        public function getUserById($id){
            $results = $this->db->findById("users", $id)->fetchArray();
            if (count($results) == 0){
                return null;
            }
            $foundUser = new User($results["email"], $results["username"], "", $results["confirmed"]);
            $foundUser->setId($results["id"]);
            return $foundUser;
        }

        public function createUser($email, $username, $password){
            $user = new User($email, $username, $password);
            $user->create($user);

            //to do: catch result and exceptions
            return $user;
        }
    }

?>