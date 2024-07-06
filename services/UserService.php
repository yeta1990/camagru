<?php

    class UserService {

        private $db;
        private $jwtService;

        public function __construct(){
            $this->db = new DbService();
            $this->jwtService = new JwtService("keyff");
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

        public function getUserByEmail($email){
            return $this->db->findByCustomField("users", "email", $email)->fetchArray();
        }

        public function isUsernameAvailable($username){
            $num_users = $this->db->query("SELECT count(*) as count from users where username = \"{$username}\"")->fetchArray();
            if (strlen($username) > 1 && strlen($username) < 256 && $num_users["count"] == 0){
                return true;
            }
            return false;
        }

        public function isEmailAvailable($email){
            $num_users = $this->db->query("SELECT count(*) as count from users where email = \"{$email}\"")->fetchArray();
            if (strlen($email) > 1 && strlen($email) < 256 && $num_users["count"] == 0){
                return true;
            }
            return false;
        }

        public function isValidPassword($password) {
            $minLength = 8;
            $hasUpperCase = preg_match('/[A-Z]/', $password);
            $hasLowerCase = preg_match('/[a-z]/', $password);
            $hasNumber = preg_match('/\d/', $password);
        
            return strlen($password) >= $minLength && $hasUpperCase && $hasLowerCase && $hasNumber;
        }

        private function validLength($field){
            return strlen($field) > 0 && strlen($field) <= 256;
        }

        public function signUp($email, $username, $password){

            if (!$this->validLength($email) || !$this->validLength($username) || !$this->validLength($password)){
                http_response_code(401);
                echo json_encode(["code" => 401, "message"=>"Invalid length of fields"]);
                exit ;
            }

            if (!$this->isUsernameAvailable($username)){
                http_response_code(401);
                echo json_encode(["code" => 401, "message"=>"Username not available"]);
                exit ;
            }
            if (!$this->isEmailAvailable($email)){
                http_response_code(401);
                echo json_encode(["code" => 401, "message"=>"Email not available"]);
                exit ;
            }
            if (!$this->isValidPassword($password)){
                http_response_code(401);
                echo json_encode(["code" => 401, "message"=>"Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number."]);
                exit ;
            }

            $user = new User($email, $username, $password);
            $user_id = $user->create();

            //to do: catch result and exceptions
            if ($user_id < 1) return 1;
            return $user_id;
        }

        public function update($id, $email, $username){
            if (!$this->validLength($email) || !$this->validLength($username)){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Invalid length of fields"]);
                exit ;
            }
            $user = $this->getUserById($id)->getObjectVars();
            if ($user["username"] != $username && !$this->isUsernameAvailable($username)){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Username not available"]);
                exit ;
            }
            if ($user["email"] != $email && !$this->isEmailAvailable($email)){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Email not available"]);
                exit ;
            }
            $userToUpdate = new User($email, $username);
            $userToUpdate->setId($id);
            $userToUpdate->setConfirmed();
            $userToUpdate->update();
        }

        public function changePassword($id, $newPassword){
            if (!$this->isValidPassword($newPassword)){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number."]);
                exit ;
            }
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

        public function confirmUser($userId){
            $this->db->query("UPDATE users SET confirmed = 1 where id = ". $userId);
        }


        public function recover($email){
            if (!$this->isEmailAvailable($email)){ //if the mail isn't available, that means the user exists...
                $user = $this->getUserByEmail($email);
                $confirmationToken = $this->jwtService->generateConfirmationAccountToken($user["id"]);
                MailService::send($email, $email, 'Recover password',
                    'Change the password for your account in camagru-albgarci: <a href="http://localhost:8080/user/edit/pass?token=' . $confirmationToken . '">Verify</a>'
                );
            }
        }
    }

?>