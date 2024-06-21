<?php
class DbService {

    private $db;

    public function __construct(){
        $this->db = new SQLite3('db.db');
    }

    public function __destruct(){
        $this->db->close();
    }

    public function query($query){
        $this->db->prepare($query)->execute();
    }

    public function insert($object) {
        $query = $this->objectToQuery($object);
        //$this->query($query);
    }

    private function objectToQuery($obj){
        $obj = get_object_vars($this);
        $insert_into = "INSERT INTO users (";
        $values = "VALUES (";
        foreach ($obj as $key => $value) {
            echo $insert_into;
            $insert_into = $insert_into . $key . ',';
            echo strval($value);
            //$values = $values . strval($value) . ',';
        }
        $insert_into = rtrim($insert_into, ',') . ')';
        $values = $values . ')';
        echo $insert_into;
        echo $values;
        return $insert_into . ' ' . $values;
    }
}
?>