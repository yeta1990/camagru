<?php

    require_once("Model.php");
    class User extends Model{
        protected $email;
        protected $username;
        protected $password;
        protected $confirmed;

        public function __construct($email, $username, $password){
            $this->email = $email;
            $this->username = $username;
            $this->password= $password;
            $this->confirmed = 0;
        }

    }
?>