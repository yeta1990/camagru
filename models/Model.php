<?php
    abstract class Model{

        private $dbService;
        protected $tableName;
        protected $id;

        public function __construct($tableName){
            $this->dbService = new DbService();
            $this->tableName = $tableName;
            $this->id = -1;
        }

        public function getObjectVars(){
            $vars = get_object_vars($this);
            unset($vars["dbService"]);
            unset($vars["tableName"]);
            unset($vars["password"]);
            if ($vars["id"] == -1){
                unset($vars["id"]);
            }
            return $vars;
        }

        public function print(){
            echo nl2br("\n");
            $vars = $this->getObjectVars();
            unset($vars["dbService"]);

            foreach($vars as $key => $value) {
                echo nl2br("$key: $value\n");
            }
        }

        public function create(){
            /*$this->print();*/
            $result = $this->dbService->insert($this->tableName, $this);
            if ($result){
                $this->setId($result);
            }
            return $result;
        }

        public function update(){
            $result = $this->dbService->update($this->tableName, $this);
        }

        public function setId($id){
            $this->id = $id;
        }

        public function getId(){
            return $this->id;
        }

    }
?>