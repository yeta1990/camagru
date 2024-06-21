<?php
    class Model{
        public function getObjectVars(){
            return get_object_vars($this);
        }

        public function print(){
            echo nl2br("\n");
            foreach($this->getObjectVars() as $key => $value) {
                echo nl2br("$key: $value\n");
            }
        }

    }
?>