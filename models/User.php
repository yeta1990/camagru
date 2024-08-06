<?php

    class User extends Model{

        protected $email;
        protected $username;
        protected $password;
        protected $confirmed;
        protected $notifications;

        public function __construct($email, $username, $notifications, $password = "",  $confirmed = 0){
            parent::__construct("users");
            $this->email = $email;
            $this->username = $username;
            $this->password= $password;
            $this->confirmed = $confirmed;
            $this->notifications = $notifications;
        }

        public function create(){
            $this->password = PasswordService::hash($this->password);
            return parent::create();
        }

        public function getObjectVars($safe = true){
            return parent::getObjectVars($safe);
        }

        public function setUsername($username){
            $this->username = $username;
        }

        public function setPassword($password){
            $this->password = PasswordService::hash($password, PASSWORD_BCRYPT);
        }

        public function setConfirmed(){
            $this->confirmed = 1;
        }

        public function isConfirmed(){
            return $this->confirmed;
        }

        public function hasNotificationsEnabled(){
            return $this->notifications;
        }

    }
?>