<?php
    class User{
        private $id;
        private $email;
        private $username;
        private $password;
        private $confirmed;

        public function __construct($id, $email, $username, $password, $confirmed){
            $this->id = $id;
            $this->email = $email;
            $this->username = $username;
            $this->password= $password;
            $this->confirmed = $confirmed;
        }

        public function getAsArray(){
            $obj = get_object_vars($this);
            echo "\n";
            $insert_into = "INSERT INTO users (";
            $values = "VALUES (";
            foreach ($obj as $key => $value) {
                $insert_into = $insert_into . $key . ',';
                $values = $values. $value. ',';

                echo $key . "," . strval($value) . "\n";
            }
            $insert_into = $insert_into . ')';
            $values = $values . ')';
            echo $insert_into;
            echo $values;
            echo "\n";
            return $obj;
            /*return var_dump(get_object_vars($this));*/
        }
    }
?>