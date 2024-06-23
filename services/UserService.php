<?php

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

        public function signUp($email, $username, $password){
            $user = new User($email, $username, $password);
            $user->create();

            //to do: catch result and exceptions
            return $user;
        }

        public function update($new_user){
            $new_user->update();
        }

        public function changePassword($id, $newPassword){
            $hashedPass = PasswordService::hash($newPassword);
            $this->db->query("UPDATE users SET password = \"{$hashedPass}\" WHERE id = {$id}");
        }

        public function checkPassword($email, $password){
            $user = $this->db->findByCustomField("users", "email", $email)->fetchArray();
            if ($user){ //not correct
                return password_verify($password, $user["password"]);
            }
            return false;
        }
    }

?>