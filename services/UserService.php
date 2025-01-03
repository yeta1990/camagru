<?php

    class UserService {

        private $db;
        private $jwtService;

        public function __construct(){
            $this->db = new DbService();
            $this->jwtService = new JwtService(getenv("JWT_PASS"));
        }

        public function getUserById($id){
            $results = $this->db->findById("users", $id)->fetchArray();
            if (!$results || count($results) == 0){
                return null;
            }
            $foundUser = new User($results["email"], $results["username"], $results["notifications"]);
            $foundUser->setId($results["id"]);
            if ($results["confirmed"] == 1) {
                $foundUser->setConfirmed();
            }
            return $foundUser;
        }

        public function getUserByEmail($email){
            return $this->db->findByCustomField("users", "email", $email)->fetchArray();
        }

        public function isUsernameAvailable($username){
            if ($username == "you"){
                return false;
            }
            $query = $this->db->getDb()->prepare("SELECT count(*) as count from users where username = :username;");
            $query->bindValue(':username', $username);
            $num_users = $query->execute()->fetchArray();
            if (strlen($username) > 1 && strlen($username) < 256 && $num_users["count"] == 0){
                return true;
            }
            return false;
        }

        public function isValidEmail($email){
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }


        public function isEmailAvailable($email){
            $query = $this->db->getDb()->prepare("SELECT count(*) as count from users where email = :email;");
            $query->bindValue(':email', $email);
            $num_users = $query->execute()->fetchArray();
            if ($this->isValidEmail($email) && strlen($email) > 1 && strlen($email) < 256 && $num_users["count"] == 0){
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
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Invalid length of fields"]);
                exit ;
            }

            if (!$this->isUsernameAvailable($username)){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Username not available"]);
                exit ;
            }
            if (!$this->isEmailAvailable($email)){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Email not available"]);
                exit ;
            }
            if (!$this->isValidPassword($password)){
                http_response_code(401);
                echo json_encode(["code" => 400, "message"=>"Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number."]);
                exit ;
            }

            $user = new User($email, $username, 1, $password);
            $user_id = $user->create();

            if ($user_id < 1) return 1;
            return $user_id;
        }

        public function update($id, $email, $username){
            if (!$this->validLength($email) || !$this->validLength($username)){
                http_response_code(400);
                echo json_encode(["code" => 400, "message"=>"Invalid length of fields"]);
                exit ;
            }
            $foundUser = $this->getUserById($id);
            if (!$foundUser){
                http_response_code(401);
                echo json_encode(["code" => 401, "message"=>"Bad login"]);
                exit;
            }
            $user = $foundUser->getObjectVars();
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
            $userToUpdate = new User($email, $username, $user["notifications"]);
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
                if ($user["confirmed"] == 1){
                    $jwtRecoverService = new JwtService(getenv("JWT_RECOVER"));
                    $recoveryToken = $jwtRecoverService->generateConfirmationAccountToken($user["id"]);
                    MailService::send($email, $email, 'Recover password',
                        'Change the password for your account in camagru-albgarci: <a href="http://localhost:8080/user/edit/pass?token=' . $recoveryToken . '">Change</a>'
                    );
                }
            }
        }


        public function toggleNotifications($id){
            $user = $this->getUserById($id);
            $newValue = 1;
            if ($user->hasNotificationsEnabled()){
                $newValue = 0;
            }
            $this->db->query("UPDATE users SET notifications = " . $newValue . " where id = ". $id);
        }

        public function sendVerificationEmail($user_data, $user_id){
            $confirmationToken = $this->jwtService->generateConfirmationAccountToken($user_id);
            MailService::send(
                $user_data['email'],
                $user_data['username'],
                'Confirm registration in camagru-albgarci',
                'Verify your account in camagru-albgarci: <a href="http://localhost:8080/api/user/verify?token=' . $confirmationToken . '">Verify</a>'
                );
        }
    }

?>