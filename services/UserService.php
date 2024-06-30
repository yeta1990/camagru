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
            $foundUser = new User($results["email"], $results["username"], $confirmed = $results["confirmed"]);
            $foundUser->setId($results["id"]);
            return $foundUser;
        }

        public function isUsernameAvailable($username){
            $num_users = $this->db->query("SELECT count(*) as count from users where username = \"{$username}\"")->fetchArray();
            if ($num_users["count"] == 0){
                return true;
            }
            return false;
        }


        public function isEmailAvailable($email){
            $num_users = $this->db->query("SELECT count(*) as count from users where email = \"{$email}\"")->fetchArray();
            if ($num_users["count"] == 0){
                return true;
            }
            return false;
        }

        public function signUp($email, $username, $password){

            if (!$this->isUsernameAvailable($username)){
                http_response_code(401);
                echo "Username not available";
                exit ;
            }
            if (!$this->isEmailAvailable($email)){
                http_response_code(401);
                echo "Email not available";
                exit ;
            }
            if (strlen($password) < 8){
                http_response_code(401);
                echo "Password must have at least 8 characters";
                exit ;
            }
            //to do: password validations 
            
            $user = new User($email, $username, $password);
            $user->create();

            //to do: send email to confirm signup

            //to do: catch result and exceptions
            
            return true;
        }

        public function update($new_user){
            $new_user->update();
        }

        public function changePassword($id, $newPassword){
            $hashedPass = PasswordService::hash($newPassword);
            $this->db->query("UPDATE users SET password = \"{$hashedPass}\" WHERE id = {$id}");
        }

        public function checkPassword($email, $password): array | bool {
            $user = $this->db->findByCustomField("users", "email", $email)->fetchArray();
            if ($user && password_verify($password, $user["password"])){
                return $user;
            }
            return false;
        }
    }

?>