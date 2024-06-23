<?php

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
            $this->password = PasswordService::hash($this->password);
            return parent::create();
        }

        public function getObjectVars(){
            return parent::getObjectVars();
        }

        public function setUsername($username){
            $this->username = $username;
        }

        public function setPassword($password){
            $this->password = PasswordService::hash($password, PASSWORD_BCRYPT);
        }

        public function setConfirmed($confirmed){
            $this->confirmed = $confirmed;
        }

    }
?>