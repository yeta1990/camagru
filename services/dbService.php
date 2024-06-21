<?php
class DbService {

    private $db;

    public function __construct(){
        $this->db = new SQLite3('db/db.db');
    }

    public function __destruct(){
        $this->db->close();
    }

    public function query($query){
        return $this->db->prepare($query)->execute();
    }

    public function insert($object) {
        $query = $this->objectToQuery($object);
        $this->query($query);
    }

    private function objectToQuery($obj){
        $vars = $obj->getObjectVars();
        $insert_into = "INSERT INTO users (";
        $values = "VALUES (";
        foreach ($vars as $key => $value) {
            $insert_into = $insert_into . $key . ',';
            $values = $values . strval($value) . ',';
        }
        $insert_into = rtrim($insert_into, ',') . ')';
        $values = rtrim($values, ',') . ')';
        echo $insert_into . ' ' . $values;
        return $insert_into . ' ' . $values;
    }
}
?>