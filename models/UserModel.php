<?php

    require_once("Model.php");
    class User extends Model{
        
        protected $email;
        protected $username;
        protected $password;
        protected $confirmed;

        public function __construct($email, $username, $password, $confirmed = 0){
            parent::__construct("users");
            $this->email = $email;
            $this->username = $username;
            $this->password= $password;
            $this->confirmed = $confirmed;
        }

        public function create(){
            $this->password = password_hash($this->password, PASSWORD_BCRYPT);
            return parent::create();
        }


    }
?>