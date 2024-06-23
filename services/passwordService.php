<?php
    class PasswordService{

        static public function hash($password){
            return password_hash($password, PASSWORD_BCRYPT);
        }
    }
?>