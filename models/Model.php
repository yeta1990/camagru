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
            return get_object_vars($this);
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
            $vars = $this->getObjectVars();
            unset($vars["dbService"]);
            unset($vars["tableName"]);
            unset($vars["id"]);
            $result = $this->dbService->insert($this->tableName, $vars);
            echo "last row id" . $this->dbService->lastInsertRowID();
            if ($result){
                $this->setId($this->dbService->lastInsertRowID());;
            }
            return $result;
        }

        public function setId($id){
            $this->id = $id;
        }

    }
?>